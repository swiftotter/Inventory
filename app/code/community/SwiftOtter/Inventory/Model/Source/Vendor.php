<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

class SwiftOtter_Inventory_Model_Source_Vendor extends SwiftOtter_Base_Model_Source_Abstract
{
    public function getAllOptions()
    {
        $vendors = Mage::getModel('SwiftOtter_Inventory/Vendor')->getCollection();
        $vendors->addOrder('name', $vendors::SORT_ORDER_ASC);
        $output = array(
            '' => Mage::helper('SwiftOtter_Inventory')->__(' -- No Selection -- ')
        );

        foreach ($vendors as $vendor) {
            $output[$vendor->getId()] = sprintf('%s (%s)', $vendor->getName(), $vendor->getId());
//            $output[] = array('value' => $vendor->getId(), 'label' => $vendor->getName());
        }

        return $output;
    }
}