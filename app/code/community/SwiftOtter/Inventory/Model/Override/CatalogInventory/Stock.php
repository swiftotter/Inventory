<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 10/10/2013
 * @package default
 **/

class SwiftOtter_Inventory_Model_Override_CatalogInventory_Stock extends Mage_CatalogInventory_Model_Stock
{
    /**
     * Subtract product qtys from stock.
     * Return array of items that require full save
     *
     * @param array $items
     * @return array
     */
    public function registerProductsSale($items)
    {
        $qtys = $this->_prepareProductQtys($items);

        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $quoteItems = $quote->getAllItems();

        $item = Mage::getModel('cataloginventory/stock_item');
        $this->_getResource()->beginTransaction();
        $stockInfo = $this->_getResource()->getProductsStock($this, array_keys($qtys), true);
        $fullSaveItems = array();
        foreach ($stockInfo as $itemInfo) {
            $item->setData($itemInfo);
            // Added to prevent problems with
//            if (!$item->checkQty($qtys[$item->getProductId()])) {
//                $this->_getResource()->commit();
//                Mage::throwException(Mage::helper('cataloginventory')->__('Not all products are available in the requested quantity'));
//            }

            $product = $this->_getProductFromQuote($quoteItems, $item->getProductId());

            /** ADDED IN FOLLOWING IF TO FILTER FOR DROP SHIPMENTS **/
            if (!$this->_productIsDropShipped($item->getProductId())) {
                $item->subtractQty($qtys[$item->getProductId()]);
            }

            if (!$item->verifyStock() || $item->verifyNotification()) {
                $fullSaveItems[] = clone $item;
            }
        }
        $this->_getResource()->correctItemsQty($this, $qtys, '-');
        $this->_getResource()->commit();
        return $fullSaveItems;
    }

    protected function _productIsDropShipped($productId)
    {
        $resource = Mage::getResourceModel('catalog/product');
        return (bool)$resource->getAttributeRawValue($productId, 'drop_shipped', Mage::app()->getStore());
    }

    /**
     * @param array $items
     * @param int $productId
     */
    protected function _getItemFromQuote($items, $productId)
    {
        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($items as $item) {
            if ($item->getProduct() && $item->getProduct()->getId() == $productId) {
                return $item;
            }
        }
    }


    /**
     * @param array $items
     * @param int $productId
     */
    protected function _getProductFromQuote($items, $productId)
    {
        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($items as $item) {
            if ($item->getProduct() && $item->getProduct()->getId() == $productId) {
                return $item->getProduct();
            }
        }
    }
}