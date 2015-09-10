<?php

class SwiftOtter_Inventory_Block_Admin_Report_AdjustmentLog_Grid extends SwiftOtter_Base_Block_Admin_Form_Filter_Grid
{
    protected $_helper = 'SwiftOtter_Inventory';

    public function __construct()
	{
		parent::__construct();

		$this->setId('adjustment_log_report')
			 ->setDefaultSort('id')
			 ->setDefaultDir('desc')
             ->setUseAjax(true)
			 ->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
        /** @var SwiftOtter_Inventory_Model_Resource_AdjustmentLog_Collection $collection */
        $collection = Mage::getResourceModel('SwiftOtter_Inventory/AdjustmentLog_Collection')->withAdditionalFields();

		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

    protected function _afterLoadCollection()
    {
        return parent::_afterLoadCollection();
    }
	
	protected function _prepareColumns()
    {
        $this->addColumn('sku', array(
            'header'	=> $this->__('Sku'),
            'align'		=> 'center',
            'index'		=> 'sku',
        ));

        $this->addColumn('name', array(
            'header'	=> $this->__('Product Name'),
            'align'		=> 'center',
            'index'		=> 'name'
        ));

//        $this->addColumn('vendor', array(
//            'header'	=> $this->__('Vendor'),
//            'align'		=> 'center',
//            'index'		=> 'vendor',
//            'type'      => 'options',
//            'options'   => Mage::getModel('SwiftOtter_Inventory/Source_Vendor')->getAllOptions()
//        ));

        $this->addColumn('created_at', array(
            'header'	=> $this->__('Action Taken'),
            'align'		=> 'center',
            'index'		=> 'created_at',
            'frame_callback' => array($this, 'formatDate')
        ));

        $this->addColumn('user_formatted_name', array(
            'header'	=> $this->__('Action Taken By'),
            'align'		=> 'center',
            'index'		=> 'user_formatted_name'
        ));

        $this->addColumn('original_quantity', array(
            'header'	=> $this->__('Original Quantity'),
            'align'		=> 'center',
            'index'		=> 'original_quantity',
        ));

        $this->addColumn('current_quantity', array(
            'header'	=> $this->__('New Quantity'),
            'align'		=> 'center',
            'index'		=> 'current_quantity',
        ));

        $this->addColumn('orders_affected', array(
            'header'	=> $this->__('Orders Affected'),
            'align'		=> 'center',
            'index'		=> 'orders_affected',
        ));

        $this->addColumn('items_affected', array(
            'header'	=> $this->__('Order Items Affected'),
            'align'		=> 'center',
            'index'		=> 'items_affected',
        ));

        $this->addColumn('view_product', array(
            'type' => 'action',
            'getter' => 'getProductId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('SwiftOtter_Inventory')->__('View Product'),
                    'url' => array(
                        'base' => '*/catalog_product/edit'
                    ),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'product_id'
        ));

		return parent::_prepareColumns();
	}

    public function formatDate ($renderedValue, $row, $column, $isExport)
    {
        $timestamp = Mage::getModel('core/date')->timestamp(strtotime($renderedValue));
        return date('m-d-Y h:i:s', $timestamp);
    }

    public function getGridUrl($params = array())
    {
        return $this->getUrl('*/*/adjustmentloggrid', array('_current'=>true));
    }


}