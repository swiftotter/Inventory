<?php

class SwiftOtter_Inventory_Block_Admin_Report_CurrentBackorders extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'Admin_Report_CurrentBackorders';
        $this->_blockGroup = 'SwiftOtter_Inventory';
        $this->_headerText = Mage::helper('SwiftOtter_Inventory')->__('Open Backorders Report');

        $this->_removeButton('add');
    }

    protected function _prepareLayout()
    {
        $this->setChild('grid',
            $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_Grid', $this->_controller . '.Grid')->setSaveParametersInSession(true)
        );
    }
}