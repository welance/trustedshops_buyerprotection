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
 * @author    Ngoc Anh Doan <nd@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Symmetrics_Buyerprotect_Model_Service_Soap_Data contains data for SOAP API
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Ngoc Anh Doan <nd@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Model_Service_Soap_Data extends Varien_Object
{
    /**
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order = null;

    /**
     * Initialize Data
     *
     * @param int                    $tsProductId TS product id
     * @param Mage_Sales_Model_Order $order       Sales order object
     *
     * @return Symmetrics_Buyerprotect_Model_Service_Soap_Data
     */
    public function init($tsProductId, Mage_Sales_Model_Order $order)
    {
        $this->setTsProductId($tsProductId);
        $this->_order = $order;

        $this->_initTsSoapData();

        return $this;
    }

    /**
     * Keys of of data:
     *
     * returnValue: return_value
     * tsId: ts_id
     * tsProductId: ts_product_id
     * amount: amount
     * currency: currency
     * paymentType: payment_type
     * buyerEmail: buyer_email
     * shopCustomerID: shop_customer_id
     * shopOrderID: shop_order_id
     * orderDate: order_date
     * wsUser: ws_user
     * wsPassword: ws_password
     *
     * @return void
     * @throw Symmetrics_Buyerprotect_Model_Service_Soap_Data_Exception
     */
    protected function _initTsSoapData()
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $this->_order;
        /* @var $payment Mage_Sales_Model_Order_Payment */
        $payment = $order->getPayment();
        /* @var $helper Symmetrics_Buyerprotect_Helper_Data */
        $helper = $this->getHelper();

        $tsStoreConfigPaths = $helper->getTsStoreConfigPaths();
        $paymentCode = $payment->getMethod();
        $availablePaymentCodes = $helper->getAvailableTsPaymentCodes();
        $allTsProductTypes = $helper->getAllTsProductTypes();

        $this->setAvailablePaymentCodes($availablePaymentCodes);

        if (!array_key_exists($paymentCode, $availablePaymentCodes)) {
            throw Mage::exception(get_class($this), "'$paymentCode' is not a supported payment by Trusted Shops!");
        }

        if (!in_array($this->getTsProductId(), $allTsProductTypes)) {
            throw Mage::exception(get_class($this), "{$this->getTsProductId()} is not a valid TS product type!");
        }

        $tsSoapData = array(
            'is_active' => Mage::getStoreConfig($tsStoreConfigPaths['is_active']),
            'ts_id' => Mage::getStoreConfig($tsStoreConfigPaths['ts_id']),
            'ws_user' => Mage::getStoreConfig($tsStoreConfigPaths['ws_user']),
            'ws_password' => Mage::getStoreConfig($tsStoreConfigPaths['ws_password']),
            'wsdl_url' => Mage::getStoreConfig($tsStoreConfigPaths['wsdl_url']),
            'buyer_email' => $order->getCustomerEmail(),
            'amount' => $order->getGrandTotal(),
            'shop_order_id' => $order->getRealOrderId(),
            'order_date' => $this->getTsOrderDate($order->getCreatedAt()),
            'payment_type' => $this->getPaymentMethodByCode($paymentCode),
            'ts_product_id' => $this->getTsProductId(),
            'currency' => $helper->getCurrencyCode(),
            'shop_customer_id' => $order->getCustomerId()
        );

        $this->setData($tsSoapData);

        return;
    }

    /**
     * Returns an TS array whith formated keys.
     * 11 required fields for TS SOAP service. Note the format of the keys.
     *
     * @see self::_initTsSoapData()
     * @return array
     */
    public function getTsSoapData()
    {
        if (!($formatedTsSoapData = $this->getFormatedTsSoapData())) {
            $formatedTsSoapData = array(
                'tsId' => $this->getTsId(),
                'tsProductId' => $this->getTsProductId(),
                'amount' => $this->getAmount(),
                'currency' => $this->getCurrency(),
                'paymentType' => $this->getPaymentType(),
                'buyerEmail' => $this->getBuyerEmail(),
                'shopCustomerID' => $this->getShopCustomerId(),
                'shopOrderID' => $this->getShopOrderId(),
                'oderDate' => $this->getOrderDate(),
                'wsUser' => $this->getWsUser(),
                'wsPasssword' => $this->getWsPassword()
            );

            $this->setFormatedTsSoapData($formatedTsSoapData);
        }

        return $formatedTsSoapData;
    }

    /**
     * Returns a TS formated date time:
     *
     * 2010-10-10T10:10:10
     *
     * @param string $dateTime Order date
     *
     * @return string
     */
    public function getTsOrderDate($dateTime)
    {
        return str_replace(' ', 'T', $dateTime);
    }

    /**
     * Returns the payment method
     *
     * @param string $paymentCode Payment code
     * 
     * @return string
     */
    public function getPaymentMethodByCode($paymentCode)
    {
        if (!($availablePaymentCodes = $this->getAvailablePaymentCodes())) {
            $availablePaymentCodes = $this->getHelper()->getAvailableTsPaymentCodes();
        }

        return $availablePaymentCodes[$paymentCode];
    }

    /**
     * Returns helper object
     *
     * @return Symmetrics_Buyerprotect_Helper_Data
     */
    public function getHelper()
    {
        if (!($helper = $this->getData('helper'))) {
            $helper = Mage::helper('buyerprotect');

            $this->setHelper($helper);
        }

        return $helper;
    }

    /**
     * URL of SOAP server
     *
     * @return string
     */
    public function getWsdlUrl()
    {
        return $this->getData('wsdl_url');
    }

    /**
     * Check service is active
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->getIsActive();
    }
}
