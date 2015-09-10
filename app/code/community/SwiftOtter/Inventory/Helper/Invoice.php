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
 * @copyright Swift Otter Studios, 7/18/14
 * @package default
 **/


class SwiftOtter_Inventory_Helper_Invoice extends SwiftOtter_Base_Helper_Data
{
    const EMAIL_NOTIFICATION_CODE = 'drop_shipped_item_invoice';
    const NON_EXISTANT_VENDOR_ID = 'main';


    /**
     * Loads invoiced items and their vendors and creates a list, per vendor, to alert if drop-shipped.
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     */
    public function alertDropShipments($invoice)
    {
        $vendorList = $this->_formulateVendorProductList($invoice);
        foreach ($vendorList as $vendorId => $products) {
            /** @var SwiftOtter_Inventory_Model_Vendor $vendor */
            $vendor = Mage::getModel('SwiftOtter_Inventory/Vendor')->load($vendorId);

            $html = Mage::app()->getLayout()->createBlock('SwiftOtter_Inventory/Email_DropShipNotification')
                ->setOrder($invoice->getOrder())
                ->setVendor($vendor)
                ->setInvoice($invoice)
                ->setProducts($products)
                ->toHtml();

            $details = Mage::getModel('SwiftOtter_Email/Details')
                ->setSubject($this->__('Vendor Drop Ship Notification'))
                ->setTitle($this->__('%s Drop Ship Notification', $vendor->getName()))
                ->setText($html);

            Mage::dispatchEvent('swiftotter_email_notification_send', array(
                'code' => self::EMAIL_NOTIFICATION_CODE,
                'emails' => $vendor->getDropShipAlert(),
                'details' => $details
            ));
        }
    }

    protected function _formulateVendorProductList($invoice, $includeNonDropShippedItems = false)
    {
        $list = array();
        $products = array();

        /** @var Mage_Sales_Model_Order_Invoice_Item $item */
        foreach ($invoice->getAllItems() as $item) {
            $productId = $item->getOrderItem()->getProductId();

            if (!isset($products[$productId])) {
                $product = Mage::getModel('catalog/product')->load($productId);
                $products[$productId] = $product;
            } else {
                $product = $products[$productId];
            }

            $go = false;
            $vendorId = null;

            if ($product->getDropShipped() && $product->getVendorId() && !$this->getProductIsConfigurable($product)) {
                $vendorId = $product->getVendorId();
                $go = true;
            } else if ($includeNonDropShippedItems) {
                $vendorId = self::NON_EXISTANT_VENDOR_ID;
                $go = true;
            }

            if ($go) {
                if (!isset($list[$vendorId]) || !is_array($list[$vendorId])) {
                    $list[$vendorId] = array();
                }

                $vendor = $list[$vendorId];

                if (!isset($vendor[$productId])) {
                    $vendor[$productId] = $product;
                }
                $product->setQty($product->getQty() + $item->getQty());

                $list[$vendorId] = $vendor;
            }
        }

        return $list;
    }
}