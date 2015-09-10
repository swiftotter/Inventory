<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

class SwiftOtter_Inventory_Model_Resource_Vendor extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct(){
        $this->_init('SwiftOtter_Inventory/Vendor', 'id');
    }
}