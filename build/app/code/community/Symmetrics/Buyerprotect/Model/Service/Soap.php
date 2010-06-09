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
 * Buyer Protection Soap Interface
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
class Symmetrics_Buyerprotect_Model_Service_Soap
{
    /**
     * make a protection request to the TrustedShops Soap Api
     *
     * @param Mage_Sales_Model_Order $order order to make a Reqest from
     *
     * @return null
     */
    public function requestForProtection(Mage_Sales_Model_Order $order)
    {
        $orderItemsCollection = clone $order->getItemsCollection();
        /* @var $orderItemsCollection Mage_Sales_Model_Mysql4_Order_Item_Collection */
        $orderItemsCollection->resetData();
        $orderItemsCollection->clear();
        $orderItemsCollection->addFieldToFilter('product_type', array('eq' => 'buyerprotect'));

        $orderItemsCollection->load();

        if ($orderItemsCollection->count() >= 1) {
            $firstItem = $orderItemsCollection->getFirstItem();
            /* @var $tsSoapDataObject Symmetrics_Buyerprotect_Model_Service_Soap_Data */
            $tsSoapDataObject = Mage::getModel('buyerprotect/service_soap_data', array($firstItem, $order));

            $tsSoapData = $tsSoapDataObject->getTsSoapData();
            /** @todo make Soap Call */
        }
    }
}