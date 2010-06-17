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
     * Checking if products of
     * Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT
     * were added and get sure only of that type is in cart.
     *
     * @param Varien_Event_Observer $observer current event observer
     *
     * @return void
     */
    public function addProductToCart($observer)
    {
        $frontController = Mage::app()->getFrontController();
        $request = $frontController->getRequest();
        /* @var $cart Mage_Checkout_Model_Cart */
        $cart = Mage::getSingleton('checkout/cart')->setStore(Mage::app()->getStore());
        /* @var $helper Symmetrics_Buyerprotect_Helper_Data */
        $helper = Mage::helper('buyerprotect');
        $tsProductsInCart = $helper->getTsProductsInCart();

        if ($request->getParam('trusted_shops')) {

            // cart is empty
            if (!($cartProductIds = $cart->getProductIds())) {
                return;
            }

            $requestedProductId = $request->getParam('trusted_shops-product');

           /**
            * cart is not empty but the only item is a type of
            * Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT
            * and is identical to $requestedProductId
            */
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
        } else {
            if ($tsProductsInCart) {
                foreach ($tsProductsInCart as $cartItemId => $tsProductId) {
                    $cart->removeItem($cartItemId);
                }
            }
        }

        return;
    }

    /**
     * Request for buyer protection service of Trusted Shops if the corresponding
     * product is in cart.
     *
     * @param Varien_Event_Observer $observer Varien observer object
     *
     * @return void
     */
    public function requestTsProtection($observer)
    {
        /* @var $helper Symmetrics_Buyerprotect_Helper_Data */
        $helper = Mage::helper('buyerprotect');

        if ($helper->hasTsProductsInCart()) {
            /* @var $order Mage_Sales_Model_Order */
            $order = $observer->getEvent()->getOrder();
            /* @var $tsSoap Symmetrics_Buyerprotect_Model_Service_Soap */
            $tsSoap = Mage::getModel('buyerprotect/service_soap');

            Mage::log('start SOAP request');
            $tsSoap->requestForProtection($order);
            Mage::log('end SOAP request');
        }

        return;
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

        /* @var $item Mage_Sales_Model_Quote_Item */
        $item = $event->getItem();
        if ($item->getProductType() == Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT) {
            $result = $event->getResult();
            $result->setDiscountAmount(0);
            $result->setBaseDiscountAmount(0);
        }
    }
    
    /**
     * Observer to check correct values of stock table 'cataloginventory_stock_item'
     * for product type Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT
     *
     * @param Varien_Event_Observer $observer Varien observer object
     *
     * @return void
     */
    public function hookIntoCataloginventoryStockItemSaveAfter($observer)
    {
        $stockItem = $observer->getEvent()->getItem();
        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */

        if (!$stockItem) {
            return;
        }

        $productTypeIdentifier = $stockItem->getProductTypeId();

        if ($productTypeIdentifier == Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT) {
            Symmetrics_Buyerprotect_Model_Type_Buyerprotect::checkStockItem($stockItem);
        }

        return;
    }

}
