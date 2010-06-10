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
 * Default Modul observer
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
class Symmetrics_Buyerprotect_Model_Observer
{
    /**
     * Observer to add the Product to chart
     *
     * @param Varien_Event_Observer $observer current event observer
     *
     * @return void
     */
    public function addProductToCart($observer)
    {
        $frontController = Mage::app()->getFrontController();
        $request = $frontController->getRequest();

        if ($request->getParam('trusted_shops')) {
            /* @var $cart Mage_Checkout_Model_Cart */
            $cart = Mage::getSingleton('checkout/cart')->setStore(Mage::app()->getStore());

            // cart is empty
            if (!($cartProductIds = $cart->getProductIds())) {
                return;
            }

            /* @var $helper Symmetrics_Buyerprotect_Helper_Data */
            $helper = Mage::helper('buyerprotect');
            $tsProductsInCart = $helper->getTsProductsInCart();
            $requestedProductId = $request->getParam('trusted_shops-product');

            // cart is not empty but the only item is a type of
            // Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT
            // and is identical to $requestedProductId
            if ((count($cartProductIds) < 2) && in_array($requestedProductId, $cartProductIds)) {
                return;
            }

            /**
             * Get rid off all previous added products of
             * Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT.
             * This way it get sure that only one item of this product type is in cart.
             */
            if ($tsProductsInCart) {
                foreach ($tsProductsInCart as $cartItemId => $tsProductId) {
                    $cart->removeItem($cartItemId);
                }
            }

            // add Buyerprotection Product to cart
            $cart->addProductsByIds(array($requestedProductId));
            $cart->save();

//            $tsSoapData = array(
//                'return_value' => '999',
//                'ts_id' => '1',
//                'ts_product_id' => '',
//                'amount' => '89',
//                'currency' => 'EUR',
//                'payment_type' => 'Käuferschutz',
//                'buyer_email' => 'kaeufer@nhdoan.de',
//                'shop_customer_id' => '123',
//                'shop_order_id' => '456789000',
//                'order_date' => 'heute',
//                'ws_user' => 'ngoc',
//                'ws_password' => '123456'
//            );
//
//            $tsBP = new Symmetrics_Buyerprotect_Model_Buyerprotection();
//            $tsBP->sendTsEmailOnSoapFail($tsSoapData);
        }
    }

    /**
     * Observer to add the Product to chart
     *
     * @param Varien_Event_Observer $observer current event observer
     *
     * @todo check if product is alrady in cart
     * @todo check if other buyerprotection Product is alerady in cart
     *
     * @return null
     */
    public function checkoutOrderSaveAfter($observer)
    {
        $order = $observer->getEvent()->getOrder();
    }

    /**
     * Observer to prevent calaogrules to the product type
     *
     * @param Varien_Event_Observer $observer current event observer
     *
     * @todo implement code
     *
     * @return null
     */
    public function catalogruleAfterApply($observer)
    {
        $event = $observer->getEvent();

        $currentCatalogRoule = Mage::getSingleton('catalogrule/rule');
        /* @var $currentCatalogRoule Mage_CatalogRule_Model_Rule */

        $machedProducts = $currentCatalogRoule->getMatchingProductIds();
        $collection = $currentCatalogRoule->getCollection();

    }
    
    /**
     * To test SOAP API
     *
     * @param Varien_Event_Observer $observer Varien observer object
     *
     * @return void
     */
    public function checkoutOnepageSaveOrderAfter($observer)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();
        /* @var $payment Mage_Sales_Model_Order_Payment */
        $payment = $order->getPayment();

        $tsSoapDataObject = Mage::getModel('buyerprotect/service_soap_data');
        $tsSoapDataObject->init(169, $order);
        $tsSoapData = $tsSoapDataObject->getTsSoapData();
        $ts = $tsSoapDataObject;

//        $soapClient = new SoapClient($tsSoapDataObject->getWsdlUrl());
//        $returnValue = $soapClient->requestForProtection(
//            $ts->getTsId(), $ts->getProductId(), $ts->getAmount(),
//            $ts->getCurrency(), $ts->getPaymentType(), $ts->getBuyerEmail(),
//            $ts->getShopCustomerId(), $ts->getShopOrderId(), $ts->getOrderDate(),
//            $ts->getWsUser(), $ts->getWsPassword()
//        );

//        Symmetrics_Buyerprotect_Model_Buyerprotection::sendTsEmailOnSoapFail($ts->getData());

    }

    /**
     * Observer to prevent calaogrules to the product type
     *
     * @param Varien_Event_Observer $observer current event observer
     *
     * @todo implement code
     *
     * @return null
     */
    public function catalogruleBeforeApply($observer)
    {
        $event = $observer->getEvent();

    }
    
    /**
     * Observer to prevent discount rules to the product type
     *
     * @param Varien_Event_Observer $observer current event observer
     *
     * @todo implement code
     *
     * @return null
     */
    public function quoteCalculateDiscountItem($observer)
    {
        $event = $observer->getEvent();
        
        $item = $event->getItem();
        /* @var $item Mage_Sales_Model_Quote_Item */
    }
    
    /**
     * OBserver to set the product to dont manage the stock and set min and max sale
     * qty to one
     *
     * @param Varien_Event_Observer $observer Varien observer object
     *
     * @return void
     */
    public function saveInventoryData($observer)
    {
        $product = $observer->getEvent()->getProduct();
        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = $product->getStockItem();

        $stockItem->setUseConfigMaxSaleQty('0');
        $stockItem->setUseConfigMinSaleQty('0');
        $stockItem->setUseConfigManageStock('0');
        $stockItem->setManageStock(0);
        $stockItem->setMinSaleQty(1);
        $stockItem->setMaxSaleQty(1);
        $stockItem->save();
    }

}
