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
 * Default helper class
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
class Symmetrics_Buyerprotect_Helper_Data
    extends Mage_Core_Helper_Abstract
{
    /*
     * Some store config paths
     */
    const XML_PATH_TS_BUYERPROTECT_IS_ACTIVE = 'buyerprotection/data/soapcheck_active';
    const XML_PATH_TS_BUYERPROTECT_TS_ID   = 'buyerprotection/data/trustedshops_id';
    const XML_PATH_TS_BUYERPROTECT_TS_USER = 'buyerprotection/data/trustedshops_user';
    const XML_PATH_TS_BUYERPROTECT_TS_PASSWORD = 'buyerprotection/data/trustedshops_password';
    const XML_PATH_TS_BUYERPROTECT_TS_WSDL_URL = 'buyerprotection/data/trustedshops_url';

    const XML_PATH_TS_AVAILABLE_PAYMENT_CODES = 'trusted_shops_payment_codes';

    const XML_PATH_TS_BUYERPROTECT_ERROR_EMAIL_SENDER = 'buyerprotection/data/trustedshops_erroremail_sender';
    const XML_PATH_TS_BUYERPROTECT_ERROR_EMAIL_TEMPLATE = 'buyerprotection/data/trustedshops_erroremail_template';
    const XML_PATH_TS_BUYERPROTECT_ERROR_EMAIL_RECIPIENT = 'buyerprotection/data/trustedshops_erroremail_recipient';

    /**
     * get all buyerprotection Products in cart
     *
     * @return array
     */
    public function getTsProductsInCart()
    {
        $tsProductIds = array();
        
        /* @var $cart Mage_Checkout_Model_Cart */
        $cart = Mage::getSingleton('checkout/cart')
            ->setStore(Mage::app()->getStore());

        /* @var $cartItems Mage_Sales_Model_Mysql4_Quote_Item_Collection */
        $cartItems = $cart->getItems();
        /* @var $tsIdsSelect Varien_Db_Select */
        $tsIdsSelect = clone $cartItems->getSelect();
        
        $tsIdsSelect->where('product_type = ?', Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT);
//        $tsIdsSelect->columns('main_table.' . $cartItems->getResource()->getIdFieldName());

        $items = $cartItems->getConnection()->fetchCol($tsIdsSelect);

        if ($items) {
            foreach ($items as $item) {
                $tsProductIds[$item] = $cartItems->getItemById($item)->getProductId();
            }
        }

        return $tsProductIds;
    }

    /**
     * Gets all products of type
     * Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT.
     *
     * @return array
     */
    public function getAllTsProductTypes()
    {
        $allTsProductTypes = array();
        /* @var $productModel Mage_Catalog_Model_Product */
        $productModel = Mage::getModel('catalog/product');
        /* @var $productCollection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
        $productCollection = $productModel->getCollection();

        $bind = array('eq' => Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT);
        $productCollection->addFieldToFilter('type_id', $bind)->load();
        $allTsProductTypes = $productCollection->getAllIds();

        return $allTsProductTypes;
    }

    /**
     * Get all payment codes defined in config.xml. These payment methods works
     * with trustedshops.de.
     *
     * @return array
     */
    public function getAvailableTsPaymentCodes()
    {
        return Mage::getStoreConfig(self::XML_PATH_TS_AVAILABLE_PAYMENT_CODES);
    }

    /**
     * Get store object.
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * Get ISO 3 letter currency code of current store
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->getCurrencyCode();
    }

    /**
     * Get ISO 3 letter currency code of current store
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->getStore()->getCurrentCurrency()->getCode();
    }

    /**
     * Returns an array of required config paths for trustedshops.de SOAP Api
     *
     * @return array
     */
    public function getTsStoreConfigPaths()
    {
        $tsStoreConfigPaths = array(
            'is_active' => self::XML_PATH_TS_BUYERPROTECT_IS_ACTIVE,
            'ts_id' => self::XML_PATH_TS_BUYERPROTECT_TS_ID,
            'ws_user' => self::XML_PATH_TS_BUYERPROTECT_TS_USER,
            'ws_password' => self::XML_PATH_TS_BUYERPROTECT_TS_PASSWORD,
            'wsdl_url' => self::XML_PATH_TS_BUYERPROTECT_TS_WSDL_URL,
            'trusted_shops_payment_codes' => self::XML_PATH_TS_AVAILABLE_PAYMENT_CODES
        );

        return $tsStoreConfigPaths;
    }

    /**
     * Determine if any TS products is in cart.
     *
     * @return bool
     */
    public function hasTsProductsInCart()
    {
        return ($this->getTsProductsInCart()) ? true : false;
    }

    /**
     * Gets product model from registry
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * Get all attribute groups with name and partially html id
     *
     * @param bool $toJson returns a JSON object on true
     *
     * @return array|json
     */
    public function getAttributeGroups($toJson = false)
    {
        $product = $this->getProduct();
        $setId = $product->getAttributeSetId();
        $return = array();

        $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->setAttributeSetFilter($setId)
            ->load();

        foreach ($groupCollection as $group) {
            $attributes = $product->getAttributes($group->getId(), true);

            foreach ($attributes as $key => $attribute) {
                if (!$attribute->getIsVisible()) {
                    unset($attributes[$key]);
                }
            }

            if (count($attributes) == 0) {
                continue;
            }

            $return[$group->getAttributeGroupName()] = 'group_' . $group->getId();
        }

        return $toJson ? json_encode($return) : $return;
    }
}
