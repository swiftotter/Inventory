<?php
/**
 * SwiftOtter_Base is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SwiftOtter_Base is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with SwiftOtter_Base. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright: 2013 (c) SwiftOtter Studios
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/5/15
 * @package default
 **/

class SwiftOtter_Inventory_Model_Cron
{
    const XML_PATH_INVENTORY_EMAIL_TEMPLATE = 'alert_email';
    const XML_PATH_INVENTORY_EMAIL_TO = 'alert_email_to';
    const VENDOR_ID_ATTRIBUTE = 'vendor_id';
    const EMAIL_INVENTORY_NOTIFICATION_CODE = 'inventory_alert';


    public function hourlyRun()
    {
        $this->inventoryUpdate();
    }

    public function dailyRun()
    {
        $this->checkETA();
    }

    public function checkETA()
    {
        Mage::log('ETA check triggered');
        Mage::getModel('SwiftOtter_Inventory/ETA')->alertDateReached();
    }

    public function inventoryUpdate()
    {
        Mage::log('Inventory update triggered');

        $vendorAttribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, self::VENDOR_ID_ATTRIBUTE);

        $stockItems = Mage::getModel('cataloginventory/stock_item')->getCollection();
        $stockItems->addFieldToFilter('need_to_alert', array('eq' => 1));
        $stockItems->addFieldToFilter('has_alerted', array('eq' => 0));
        $stockItems->getSelect()->where('qty <= reorder_point');

        $stockItems->getSelect()->joinLeft(
            array('vendor_id_table' => $vendorAttribute->getBackendTable()),
            sprintf('main_table.product_id = vendor_id_table.entity_id AND vendor_id_table.attribute_id = %s', $vendorAttribute->getAttributeId()),
            array('vendor_id' => 'value')
        );

        if (count($stockItems) > 0) {
            $vendors = $this->_groupByVendor($stockItems);

            /** @var SwiftOtter_Inventory_Model_Vendor $vendor */
            foreach ($vendors as $vendor) {
                $this->_sendStockItemMail($vendor->getStockItems(), $vendor);
            }

            $this->_sendStockItemMail($stockItems);

            foreach ($stockItems as $stockItem) {
                $stockItem->setHasSaved(true)
                    ->setNeedToAlert(false)
                    ->setHasAlerted(true)
                    ->save();
            }
        }
    }

    /**
     * @param array $stockItems
     * @param SwiftOtter_Inventory_Model_Vendor $vendor
     */
    protected function _sendStockItemMail($stockItems, $vendor = null)
    {
        if (count($stockItems) > 0) {
            $helper = Mage::helper('SwiftOtter_Inventory');

            Mage::getDesign()->setArea(Mage_Core_Model_App_Area::AREA_FRONTEND);
            /** @var SwiftOtter_Inventory_Block_Email_InventoryAlert $block */
            $block = Mage::app()->getLayout()->createBlock('SwiftOtter_Inventory/Email_InventoryAlert')
                ->setStockItems($stockItems)
                ->setVendor($vendor);

            $html = $block->toHtml();

            $details = Mage::getModel('SwiftOtter_Email/Details')
                ->setSubject($helper->__('Inventory Notification'))
                ->setDescription($helper->__('The following products have triggered an inventory stock alert.'))
                ->setText($html);

            if ($vendor) {
                $details->setTitle($helper->__('Inventory Alert for: %s', $vendor->getName()));
            } else {
                $details->setTitle($helper->__('Inventory Alert'));
            }

            $observer = array(
                'code' => self::EMAIL_INVENTORY_NOTIFICATION_CODE,
                'details' => $details
            );

            if ($vendor) {
                $observer['emails'] = $vendor->getInventoryAlert();
                $observer['input_emails_only'] = true;
            }

            Mage::dispatchEvent('swiftotter_email_notification_send', $observer);
        }
    }

    /**
     * @param $stockItems
     * @return array
     */
    protected function _groupByVendor($stockItems)
    {
        $vendors = array();

        /** @var Mage_CatalogInventory_Model_Stock_Item $stockItem */
        foreach ($stockItems as $stockItem)
        {
            $vendorId = $stockItem->getVendorId();
            /** @var SwiftOtter_Inventory_Model_Vendor $vendor */
            $vendor = null;

            foreach ($vendors as $inputVendor) {
                if ($inputVendor->getId() == $vendorId) {
                    $vendor = $inputVendor;
                }
            }

            if (!$vendor) {
                $vendor = $this->_loadVendor($vendorId);
                $vendors[] = $vendor;
            }

            $vendor->addStockItem($stockItem);
        }

        return $vendors;
    }

    protected function _loadVendor($vendorId)
    {
        return Mage::getModel('SwiftOtter_Inventory/Vendor')->load($vendorId);
    }
}