<?php

class SwiftOtter_Inventory_Block_Admin_Report_StockStatus extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		parent::__construct();
		$this->_controller = 'Admin_Report_StockStatus';
		$this->_blockGroup = 'SwiftOtter_Inventory';

        $headerText = 'Stock Status Report (View All)';
        $viewStyle = Mage::registry('stock_status_view');
        if (!$viewStyle) {
            $viewStyle = 'all';
        }

        if ($viewStyle == 'low_stock') {
            $headerText = 'Stock Status Report (Viewing Low Stock)';
        } else if ($viewStyle == 'out_of_stock') {
            $headerText = 'Stock Status Report (Viewing Out of Stock)';
        }
		$this->_headerText = Mage::helper('SwiftOtter_Inventory')->__($headerText);

        $this->_removeButton('add');

        $this->_addButton('view_all', array(
            'label' => Mage::helper('SwiftOtter_Inventory')->__('View All'),
            'onclick' => 'setLocation(\'' . $this->getUrl('*/*/*', array('view' => 'all')) . '\');'
        ));

        $this->_addButton('view_low_stock', array(
            'label' => Mage::helper('SwiftOtter_Inventory')->__('View Low Stock'),
            'onclick' => 'setLocation(\'' . $this->getUrl('*/*/*', array('view' => 'low_stock')) . '\');'
        ));

        $this->_addButton('view_out_stock', array(
            'label' => Mage::helper('SwiftOtter_Inventory')->__('View Out-Of-Stock'),
            'onclick' => 'setLocation(\'' . $this->getUrl('*/*/*', array('view' => 'out_of_stock')) . '\');'
        ));
	}
	
	protected function _prepareLayout() {
		$this->setChild('grid', $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_Grid', $this->_controller . '.Grid')->setSaveParametersInSession(true));
	}
}