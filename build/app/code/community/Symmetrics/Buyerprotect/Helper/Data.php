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
    const XML_PATH_TS_BUYERPROTECT = 'buyerprotection/data';
    const XML_PATH_TS_BUYERPROTECT_IS_ACTIVE = 'buyerprotection/data/active';
    const XML_PATH_TS_BUYERPROTECT_TS_ID   = 'buyerprotection/data/trustedshops_id';
    const XML_PATH_TS_BUYERPROTECT_TS_USER = 'buyerprotection/data/trustedshops_user';
    const XML_PATH_TS_BUYERPROTECT_TS_PASSWORD = 'buyerprotection/data/trustedshops_password';
    const XML_PATH_TS_BUYERPROTECT_TS_WSDL_URL = 'buyerprotection/data/trustedshops_url';

    const XML_PATH_TS_AVAILABLE_PAYMENT_CODES = 'trusted_shops_payment_codes';

    const XML_PATH_TS_BUYERPROTECT_ERROR_EMAIL_SENDER = 'buyerprotection/data/trustedshops_erroremail_sender';
    const XML_PATH_TS_BUYERPROTECT_ERROR_EMAIL_TEMPLATE = 'buyerprotection/data/trustedshops_erroremail_template';
    const XML_PATH_TS_BUYERPROTECT_ERROR_EMAIL_RECIPIENT = 'buyerprotection/data/trustedshops_erroremail_recipient';

    const BUYERPROTECT_MODUL_NAME = 'Symmetrics_Buyerprotect';

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
     * if the shop has zero products of this type the function return false
     *
     * @return array|bool
     */
    public function getAllTsProductTypes()
    {
        $allTsProductTypes = array();
        /* @var $productModel Mage_Catalog_Model_Product */
        $productModel = Mage::getModel('catalog/product');
        $productCollection = $productModel->getCollection();
        /* @var $productCollection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */

        $bind = array('eq' => Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT);
        $productCollection->addFieldToFilter('type_id', $bind)
            ->load();

        if ($productCollection->count() <= 0) {
            return false;
        }
        $allTsProductTypes = $productCollection->getAllIds();

        return $allTsProductTypes;
    }

    /**
     * Get all payment codes defined in config.xml. These payment methods works
     * with trustedshops.de.
     *
     * @return array
     * @todo replace Mage::getStoreConfig() with $this->getStoreConfig()
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
     * Gets Trusted Shops User Id
     *
     * @return string
     */
    public function getTsUserId()
    {
        return $this->getStoreConfig(self::XML_PATH_TS_BUYERPROTECT_TS_ID, $this->getStore());
    }

    /**
     * retrive the current config data as array
     *
     * @return array
     */
    public function getConfigData()
    {
        return $this->getStoreConfig(self::XML_PATH_TS_BUYERPROTECT, $this->getStore());
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

    /**
     * Get the correct app model depending on website code
     *
     * @return Mage_Core_Model_App
     */
    public function getMageApp()
    {
        $mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';
        $mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';

        return Mage::app($mageRunCode, $mageRunType);
    }

    /**
     * Get current store id of current website
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getMageApp()->getStore()->getId();
    }

    /**
     * Get correct config values of store of current website
     *
     * @param string $path XML path
     *
     * @return mixed
     */
    public function getStoreConfig($path)
    {
        return $this->getMageApp()->getStore()->getConfig($path);
    }

    /**
     * Checks if TS Buyerprotect is activated in backend
     *
     * @return bool
     */
    public function isBuyerprotectActive()
    {
        return (bool) $this->getStoreConfig(self::XML_PATH_TS_BUYERPROTECT_IS_ACTIVE);
    }

    /**
     * JSON encoded HTML content which should be insert underneath the sku input
     * field.
     *
     * @return string
     */
    public function getTsSkuComment()
    {
        $html = '';
        $content = $this->__("Attention! Change the 'SKU' only when 'Trusted Shops' requests for this!");

        $html = '<p class="note"><span class="ts-sku-note-content"><span>'
              . $content
              . '</span></span></p>';

        return json_encode(array('content' => $html));
    }
}
