<?php

class SwiftOtter_Inventory_Block_Admin_Report_CurrentBackorders_Grid extends SwiftOtter_Base_Block_Admin_Form_Filter_Grid
{
    protected $_helper = 'SwiftOtter_Inventory';

    public function __construct()
    {
        parent::__construct();

        $this->setId('current_backorder_report')
             ->setDefaultSort('order_date')
             ->setDefaultDir('desc')
             ->setUseAjax(true)
             ->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        /** @var SwiftOtter_Inventory_Model_Resource_CurrentBackorder_Collection $collection */
        $collection = Mage::getResourceModel('SwiftOtter_Inventory/CurrentBackorder_Collection')->retrieveBackorders();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('order_increment_id', array(
            'header'	=> $this->__('Order #'),
            'align'		=> 'left',
            'index'		=> 'order_increment_id',
            'filter_index' => 'order.entity_id'
        ));


        $this->addColumn('order_date', array(
            'header'	=> $this->__('Order Placed Date'),
            'align'		=> 'right',
            'index'		=> 'order_date',
            'type'      => 'date',
            'filter_index' => 'order.created_at',
            'frame_callback' => array($this, 'formatDateTime')
        ));

        $this->addColumn('eta', array(
            'header'	=> $this->__('ETA'),
            'align'		=> 'right',
            'index'		=> 'eta',
            'type'      => 'date',
            'filter_index' => 'product_eta_table.value',
            'frame_callback' => array($this, 'formatDateTime')
        ));

        $this->addColumn('days_waiting', array(
            'header'	=> $this->__('Days Waiting'),
            'align'		=> 'center',
            'index'		=> 'days_waiting',
            'type'      => 'number',
            'filter_index' => 'DATEDIFF(`order`.created_at, NOW())'
        ));

        $this->addColumn('qty_backordered', array(
            'header'	=> $this->__('Quantity'),
            'align'		=> 'center',
            'index'		=> 'qty_backordered',
            'type'      => 'number'
        ));

        $this->addColumn('sku', array(
            'header'	=> $this->__('SKU'),
            'align'		=> 'left',
            'index'		=> 'sku',
            'filter_index' => 'product.sku'
        ));

        $this->addColumn('name', array(
            'header'	=> $this->__('Product'),
            'align'		=> 'left',
            'index'		=> 'name',
        ));

        $this->addColumn('customer_name', array(
            'header'	=> $this->__('Customer'),
            'align'		=> 'left',
            'index'		=> 'customer_name',
            'filter_index' => 'CONCAT(`order`.customer_firstname, \' \', `order`.customer_lastname)'
        ));

//        $this->addColumn('vendor_id', array(
//            'header'	=> $this->__('Vendor'),
//            'align'		=> 'center',
//            'index'		=> 'vendor_id',
//            'type'      => 'options',
//            'options'   => Mage::getModel('SwiftOtter_Inventory/Source_Vendor')->getAllOptions()
//        ));

        $this->addColumn('view_product', array(
            'type' => 'action',
            'getter' => 'getProductId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('SwiftOtter_Inventory')->__('Product'),
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

        $this->addColumn('view_order', array(
            'type' => 'action',
            'getter' => 'getOrderId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('SwiftOtter_Inventory')->__('Order'),
                    'url' => array(
                        'base' => '*/sales_order/view'
                    ),
                    'field' => 'order_id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'order_id'
        ));


		return parent::_prepareColumns();
	}

    public function formatDateTime ($renderedValue, $row, $column, $isExport)
    {
        if (strtotime($renderedValue) != 0) {
            return date('m-d-Y', strtotime($renderedValue));
        } else {
            return " -- ";
        }
    }

    public function getGridUrl($params = array())
    {
        return $this->getUrl('*/*/currentbackordersgrid', array('_current'=>true));
    }


}