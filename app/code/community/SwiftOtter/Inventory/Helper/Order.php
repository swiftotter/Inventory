<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 08/10/2013
 * @package default
 **/

class SwiftOtter_Inventory_Helper_Order extends Mage_Core_Helper_Abstract
{
    /**
     * @param array $orders Accepts an array with the Mage_Sales_Model_Order model being the value in the array
     */
    public function verifyOrderBackorderStatus($orders)
    {
        $orderItemCollection = Mage::getResourceModel('SwiftOtter_Inventory/OrderItem_Collection');

        Mage::getResourceModel('sales/order')->beginTransaction();

        /** @var Mage_Sales_Model_Order $order */
        foreach ($orders as $orderId => $order) {
            $backorderedItems = $orderItemCollection->getTotalBackorderedItemsByOrder($order);
            if (!$backorderedItems) {
                $order->setHasBackorderedItems(false)
                    ->save();
            }
        }

        Mage::getResourceModel('sales/order')->commit();
    }

    public function adjustProductQtyOnHand (Mage_Sales_Model_Order_Item $orderItem, $initialQty = 1, $orderShipmentRatio = 1, $child = false)
    {
        $product = $orderItem->getProduct();

        if (!$product->getDropShipped()) {
            if (!$child) {
                $qty = $initialQty;
            } else {
                $qty = round($orderItem->getQtyOrdered() * $orderShipmentRatio);
            }


            $inventory = Mage::getModel('cataloginventory/stock_item')->loadByProduct($orderItem->getProductId());

            if ($inventory->getManageStock()) {
                $qtyOnHand = round($inventory->getQtyOnHand());
                $qtyOnHand -= $qty;

                $inventory->setQtyOnHand($qtyOnHand);
                $inventory->save();
            }

//            if ($orderItem->getChildrenItems()) {
//                if (!$child) {
//                    $orderShipmentRatio = $initialQty / $orderItem->getQtyOrdered();
//                }
//
//                foreach ($orderItem->getChildrenItems() as $childOrderItem) {
//                    $this->adjustProductQtyOnHand($childOrderItem, $initialQty, $orderShipmentRatio, true);
//                }
//            }
        }
    }
}