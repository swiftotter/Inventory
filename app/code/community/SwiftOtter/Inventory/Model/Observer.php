<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

class SwiftOtter_Inventory_Model_Observer
{
    const PRODUCT_INVENTORY_QTY_CHANGED = 'swiftotter_inventory_product_qty_changed';


    const ADMINHTML_ROUTE_INVOICE_CREATE = 'adminhtml_sales_order_invoice_new';
    const XML_PATH_INVENTORY_BACKORDER_RECEIVED_EMAIL_TEMPLATE = 'received_inventory_email';
    const XML_PATH_INVENTORY_BACKORDER_RECEIVED_EMAIL_TO = 'received_inventory_email_to';
    const XML_PATH_INVENTORY_BACKORDER_RECEIVED_EMAIL_ENABLED = 'received_inventory_email_enabled';

    public function catalogProductSaveCommitAfter($observer)
    {
        $product = $observer->getProduct();

        if (strtotime($product->getOrigData('eta')) !== strtotime($product->getEta())
            && $product->getTypeId() !== 'clearance'
            && !$product->getHasETAAlerted()) {
            Mage::getModel('SwiftOtter_Inventory/ETA')->alertChange($product);
        }
    }

    public function salesOrderInvoiceItemCollectionLoadAfter($observer)
    {
        $request = Mage::app()->getRequest();
        $actions = array('view', 'print', 'updateqty');

        if (in_array($request->getActionName(), $actions)) {
            /** @var Mage_Sales_Model_Order_Invoice_Item $item */
            foreach ($observer->getOrderInvoiceItemCollection() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->getDropShipped() && $orderItem->getParentItem()) {
                    $orderItem->unsParentItem();
                }
            }
        }
    }

    public function controllerActionLayoutRenderBeforeAdminhtmlSalesOrderInvoiceNew($observer)
    {
        if ($invoice = Mage::registry('current_invoice')) {
            $changed = false;

            /** @var Mage_Sales_Model_Order_Invoice_Item $invoiceItem */
            foreach ($invoice->getAllItems() as $invoiceItem) {
                $orderItem = $invoiceItem->getOrderItem();

                if ($orderItem->getQtyBackordered() > 0 && !$orderItem->getParentItemId()) {
                    $qty = $orderItem->getQtyOrdered() - $orderItem->getQtyBackordered();
                    if ($qty > $orderItem->getQtyToInvoice()) {
                        $qty = $orderItem->getQtyToInvoice();
                    }

                    $invoiceItem->setQty($qty);

                    if ($qty == 0) {
                        $invoiceItem->setRowTotal(0)
                            ->setBaseRowTotal(0);
                    }

                    $changed = true;
                }
            }

            if ($changed) {
                $invoice->setGrandTotal(0);
                $invoice->collectTotals();
            }
        }
    }

    public function coreCopyFieldsetSalesConvertOrderItemToInvoiceItem($observer)
    {
        /** @var Mage_Sales_Model_Order_Item $orderItem */
        $orderItem = $observer->getSource();
        /** @var Mage_Sales_Model_Order_Invoice_Item $invoiceItem */
        $invoiceItem = $observer->getTarget();

        if ($orderItem->getQtyBackordered() > 0 && !$orderItem->getParentItemId()) {
            $qty = $orderItem->getQtyOrdered() - $orderItem->getQtyBackordered();
            if ($qty > $orderItem->getQtyToInvoice()) {
                $qty = $orderItem->getQtyToInvoice();
            }

            $invoiceItem->setQty($qty);

            if ($qty == 0) {
                $invoiceItem->setPrice(0);
                $invoiceItem->setBasePrice(0);
            }
        }
    }

    /**
     * Increments the quantity of items on hand when a credit memo is processed
     *
     * @param $observer
     */
    public function salesCreditmemoItemSaveBefore($observer)
    {
        /** @var Mage_Sales_Model_Order_Creditmemo_Item $item */
        $item = $observer->getCreditmemoItem();

        // No id means that there it has not been saved yet. We want to protect from duplicate decrements being made.
        if (!$item->getId()) {
            /** @var Mage_Catalog_Model_Product $product */
            $product = $item->getOrderItem()->getProduct();

            if (!$product->getDropShipped()) {
                $inventory = Mage::getModel('cataloginventory/stock_item')->loadByProduct($item->getProductId());

                $qtyOnHand = round($inventory->getQtyOnHand());
                $qtyOnHand += $item->getQty();

//                $qtyAvailable = (float)$inventory->getQty();
//                $qtyAvailable += $item->getQty();

                $inventory->setQtyOnHand($qtyOnHand);
//                $inventory->setQty($qtyAvailable);

                $inventory->save();
            }
        }
    }

    /**
     * Decrements the quantity on hand when a shipment is processed (leaves the facility). This should represent an
     * accurate count of what is on the shelf.
     *
     * @param $observer
     */
    public function salesShipmentItemSaveBefore($observer)
    {
        /** @var Mage_Sales_Model_Order_Shipment_Item $item */
        $item = $observer->getShipmentItem();

        // No id means that there it has not been saved yet. We want to protect from duplicate decrements being made.
        if (!$item->getId()) {
            Mage::helper('SwiftOtter_Inventory/Order')->adjustProductQtyOnHand($item->getOrderItem(), $item->getQty());

//            /** @var Mage_Catalog_Model_Product $product */
//            $product = $item->getOrderItem()->getProduct();
//
//            if (!$product->getDropShipped()) {
//                $inventory = Mage::getModel('cataloginventory/stock_item')->loadByProduct($item->getProductId());
//
//                $qtyOnHand = (float)$inventory->getQtyOnHand();
//                $qtyOnHand -= $item->getQty();
//
//                $inventory->setQtyOnHand($qtyOnHand);
//                $inventory->save();
//            }
        }
    }

    public function salesQuoteProductAddAfter($observer)
    {
        $items = $observer->getItems();

        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($items as $item) {
            if ($item->getProduct()->getCartQty() < $item->getQty() &&
                $item->getProduct()->getCartQty() < 0) {
                $item->setQty($item->getProduct()->getCartQty());
            }
        }
    }

    public function catalogProductLoadAfter($observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getProduct();

        if ($product->getStockItem()) {
            $stockItem = $product->getStockItem();
            $stockItem->setOriginalQty((float)$stockItem->getQty());
        }
    }

    public function cataloginventoryStockItemLoadAfter($observer)
    {
        /** @var Mage_CatalogInventory_Model_Stock_Item $stockItem */
        $stockItem = $observer->getItem();
        $stockItem->setOriginalQty((float)$stockItem->getQty());
    }

    public function salesOrderShipmentSaveAfter($observer)
    {
        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        $shipment = $observer->getShipment();

        /** @var Mage_Sales_Model_Order_Item $orderItem */
        foreach ($shipment->getOrder()->getAllItems() as $orderItem) {
            if ($orderItem->getQtyShipped() == $orderItem->getQtyOrdered() &&
                $orderItem->getQtyBackordered() > 0) {
                $orderItem->setQtyBackordered(0);
                $orderItem->save();
            }
        }

        Mage::helper('SwiftOtter_Inventory/Order')->verifyOrderBackorderStatus(
            array($shipment->getOrder())
        );
    }

