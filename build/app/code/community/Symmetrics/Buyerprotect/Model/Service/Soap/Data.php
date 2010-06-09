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
     * Constructor
     *
     * @param string                 $tsProductId Trusted Shops product id
     * @param Mage_Sales_Model_Order $orderObject Sales order object
     */
    function __construct($tsProductId, Mage_Sales_Model_Order $orderObject)
    {
        $this->_order = $orderObject;
        $this->setTsProductId($tsProductId);

        parent::__construct();
    }

    /**
     * Inits SOAP data
     *
     * @return void
     */
    protected function _construct()
    {
        $helper = Mage::getModel('buyerprotect');
        $this->setHelper($helper);

        $this->_initTsSoapData();

        return;
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
        $order = $this->_order;
        /* @var $helper Symmetrics_Buyerprotect_Helper_Data */
        $helper = $this->getHelper();

        $tsStoreConfigPaths = $helper->getTsStoreConfigPaths();
        $paymentCode = $order->getPayment()->getMethod();
        $availablePaymentCodes = $helper->getAvailableTsPaymentCodes();
        $allTsProductTypes = $helper->getAllTsProductTypes();
        $currencyCode = $helper->getCurrencyCode();

        if (!in_array($paymentCode, $availablePaymentCodes)) {
            throw Mage::exception($this, "$paymentCode is not a supported payment by Trusted Shops!");
        }

        if (!in_array($tsProductId, $allTsProductTypes)) {
            throw Mage::exception($this, "$tsProductId is not a valid TS product type!");
        }

        $tsSoapData = array(
            'is_active' => Mage::getStoreConfig($tsStoreConfigPaths['is_active']),
            'ts_id' => Mage::getStoreConfig($tsStoreConfigPaths['ts_id']),
            'ws_user' => Mage::getStoreConfig($tsStoreConfigPaths['ws_user']),
            'ws_password' => Mage::getStoreConfig($tsStoreConfigPaths['ws_password']),
            'wsdl_url' => Mage::getStoreConfig($tsStoreConfigPaths['wsdl_url']),
            'buyer_email' => $order->getCustomerEmail(),
            'amount' => (double) $order->getGrandTotal(),
            'shop_order_id' => $order->getRealOrderId(),
            'order_date' => str_replace(' ', 'T', $order->getCreatedAt()),
            'payment_type' => $paymentMethod,
            'ts_product_id' => $this->getTsProductId(),
            'currency' => $currencyCode,
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
}
