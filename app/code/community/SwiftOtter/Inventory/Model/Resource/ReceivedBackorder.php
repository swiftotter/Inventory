<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

class SwiftOtter_Inventory_Model_Resource_ReceivedBackorder extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct(){
        $this->_init('SwiftOtter_Inventory/ReceivedBackorder', 'id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getCreatedAt()) {
            $object->setCreatedAt(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
        }

        return parent::_beforeSave($object);
    }
}