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
 * Soruce Model to get the WSDL API for Trusted Shops protectionservice 
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    Symmetrics GmbH <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Model_System_Config_Source_Wsdl
{
    /**
     * returns the values for the 'server' field in backend
     *
     * @return array
     */
    public function toOptionArray()
    {
        $wsdlUri = 'ts/protectionservices/ApplicationRequestService?wsdl';
        $returnArray = array(
            /* Only for development */
            array(
                'value' => 'https://protection-qa.trustedshops.com/' . $wsdlUri,
                'label' => Mage::helper('buyerprotect')->__('Test')
            ),

            array(
                'value' => 'https://protection.trustedshops.com/ts/' . $wsdlUri,
                'label' => Mage::helper('buyerprotect')->__('Live')
            ),
        );

        return $returnArray;
    }
}
