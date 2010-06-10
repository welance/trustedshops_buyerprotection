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
 * Buyerprotection Product Type Class
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerportect
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Model_Type_Buyerprotect extends Mage_Catalog_Model_Product_Type_Abstract
{
    /**
     * Constant to get protuct type
     */
    const TYPE_BUYERPROTECT = 'buyerprotect';

    /**
     * Trusted Shops product ids and clear prices.
     * Note: product ids and prices may change in future! The informations were
     * taken from the implementation handbook (Integrationshandbuch) v3.01.
     *
     * @todo implement for this array a static getter method
     *
     * @var array
     */
    public static $tsProductIds = array(
        'TS080501_500_30_EUR' => 0.82,
        'TS080501_1500_30_EUR' => 2.47,
        'TS080501_2500_30_EUR' => 4.12,
        'TS080501_5000_30_EUR' => 8.24,
        'TS080501_10000_30_EUR' => 16.47,
        'TS080501_20000_30_EUR' => 32.94
    );

    /**
     * Check if Product ist virtual, this always returns true
     *
     * @param Mage_Catalog_Model_Product $product product type instance
     *
     * @return boolean
     */
    public function isVirtual($product = null)
    {
        // return True if this product is virtual and false if this product isn't virtual product
        return true;
    }
}
