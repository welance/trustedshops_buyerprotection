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
 * @copyright 2010 Symmetrics Gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Block class to render the beadge on the right sidebar
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010 Symmetrics Gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Block_Badge extends Mage_Core_Block_Template
{

    /**
     * Check if Buyerprotection is enabled, has a valid user id and TS logo/image
     * exists.
     *
     * @return boolean
     */
    protected function isValid()
    {
        $helper = Mage::helper('buyerprotect');
        /* @var $helper Symmetrics_Buyerprotect_Helper_Data */
        $configData = $helper->getConfigData();

        if (!isset($configData['ts_logo_img'], $configData['ts_background_img'])) {
            return false;
        }

        if (strlen($configData['trustedshops_id']) == 33
            && substr($configData['trustedshops_id'], 0, 1) == 'X'
            && $configData['trustedshops_user'] != ''
            && !empty($configData['ts_logo_img'])
            && !empty($configData['ts_background_img'])) {
            return true;
        }

        return false;
    }

    /**
     * Returns the seal code with adapted design and paths for magento
     *
     * @return string
     */
    public function getSeal()
    {
        $helper = Mage::helper('buyerprotect');
        /* @var $helper Symmetrics_Buyerprotect_Helper_Data */
        
        $configData = $helper->getConfigData();
        
        $seal = $configData['trustedshops_certificate_logo_code'];

        $new = 'class="box" ';
        $pos = 'id="tsBox"';
        $seal = str_replace($pos, $new . $pos, $seal);

        $logo = Mage::getBaseUrl('media') . '/trustedshops/' . $configData['ts_logo_img'];
        $seal = preg_replace(
            '!(\<img style\=\"(?:.*)\" src\=\")(?:.*\/trustedshops.*\..{3,4})\"!imsU',
            '$1' . $logo . '"',
            $seal
        );

        $search = array();
        $search[] = '/images\/bg_yellow.jpg/i';
        $search[] = '/width:[0-9]+(?:\.[0-9]*)?px/i';
        $search[] = '/border:1px solid #C0C0C0/i';
        $search[] = '/padding:2px/i';

        $repleace = array();
        $repleace[] = Mage::getBaseUrl('media') . '/trustedshops/' . $configData['ts_background_img'];
        $repleace[] = 'width:100%';
        $repleace[] = 'border:0px';
        $repleace[] = 'padding:0px';
        $seal = preg_replace($search, $repleace, $seal);

        return $seal;
    }

}
