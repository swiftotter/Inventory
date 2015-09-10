<?php

class SwiftOtter_Inventory_Block_Admin_Report_SalesOrdering extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		parent::__construct();
		$this->_controller = 'Admin_Report_SalesOrdering';
		$this->_blockGroup = 'SwiftOtter_Inventory';
		$this->_headerText = Mage::helper('SwiftOtter_Inventory')->__('Sales/Ordering Report');

        $this->_removeButton('add');
	}
	
	protected function _prepareLayout() {
        $this->setChild('filter', $this->getLayout()->createBlock('SwiftOtter_Inventory/Admin_Report_SalesOrdering_Filter')->setSaveParametersInSession(true));
		$this->setChild('grid', $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_Grid', $this->_controller . '.Grid')->setSaveParametersInSession(true));
	}
}