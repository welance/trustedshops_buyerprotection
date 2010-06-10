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
        }

        return;
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
     * Request for buyer protection service of Trusted Shops if the corresponding
     * product is in cart.
     *
     * @param Varien_Event_Observer $observer Varien observer object
     *
     * @return void
     */
    public function checkoutOnepageSaveOrderAfter($observer)
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
            die;
        }

        return;
    }

    /**
     * Method to test cart discount
     *
     * @param Varien_Event_Server $observer Varien observer object
     *
     * @return void
     */
    public function dump($observer)
    {
//        var_dump($observer->getItem());
//        die;
        return;
    }
}
