<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

class SwiftOtter_Inventory_Block_Email_InventoryAlert extends Mage_Core_Block_Template
{
    protected $_stockItems;

    protected function _construct()
    {
        $this->setTemplate('SwiftOtter/Inventory/Email/ReorderAlert.phtml');
    }


    /**
     * @param Mage_CatalogInventory_Model_Resource_Stock_Item_Collection $stockItems
     * @return $this
     */
    public function setStockItems($stockItems)
    {
        $this->_stockItems = $stockItems;
        return $this;
    }

    /**
     * @return Mage_CatalogInventory_Model_Resource_Stock_Item_Collection
     */
    public function getStockItems()
    {
        return $this->_stockItems;
    }
}