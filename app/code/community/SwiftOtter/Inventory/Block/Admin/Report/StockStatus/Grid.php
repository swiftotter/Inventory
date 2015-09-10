<?php

class SwiftOtter_Inventory_Block_Admin_Report_StockStatus_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('stock_status_report')
			 ->setDefaultSort('id')
			 ->setDefaultDir('desc')
             ->setUseAjax(true)
			 ->setSaveParametersInSession(true);
	}

    protected function getSelectedProducts() {

    }
	
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('catalog/product')->getCollection();

        $collection->joinTable(
            array('s' => $collection->getTable('cataloginventory/stock_item')),
            'product_id = entity_id',
            array(
                'qty_available' => 'qty',
                'qty_on_hand' => 'qty_on_hand',
                'is_in_stock' => 'is_in_stock',
                'reorder_point' => 'reorder_point'
            )
        );

        $select = $collection->getSelect();

        Mage::helper('SwiftOtter_Inventory')
            ->addManageStockFilter($select, 's')
            ->addStockableProductFilter($collection);

        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('vendor_id');
        $collection->addAttributeToSelect('status');

        //$collection->addAttributeToFilter('drop_shipped', array('neq' => '1'));

        $viewStyle = Mage::registry('stock_status_view');
        if (!$viewStyle) {
            $viewStyle = 'all';
        }

        if ($viewStyle == 'low_stock') {
            $select->where('`s`.qty <= `s`.reorder_point');
        } else if ($viewStyle == 'out_of_stock') {
            $select->where('`s`.qty <= 0');
        }

		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns() {

        $renderers = array(
            'status_edit' => 'SwiftOtter_Inventory/Admin_Grid_Column_Renderer_StatusEdit'
        );
        $this->setColumnRenderers($renderers);

        $editable = true;


        $this->addColumn('sku', array(
            'header'	=> $this->__('SKU'),
            'align'		=> 'center',
            'index'		=> 'sku',
        ));
		
		$this->addColumn('name', array(
			'header'	=> $this->__('Name'),
			'align'		=> 'center',
			'index'		=> 'name',
		));

		$this->addColumn('vendor_id', array(
			'header'	=> $this->__('Vendor'),
			'align'		=> 'center',
			'index'		=> 'vendor_id',
            'type'      => 'options',
            'options'   => Mage::getModel('SwiftOtter_Inventory/Source_Vendor')->getAllOptions()
		));

//        $this->addColumn('raw_cost', array(
//            'header'	=> $this->__('Cost'),
//            'align'		=> 'center',
//            'index'		=> 'raw_cost',
//            'type'      => 'currency',
//            'editable'  => false
//        ));
//
//		$this->addColumn('price', array(
//			'header'	=> $this->__('Price'),
//			'align'		=> 'center',
//			'index'		=> 'price',
//            'type'      => 'currency',
//            'editable'  => false
//		));

		$this->addColumn('qty_available', array(
			'header'	=> $this->__('QTY Available'),
			'align'		=> 'center',
			'index'		=> 'qty_available',
            'type'      => 'number',
            'editable'  => false
		));

        $this->addColumn('qty_on_hand', array(
            'header'	=> $this->__('QTY on Hand'),
            'align'		=> 'center',
            'index'		=> 'qty_on_hand',
            'type'      => 'number',
            'editable'  => false
        ));

        $this->addColumn('reorder_point', array(
            'header'	=> $this->__('Reorder Point'),
            'align'		=> 'center',
            'type'      => 'number',
            'index'		=> 'reorder_point',
            'editable'  => false
        ));

        $this->addColumn('status', array(
            'header'	=> $this->__('Status'),
            'align'		=> 'center',
            'index'		=> 'status',
            'type'      => 'status_edit',
            'options'   => Mage::getModel('catalog/product_status')->getOptionArray()
        ));
		
		return parent::_prepareColumns();
	}


    public function getGridUrl($params = array())
    {
        return $this->getUrl('*/*/stockstatusGrid', array('_current'=>true));
    }


}