<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

class SwiftOtter_Inventory_Model_Resource_AdjustmentLog extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct(){
        $this->_init('SwiftOtter_Inventory/AdjustmentLog', 'id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getCreatedAt()) {
            $time = new DateTime();
            $object->setCreatedAt($time->format('Y-m-d h:i:s'));
        }

        return parent::_beforeSave($object);
    }


}