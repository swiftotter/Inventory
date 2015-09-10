<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 10/29/2013
 * @package default
 **/

class SwiftOtter_Inventory_Block_Override_Adminhtml_Catalog_Product_Edit_Tab_Inventory extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('SwiftOtter/Inventory/Override/catalog_product_edit_tab_inventory.phtml');
    }
}