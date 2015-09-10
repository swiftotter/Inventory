<?php

class SwiftOtter_Inventory_Block_Admin_Report_ShippableBackorders_Grid extends SwiftOtter_Base_Block_Admin_Form_Filter_Grid
{
    protected $_helper = 'SwiftOtter_Inventory';

    public function __construct()
    {
        parent::__construct();

        $this->setId('shippable_backorder_report')
             ->setDefaultSort('id')
             ->setDefaultDir('desc')
             ->setUseAjax(true)
             ->setSaveParametersInSession(true);
    }

    protected function getSelectedProducts() {

    }

    protected function _prepareCollection()
    {
        /** @var SwiftOtter_Inventory_Model_Resource_CurrentBackorder_Collection $collection */
        $collection = Mage::getResourceModel('SwiftOtter_Inventory/ReceivedBackorder_Collection')->retrieveShippableBackorders();

        $this->setDefaultFilter(array(
            'shipped' => 0
        ));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }



    protected function _afterLoadCollection()
    {
//        $collection = $this->getCollection();
//
//        $total = 0;
//        $qtyInStock = 0;
//        foreach ($collection as $row) {
//            $total += $row->getTotalValue();
//            $qtyInStock += $row->getQtyInStock();
//        }
//
//        $this->setTotals(new Varien_Object(
//            array (
//                'total_value' => $total,
//                'qty_in_stock' => $qtyInStock
//            )
//        ));

        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {

//        $renderers = array(
//            'input_edit' => 'SwiftOtter_Inventory/Admin_Grid_Column_Renderer_InputEdit'
//        );
//        $this->setColumnRenderers($renderers);

        $editable = true;

        $this->addColumn('order_increment_id', array(
            'header'	=> $this->__('Order Number'),
            'align'		=> 'center',
            'index'		=> 'order_increment_id',
            'filter_index' => 'order.increment_id'
        ));

        $this->addColumn('created_at', array(
            'header'	=> $this->__('Received At'),
            'align'		=> 'center',
            'index'		=> 'created_at',
            'type'      => 'date'
        ));

        $this->addColumn('customer_name', array(
            'header'	=> $this->__('Customer'),
            'align'		=> 'center',
            'index'		=> 'customer_name',
        ));

        $this->addColumn('sku', array(
            'header'	=> $this->__('SKU'),
            'align'		=> 'center',
            'index'		=> 'sku',
            'filter_index' => 'product.sku'
        ));

        $this->addColumn('name', array(
            'header'	=> $this->__('Name'),
            'align'		=> 'center',
            'index'		=> 'name',
        ));

        $this->addColumn('qty_ordered', array(
            'header'	=> $this->__('Quantity Ordered'),
            'align'		=> 'center',
            'index'		=> 'qty_ordered',
        ));

        $this->addColumn('qty_to_ship', array(
            'header'	=> $this->__('Shippable Quantity'),
            'align'		=> 'center',
            'index'		=> 'qty_to_ship',
        ));

        $this->addColumn('shipped', array(
            'header'    => $this->__('Has Shipped'),
            'align'     => 'center',
            'index'     => 'shipped',
            'type'      => 'options',
            'options'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray()
        ));


        $this->addColumn('view_order', array(
            'type' => 'action',
            'getter' => 'getOrderId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('SwiftOtter_Inventory')->__('View Order'),
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

    public function formatViewShipment ($renderedValue, $row, $column, $isExport)
    {
        if (!$row->getShipped()) {
            return '';
        } else {
            return $renderedValue;
        }
    }

    public function getGridUrl($params = array())
    {
        return $this->getUrl('*/*/shippablebackordersgrid', array('_current'=>true));
    }


}