//    public function salesQuoteItemQtySetAfter($observer)
//    {
//        /* @var $quoteItem Mage_Sales_Model_Quote_Item */
//        $quoteItem = $observer->getItem();
//
////        $quoteItem->removeMessageByText('This product is currently out of stock.');
////
////        $this->_removeErrorsFromQuoteAndItem($quoteItem, Mage_CatalogInventory_Helper_Data::ERROR_QTY);
//    }
//
//    /**
//     * Removes error statuses from quote and item, set by this observer
//     * Borrowed from Mage_CatalogInventory_Model_Observer
//     *
//     * @param Mage_Sales_Model_Quote_Item $item
//     * @param int $code
//     * @return Mage_CatalogInventory_Model_Observer
//     */
//    protected function _removeErrorsFromQuoteAndItem($item, $code)
//    {
//        if ($item->getHasError()) {
//            $params = array(
//                'origin' => 'cataloginventory',
//                'code' => $code
//            );
//            $item->removeErrorInfosByParams($params);
//        }
//
//        $quote = $item->getQuote();
//        $quoteItems = $quote->getItemsCollection();
//        $canRemoveErrorFromQuote = true;
//
//        foreach ($quoteItems as $quoteItem) {
//            if ($quoteItem->getItemId() == $item->getItemId()) {
//                continue;
//            }
//
//            $errorInfos = $quoteItem->getErrorInfos();
//            foreach ($errorInfos as $errorInfo) {
//                if ($errorInfo['code'] == $code) {
//                    $canRemoveErrorFromQuote = false;
//                    break;
//                }
//            }
//
//            if (!$canRemoveErrorFromQuote) {
//                break;
//            }
//        }
//
//        if ($quote->getHasError() && $canRemoveErrorFromQuote) {
//            $params = array(
//                'origin' => 'cataloginventory',
//                'code' => $code
//            );
//            $quote->removeErrorInfosByParams(null, $params);
//        }
//
//        return $this;
//    }

    public function salesOrderPlaceBefore($observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getOrder();

        $hasBackorderedItems = false;

        $this->_updateIncrementQtys($order);

        /** @var Mage_Sales_Model_Order_Item $item */
        foreach ($order->getAllItems() as $item) {
            if ($item->getQtyBackordered() > 0) {
                $hasBackorderedItems = true;
            }
        }

        $order->setHasBackorderedItems($hasBackorderedItems);
    }

    public function checkoutSubmitAllAfter($observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getOrder();

        if (Mage::registry('disable_events')) {
            return false;
        }

        $items = $order->getAllItems();
        /** @var Mage_Sales_Model_Order_Item $item */
        foreach ($items as $item) {
            $product = $item->getProduct();

            if ($product->getDropShipped()) {
                $item->setDropShipped(true);
            }

            if ($product->getShipSeparately()) {
                $item->setShipSeparately(true);
                if ($item->getParentItem()) {
                    $item->getParentItem()->setShipSeparately(true);
                }
            }

            if ($product->isComposite() && $item->getHasChildren()) {
                Mage::helper('SwiftOtter_Inventory/Backorder')->reindexParentOrderItems(array($item));
            }

            $stockItem = Mage::getModel('cataloginventory/stock_item');
            $stockItem->assignProduct($product);

            if (!$product->isComposite() && $stockItem->getManageStock()) {
                if ($stockItem->getQty() <= $stockItem->getReorderPoint() && !$stockItem->getHasAlerted()) {
                    $stockItem->setNeedToAlert(true);
                    $stockItem->save();
                }

                if ($item->getProduct()->getDropShipped() && $item->getQtyBackordered()) {
                    $item->setQtyBackordered(0);
                }
            }

            if ($item->hasDataChanges()) {
                $item->save();
            }
        }
    }

    /**
     * @param Mage_Sales_Model_Order $order
     */
    protected function _updateIncrementQtys($order)
    {
        $items = $order->getAllItems();

        /** @var Mage_Sales_Model_Order_Item $item */
        foreach ($items as $item) {
            $product = $item->getProduct();
            $incrementProductId = $product->getIncrementProductId();

            if ($product->getEnableIncrementProductQty() &&
                $incrementProductId) {
                $increment = (int)$product->getIncrementProductQty();
                if (!$increment) {
                    $increment = 1;
                }

                $incrementProduct = $product;
                if ($incrementProductId != $product->getId()) {
                    $incrementProduct = Mage::getModel('catalog/product')->setId($incrementProductId);
                }

                $stockItem = Mage::getModel('cataloginventory/stock_item');

                $stockItem->assignProduct($incrementProduct)
                    ->setQty($stockItem->getQty() + ($increment * $item->getQtyOrdered()))
                    ->save();
            }
        }
    }


    /**
     * Updates boilerplate content for the stock item. Primarily, this is setting the alerted state, and determining
     * whether to trigger the product qty changed. The only thing that might cause a problem (but probably not) would
     * be a credit memo increasing the inventory of a product.
     *
     * @param $observer
     * @return bool
     */
    public function cataloginventoryStockItemSaveAfter($observer)
    {
        if (Mage::registry('disable_events')) {
            return false;
        }

        /** @var Mage_CatalogInventory_Model_Stock_Item $stockItem */
        $stockItem = $observer->getItem();
        $product = $stockItem->getProduct();
        if (!$product) {
            $product = Mage::getModel('catalog/product')->load($stockItem->getProductId());
        }

        // Has saved - to prevent recursion.
        if ($stockItem->getHasSaved() || !$stockItem->getManageStock() || $product->isComposite()) {
            return false;
        }

        $stockItem->setHasSaved(true);

        $originalQtyOnHand = (float)$stockItem->getOriginalQtyOnHand();
        $qtyOnHand = (float)$stockItem->getQtyOnHand();

        if ($stockItem->hasOriginalQtyOnHand()) {
            $difference = $qtyOnHand - $originalQtyOnHand;
            $stockItem->setQty($stockItem->getQty() + $difference);

            // A failsafe to ensure that the value always stays at or below on hand numbers
            if ($stockItem->getQty() > $stockItem->getQtyOnHand()) {
                $stockItem->setQty($stockItem->getQtyOnHand());
            }

            if ($difference > 0) {
                if (!$product) {
                    $product = Mage::getModel('catalog/product')->load($stockItem->getProductId());
                }

                Mage::dispatchEvent(self::PRODUCT_INVENTORY_QTY_CHANGED, array(
                    'product' => $product,
                    'stock_item' => $stockItem,
                    'difference' => $qtyOnHand - $originalQtyOnHand,
                    'original_quantity' => $originalQtyOnHand,
                    'current_quantity' => $qtyOnHand
                ));

                $stockItem->unsQtyCorrection();
            }
        }

        if ($stockItem->getHasAlerted() && $stockItem->getQty() > $stockItem->getReorderPoint()) {
            $stockItem->setHasAlerted(0);
        }

        $stockItem->setIsInStock($stockItem->getQty() > 0);

        if ($stockItem->hasDataChanges()) {
            $stockItem->unsQtyCorrection();
            $stockItem->save();
        }
    }

    /**
     * Listens to the event and provides more specific information. The primary function is to trigger the
     * receive function in the backorder helper
     *
     * @param $observer
     */
    public function swiftotterInventoryProductQtyChanged($observer)
    {
        $currentQty = (int)$observer->getCurrentQuantity();
        $originalQty = (int)$observer->getOriginalQuantity();

        $difference = $observer->getDifference();

        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getProduct();

        if (is_object($product) && $product->getEta() && $originalQty <= 0 && $currentQty > 0) {
            $product->setEta(null);
            $product->getResource()->saveAttribute($product, 'eta');

            Mage::getModel('SwiftOtter_Inventory/ETA')->alertChange($product);
            Mage::helper('SwiftOtter_Base')->cleanProduct($product);
        }

        Mage::helper('SwiftOtter_Inventory/Backorder')->processReceiveEvent($currentQty, $originalQty, $difference, $product);
    }

    public function salesOrderItemCollectionLoadAfter ($observer)
    {
        if (Mage::app()->getRequest()->getControllerName() == 'sales_order_invoice' && Mage::app()->getRequest()->getActionName() == 'new') {
            $collection = $observer->getOrderItemCollection();

            /** @var Mage_Sales_Model_Order_Item $orderItem */
            foreach ($collection as $orderItem) {
                if ($orderItem->getQtyBackordered() > 0) {
                    $orderItem->setInvoiceQty($orderItem->getQtyOrdered() - $orderItem->getQtyBackordered());
                } else {
                    $orderItem->setInvoiceQty($orderItem->getQtyOrdered());
                }
            }
        }
    }

    public function salesOrderInvoiceRegister($observer)
    {
        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = $observer->getInvoice();

        Mage::helper('SwiftOtter_Inventory/Invoice')->alertDropShipments($invoice);

        if (Mage::registry('disable_events')) {
            return false;
        }

        // TODO: I can't jog my memory as to why this was in here. I am removing and we will see what the fallout is.

//        /** @var Mage_Sales_Model_Order_Invoice_Item $item */
//        foreach ($invoice->getAllItems() as $item) {
//            $orderItem = $item->getOrderItem();
//            $invoicedQty = $item->getQty();
//            $backorderQty = $orderItem->getQtyBackordered();
//
//            if ($backorderQty > 0) {
//                $orderItem->setQtyBackordered($backorderQty - $invoicedQty);
//            }
//            $orderItem->save();
//        }
    }

    public function salesShipmentItemSaveAfter($observer)
    {
        if (Mage::registry('disable_events')) {
            return false;
        }

        /** @var Mage_Sales_Model_Order_Shipment_Item $shipmentItem */
        $shipmentItem = $observer->getShipmentItem();

        Mage::helper('SwiftOtter_Inventory/Backorder')->updateBackorderReceivedWithShipment(
            $shipmentItem->getOrderItemId(),
            $shipmentItem->getId()
        );
    }


    public function swiftotterInventoryMassEditAfterUpdate($observer)
    {
        $orderItems = Mage::registry('backorder_received');
        if (is_array($orderItems) &&
            Mage::helper('SwiftOtter_Inventory')->getStoreConfigFlag(self::XML_PATH_INVENTORY_BACKORDER_RECEIVED_EMAIL_ENABLED)) {

            /** @var Mage_Core_Model_Translate $translate */
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);

            /** @var Mage_Core_Model_Email_Template $mailTemplate */
            $mailTemplate = Mage::getModel('core/email_template');
            $template = Mage::helper('SwiftOtter_Inventory')->getStoreConfig(self::XML_PATH_INVENTORY_BACKORDER_RECEIVED_EMAIL_TEMPLATE);
            $sendString = Mage::helper('SwiftOtter_Inventory')->getStoreConfig(self::XML_PATH_INVENTORY_BACKORDER_RECEIVED_EMAIL_TO);

            $sendTo = array();
            foreach (explode(';', $sendString) as $to) {
                $sendTo[] = array(
                    'email' => $to,
                    'name' => 'SwiftOtter',
                );
            }

            foreach ($sendTo as $recipient) {
                $mailTemplate->setDesignConfig(array(
                    'area' => 'frontend',
                    'store' => Mage::app()->getStore()
                ));

                $mailTemplate->sendTransactional(
                    $template,
                    'general',
                    $recipient['email'],
                    $recipient['name'],
                    array(
                        'order_items' => $orderItems
                    ));

                $translate->setTranslateInline(true);
            }
        }
    }

}