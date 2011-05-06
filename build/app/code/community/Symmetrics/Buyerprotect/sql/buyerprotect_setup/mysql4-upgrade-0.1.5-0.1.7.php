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

/* @var $this Symmetrics_Buyerprotect_Model_Setup */

Mage::App()->setUpdateMode(false);
Mage::App()->setCurrentStore(Mage_Core_Model_App::DISTRO_STORE_ID);

$tsProductsIds = Symmetrics_Buyerprotect_Model_Type_Buyerprotect::getAllTsProductIds();
$tsProductsData = array();
$preTaxValue = 1.19;
$currency = new Zend_Currency('de_DE');

foreach ($tsProductsIds as $tsProduct) {
    $tsProductsData['price'] = (double) $tsProduct->net * (double) $preTaxValue;
    preg_match('/^TS080501_([0-9]*)_.*/', $tsProduct->id, $matches);
    $tsProductName = "Käuferschutz bis $matches[1] €";
    $tsProductsData['name'] = $tsProductName;
    $tsProductsData['description'] = $tsProductName . $currency->toCurrency($tsProductsData['price']);
    $tsProductsData['short_description'] = $tsProductName . $currency->toCurrency($tsProductsData['price']);

    $this->createBuyerprotectProduct($tsProduct->id, $tsProductsData);
}
Mage::App()->setUpdateMode(true);
