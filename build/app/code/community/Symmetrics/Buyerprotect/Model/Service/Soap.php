<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
 
/**
 * Buyer Protection Soap Interface
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @author    Ngoc Anh Doan <nd@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Model_Service_Soap
{
    /**
     * Constant to define $_soapRequestErrorCode on exception
     *
     * @todo in v2 0 is used by Trusted Shops
     */
    const TS_SOAP_EXCEPTION_CODE = 0;

    /**
     * Do not set positive values on error!
     * The Trusted Shop API returns a positive value on success.
     *
     * @var int|null
     */
    protected $_soapRequestErrorCode = null;

    /**
     * SOAP request to Trusted Shops, a positive $errorCode determines a successful
     * request.
     *
     * @param Symmetrics_Buyerprotect_Model_Service_Soap_Data $ts SOAP data object
     *
     * @return void
     */
    protected function _request(Symmetrics_Buyerprotect_Model_Service_Soap_Data $ts)
    {
        $soapClient = new SoapClient($ts->getWsdlUrl());

        $this->_soapRequestErrorCode = $soapClient->requestForProtection(
            $ts->getTsId(),
            $ts->getTsProductId(),
            $ts->getAmount(),
            $ts->getCurrency(),
            $ts->getPaymentType(),
            $ts->getBuyerEmail(),
            $ts->getShopCustomerId(),
            $ts->getShopOrderId(),
            $ts->getOrderDate(),
            $ts->getWsUser(),
            $ts->getWsPassword()
        );

        return;
    }

    /**
     * make a protection request to the TrustedShops Soap Api
     *
     * @param Mage_Sales_Model_Order $order order to make a Reqest from
     *
     * @return Symmetrics_Buyerprotect_Model_Service_Soap_Data
     * @throw Symmetrics_Buyerprotect_Model_Service_Soap_Exception
     * @todo do some logging
     */
    public function requestForProtection(Mage_Sales_Model_Order $order)
    {
        $orderItemsCollection = clone $order->getItemsCollection();
        /* @var $orderItemsCollection Mage_Sales_Model_Mysql4_Order_Item_Collection */

        $orderItemsCollection->addFieldToFilter('product_type', array('eq' => 'buyerprotect'));

        // Varien_Data_Collection::count() will do the load!
        if ($orderItemsCollection->count() >= 1) {
            $tsItem = null;
            
            // determine TS product type
            foreach ($orderItemsCollection->getItems() as $item) {
                if ($item->getProductType() == Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT) {
                    $tsItem = $item;
                }
            }

            if (!$tsItem) {
                throw Mage::exception(get_class($this), "$tsItem is empty!");
            }

            /* @var $tsSoapDataObject Symmetrics_Buyerprotect_Model_Service_Soap_Data */
            $tsSoapDataObject = Mage::getModel('buyerprotect/service_soap_data');

            $tsSoapDataObject->init($order, $tsItem);

            if ($tsSoapDataObject->isActive()) {
                try {
                    $this->_request($tsSoapDataObject);
                } catch (SoapFault $soapFault) {
                    $this->_soapRequestErrorCode = self::TS_SOAP_EXCEPTION_CODE;
                    Mage::logException($soapFault);
                }

                /*
                 * Request wasn't successful
                 */
                if (!($this->_soapRequestErrorCode > self::TS_SOAP_EXCEPTION_CODE)) {
                    $tsSoapDataObject->setIsSuccessfull(false);
                    $tsSoapDataObject->setSoapRequestErrorCode($this->_soapRequestErrorCode);
                    $tsSoapDataObject->setTsBuyerProtectRequestId(false);
                    Mage::log('send email');
                    $tsSoapDataObject->setReturnValue($this->_soapRequestErrorCode);
                    Symmetrics_Buyerprotect_Model_Buyerprotection::sendTsEmailOnSoapFail($tsSoapDataObject->getData());
                } else {
                    $tsSoapDataObject->setIsSuccessfull(true);
                    $tsSoapDataObject->setSoapRequestErrorCode(false);
                    $tsSoapDataObject->setTsBuyerProtectRequestId($this->_soapRequestErrorCode);
                    Mage::log('id: ' . $this->_soapRequestErrorCode);
                }
            }

            return $tsSoapDataObject;
        }
    }
}
