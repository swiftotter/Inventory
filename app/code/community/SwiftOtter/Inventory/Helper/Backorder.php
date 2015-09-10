<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 08/10/2013
 * @package default
 **/

class SwiftOtter_Inventory_Helper_Backorder extends Mage_Core_Helper_Abstract
{
    /**
     * @param $observer
     * @return $this
     */
    public function processReceiveEvent($currentQuantity, $originalQuantity, $difference, Mage_Catalog_Model_Product $product)
    {
        // Output variables
        $ordersAffected = 0;
        $itemsAffected = 0;
        $receivedBackorders = array();
        $ordersAffectedArray = array();

        // Iteration collection
        $backorders = Mage::getResourceModel('SwiftOtter_Inventory/OrderItem_Collection')
            ->getBackOrderedItems($product->getId());

        /** @var Mage_Sales_Model_Order_Item $orderItem */
        foreach ($backorders as $orderItem) {
            if ($difference <= 0) {
                break;
            }

            $originalDifference = $difference;
            $itemQtyBackordered = (int)$orderItem->getQtyBackordered();

            if ($itemQtyBackordered <= $difference) {
                // if the quantity backordered is less than the difference in new quantity
                $orderItem->setQtyBackordered(0); // this will always be zero
                $qtyReceived = $itemQtyBackordered;

                $difference -= $itemQtyBackordered; // decrement the difference by the previously backordered amount
            } else {
                $itemQtyBackordered -= $difference; // perform the reverse of above, reduce the number backordered by the difference
                $qtyReceived = $itemQtyBackordered;
                $orderItem->setQtyBackordered($qtyReceived);

                $difference = 0;
            }

            // Initialize the backorder receipt so that it can tracked in the system
            $received = Mage::getModel('SwiftOtter_Inventory/ReceivedBackorder')
                ->initialize($orderItem, $qtyReceived);
            $receivedBackorders[] = $received;

            // order items that are affected
            $itemsAffected++;

            // this may overwrite itself sometimes, but this is desired functionality
            $ordersAffectedArray[$orderItem->getOrderId()] = $orderItem->getOrder();

            // adds to registry for sending alert emails
            $this->addToBackorderReceivedRegistry($orderItem);

            $orderItem->save();

//            if ($difference !== $originalDifference) {
//                $currentQuantity -= $originalDifference - $difference;
//            }
        }

        $ordersAffected = count($ordersAffectedArray);
        $log = $this->log($currentQuantity, $originalQuantity, $product->getId(), $ordersAffected, $itemsAffected);

        Mage::getResourceModel('SwiftOtter_Inventory/ReceivedBackorder')->beginTransaction();

        /** @var SwiftOtter_Inventory_Model_ReceivedBackorder $received */
        foreach ($receivedBackorders as $received) {
            $received->setAdjustmentId($log->getId());
            $received->save();
        }

        Mage::getResourceModel('SwiftOtter_Inventory/ReceivedBackorder')->commit();

        $this->_reindexParentOrderItems();
        $this->_afterProcessReceiveEvent($log, $ordersAffectedArray);

        return $this;
    }

    /**
     * Determines how many parents are in stock and able to be shipped if children are out of stock.
     *
     * @param $orderItemParents
     */
    public function reindexParentOrderItems($orderItemParents)
    {
        if (count($orderItemParents)) {
            /** @var Mage_Sales_Model_Order_Item $orderItem */
            foreach($orderItemParents as $orderItem) {
                if ($children = $this->_getOrderItemChildren($orderItem)) {
                    $qty = $orderItem->getQtyOrdered();
                    $backorderedSum = 0;

                    $availableQty = array($qty);

                    /** @var Mage_Sales_Model_Order_Item $childOrderItem */
                    foreach ($children as $childOrderItem) {
                        if ($childOrderItem->getQtyBackordered()) {
                            // The split quantity (how many are in this kit if one was ordered)
                            $singleQuantity = $childOrderItem->getQtyOrdered() / $qty;

                            // The difference of how many is available to ship
                            $inStock = $childOrderItem->getQtyOrdered() - $childOrderItem->getQtyBackordered();

                            // How many can be shipped (How many in stock divided by the single quantity)
                            $parentAvailable = floor($inStock / $singleQuantity);

                            //We floor it to get the base amount available
                            $availableQty[] = $parentAvailable;
                        }

                        // Not used, but tracked for possible future use
                        $backorderedSum += $childOrderItem->getQtyBackordered();
                    }

                    if (count($availableQty) > 0) {
                        // Forces only full backorders
                        if (min($availableQty) == $orderItem->getQtyOrdered()) {
                            $orderItem->setQtyBackordered(0);
                        } else {
                            $orderItem->setQtyBackordered($orderItem->getQtyOrdered());
                        }

                        // Allows for partial backorders:
                        // $orderItem->setQtyBackordered($orderItem->getQtyOrdered() - min($availableQty));

                        $orderItem->save();
                    }
                }
            }
        }
    }

