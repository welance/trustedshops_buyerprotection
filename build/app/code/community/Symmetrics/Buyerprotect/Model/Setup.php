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
 * Setup class for Symmetrics_Buyerprotect
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Ngoc Anh Doan <nd@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Model_Setup extends Mage_Catalog_Model_Resource_Eav_Mysql4_Setup
{
    /**
     * Default ignored attribute codes
     *
     * @var array
     */
    protected $_ignoredAttributeCodes = array('entity_id', 'attribute_set_id', 'entity_type_id');

    /**
     * Default ignored attribute types
     *
     * @var array
     */
    protected $_ignoredAttributeTypes = array();
    
    /*
     * Some constants for migration skripts.
     */
    const MIGRATION_TEMPLATE_PATH = '/app/locale/de_DE/migrations/';
    const MIGRATION_TEMPLATE_SUFFIX = '.html';
    
    /**
     * Creates a new email template
     *
     * @param string $templateCode code/identifier of template
     * @param array  $content      index of 'template_subject' and 'template_text' are needed
     * @param int    $templateType 1|2 text|html email
     *
     * @return bool
     */
    public function createEmailTemplate($templateCode, array $content, $templateType = 2)
    {
        if (!isset ($content['template_subject']) || !isset ($content['template_text'])) {
            return false;
        }

        /* @var $emailTemplate Mage_Core_Model_Email_Template */
        $emailTemplate = Mage::getModel('core/email_template');

        // Check if template exists
        if ($emailTemplate->loadByCode($templateCode)->hasData()) {
            return false;
        }

        $emailTemplate->setTemplateCode($templateCode)
            ->setTemplateSubject($content['template_subject'])
            ->setTemplateText($content['template_text'])
            ->setTemplateType($templateType)
            ->setAddedAt(new Zend_Db_Expr('Now()'))
            ->setModifiedAt(new Zend_Db_Expr('Now()'))
            ->save();

        return true;
    }

    /**
     * crate a buyerprotect Product from given data
     *
     * @param string $sku         sku for new product
     * @param array  $productData Required indexes:
     *                            $productData['price'] (pre-tax)
     *                            $productData['name']
     *                            $productData['description']
     *                            $productData['short_description']
     *
     * @return integer|array
     */
    public function createBuyerprotectProduct($sku, $productData)
    {
        $defaultSetId = $this->getDefaultAttributeSetId('catalog_product');
        $productModel = Mage::getModel('catalog/product');
        /* @var $productModel Mage_Catalog_Model_Product */

        $productModel->setStoreId(0)
            ->setAttributeSetId($defaultSetId)
            ->setTypeId('buyerprotect')
            ->setSku($sku);
        
        foreach ($productModel->getTypeInstance(true)->getEditableAttributes($productModel) as $attribute) {
            $_attrCode = $attribute->getAttributeCode();
            if ($this->_isAllowedAttribute($attribute) && isset($productData[$_attrCode])) {
                $productModel->setData(
                    $attribute->getAttributeCode(),
                    $productData[$_attrCode]
                );
            }
        }
        
        $errors = $productModel->validate();
        if (is_array($errors)) {
            return $errors;
        }

        try {
            $productModel->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        /**************
         * stock part *
         **************/

        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = Mage::getModel('cataloginventory/stock_item');

        $stockItem->loadByProduct($productModel);

        // note: the product id has to be set
        $stockItem->setProductId($productModel->getId());
        $stockItem->setIsInStock(1);
        $stockItem->setMinSaleQty(1);
        $stockItem->setMaxSaleQty(1);

        $stockItem->save();
        
        return;
    }

    /**
     * Check is attribute allowed
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute  attribute to check
     * @param array                                    $attributes array of attributes
     *
     * @return boolean
     */
    protected function _isAllowedAttribute($attribute, $attributes = null)
    {
        if (is_array($attributes)
            && !( in_array($attribute->getAttributeCode(), $attributes)
                  || in_array($attribute->getAttributeId(), $attributes))) {
            return false;
        }

        return !in_array($attribute->getFrontendInput(), $this->_ignoredAttributeTypes)
               && !in_array($attribute->getAttributeCode(), $this->_ignoredAttributeCodes);
    }
}