    protected function _getOrderItemChildren($orderItem)
    {
        if ($orderItem->getChildrenItems()) {
            return $orderItem->getChildrenItems();
        } else {
            $children = Mage::getResourceModel('sales/order_item_collection');
            $children->addFieldToFilter('parent_item_id', array('eq' => $orderItem->getId()));

            return $children;
        }
    }


    protected function _reindexParentOrderItems()
    {
        $orderItemParents = array();

        /** @var Mage_Sales_Model_Order_Item $orderItem */
        foreach ($this->getBackorderReceivedRegistry() as $orderItem) {
            if ($orderItem->getParentItemId() && !array_key_exists($orderItem->getParentItemId(), $orderItemParents)) {
                $parentItem = $orderItem->getParentItem();
                if (!$parentItem) {
                    $parentItem = Mage::getModel('sales/order_item')->load($orderItem->getParentItemId());
                }
                $orderItemParents[$orderItem->getParentItemId()] = $parentItem;
            }
        }

        if (count($orderItemParents)) {
            $this->reindexParentOrderItems($orderItemParents);
        }
    }

    /**
     * Verifies validity of backorder status on orders
     *
     * @param $ordersAffected
     * @return $this
     */
    public function _afterProcessReceiveEvent($log, $ordersAffected)
    {
        Mage::helper('SwiftOtter_Inventory/Order')->verifyOrderBackorderStatus($ordersAffected);

        return $this;
    }

    /**
     * Saves the values to the SwiftOtter_Inventory/AdjustmentLog tables and returns the model
     *
     * @param $currentQty
     * @param $originalQty
     * @param $productId
     * @param $ordersAffected
     * @param $itemsAffected
     * @return SwiftOtter_Inventory_Model_AdjustmentLog
     */
    public function log ($currentQty, $originalQty, $productId, $ordersAffected, $itemsAffected)
    {
        /** @var SwiftOtter_Inventory_Model_AdjustmentLog $logEntry */
        $logEntry = Mage::getModel('SwiftOtter_Inventory/AdjustmentLog');
        $logEntry->setProductId($productId)
            ->setOriginalQuantity($originalQty)
            ->setCurrentQuantity($currentQty)
            ->setOrdersAffected($ordersAffected)
            ->setItemsAffected($itemsAffected)
            ->setUserId(Mage::getSingleton('admin/session')->getUser()->getId());

        $logEntry->save();
        return $logEntry;
    }

    /**
     *
     *
     * @param $orderItemId
     * @param $shipmentItemId
     * @return $this
     */
    public function updateBackorderReceivedWithShipment($orderItemId, $shipmentItemId)
    {
        $receivedBackorder = Mage::getModel('SwiftOtter_Inventory/ReceivedBackorder')->load($orderItemId, 'order_item_id');

        if ($receivedBackorder->getId()) {
            $receivedBackorder
                ->setShipped(true)
                ->setShipmentItemId($shipmentItemId)
                ->save();
        }

        return $this;
    }

    public function getBackorderReceivedRegistry()
    {
        $output = Mage::registry('backorder_received');
        if (!is_array($output)) {
            $output = array();
        }

        return $output;
    }

    /**
     * Adds a order item to the list of order items to queue for an email
     *
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return $this
     */
    public function addToBackorderReceivedRegistry(Mage_Sales_Model_Order_Item $orderItem)
    {
        $items = $this->getBackorderReceivedRegistry();
        $items[] = $orderItem;

        Mage::unregister('backorder_received');
        Mage::register('backorder_received', $items);

        return $this;
    }
}