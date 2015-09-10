<?php

class SwiftOtter_Inventory_Block_Admin_Report_SalesOrdering_Grid extends SwiftOtter_Base_Block_Admin_Form_Filter_Grid
{
    const ENTITY_TYPE = 'catalog_product';
    const VENDOR_ATTRIBUTE_CODE = 'vendor_id';

    protected $_helper = 'SwiftOtter_Report';

    public function __construct()
    {
        parent::__construct();

        $this->setId('inventory_sales_ordering_report')
             ->setDefaultSort('qty_ordered')
             ->setDefaultDir('desc')
             ->setUseAjax(true)
             ->setSaveParametersInSession(true)
             ->setCountTotals(true);

        $request = Mage::app()->getRequest();
        if ($request->getParam('sort')) {
            $sortKey = $request->getParam('sort');
            $sortValue = $request->getParam('value');

            $this->setDefaultFilter(array(
                $sortKey => $sortValue
            ));
        }

    }


    protected function _prepareCollection()
    {
        /** @var SwiftOtter_Inventory_Model_Resource_Report_SalesOrdering_Collection $collection */

        $start = null;
        $end = null;
        $reorderTimeframe = null;

        if ($this->getFilter()) {
            $filter = $this->getFilter();
            $start = $filter->getRange()->getStart();
            $end = $filter->getRange()->getEnd();
            $reorderTimeframe = $filter->getReorderTimeframe();
        }

        $collection = Mage::getResourceModel('SwiftOtter_Inventory/Report_SalesOrdering_Collection')->getReportCollection($start, $end, $reorderTimeframe);
        $this->setCollection($collection);

        if (Mage::app()->getRequest()->getParam('debug')) {
            echo (string)$collection->getSelect();
        }

        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        $collection = $this->getCollection();

        $totalValue = 0;
        $dailySalesAverage = 0;
        $qtyInStock = 0;
        foreach ($collection as $row) {
            $totalValue += $row->getTotalValue();
            $dailySalesAverage += $row->getDailySalesAverage();
            $qtyInStock += $row->getQtyInStock();
        }

        if ($collection->count() > 0) {
            $dailySalesAverage = $dailySalesAverage / $collection->count();
        }

        $this->setTotals(new Varien_Object(
            array (
                'total_value' => $totalValue,
                'daily_sales_average' => $dailySalesAverage,
                'qty_in_stock' => $qtyInStock
            )
        ));

        return parent::_afterLoadCollection();
    }
	
	protected function _prepareColumns()
    {
        $renderers = array(
            'input_edit' => 'SwiftOtter_Inventory/Admin_Grid_Column_Renderer_InputEdit'
        );
        $this->setColumnRenderers($renderers);

        $editable = true;


        $this->addColumn('sku', array(
            'header'	=> $this->__('SKU'),
            'align'		=> 'center',
            'index'		=> 'sku',
            'filter_index' => 'e.sku'
        ));

        $this->addColumn('name', array(
            'header'	=> $this->__('Name'),
            'align'		=> 'center',
            'index'		=> 'name',
            'filter_index' => 'at_name.value'
        ));

        $this->addColumn('vendor_id', array(
            'header'	=> $this->__('Vendor'),
            'align'		=> 'center',
            'index'		=> 'vendor_id',
            'type'      => 'options',
            'filter_index' => 'vendor.id',
            'options'   => Mage::getModel('SwiftOtter_Inventory/Source_Vendor')->getAllOptions()
        ));

        $this->addColumn('qty_available', array(
            'header'	=> $this->__('QTY Available'),
            'align'		=> 'center',
            'type'      => 'number',
            'index'		=> 'qty',
            'group_function' => 'MAX',

            'filter_condition_callback' => array($this, '_groupedFilter')
        ));

        $this->addColumn('qty_on_hand', array(
            'header'	=> $this->__('QTY on Hand'),
            'align'		=> 'center',
            'type'      => 'number',
            'index'		=> 'qty_on_hand',
            'group_function' => 'MAX',
            'filter_condition_callback' => array($this, '_groupedFilter')
        ));

        $this->addColumn('reorder_point', array(
            'header'	=> $this->__('Reorder Point'),
            'align'		=> 'center',
            'index'		=> 'reorder_point',
            'type'      => 'number'
        ));

        $this->addColumn('daily_sales_average', array(
            'header'	=> $this->__('Daily Sales Average'),
            'align'		=> 'center',
            'index'		=> 'daily_sales_average',
            'frame_callback' => array($this, 'dailySalesAverageFormat'),
            'filter'    => false
        ));

        $this->addColumn('qty_ordered', array(
            'header'	=> $this->__('QTY Sold'),
            'align'		=> 'center',
            'index'		=> 'qty_ordered',
            'type'      => 'number',
            'group_function' => 'SUM',
            'filter_condition_callback' => array($this, '_groupedFilter')
        ));

        $this->addColumn('reorder_left', array(
            'header'	=> $this->__('Days Until Reorder'),
            'align'		=> 'center',
            'index'		=> 'reorder_left',
            'type'      => 'number',
            'frame_callback' => array($this, 'reorderLeftFormat'),
            'filter'    => false
        ));

        $this->addColumn('to_stockout', array(
            'header'	=> $this->__('Days Until Out'),
            'align'		=> 'center',
            'index'		=> 'to_stockout',
            'type'      => 'number',
            'frame_callback' => array($this, 'toStockoutFormat'),
            'filter'    => false
        ));

        $this->addColumn('to_order', array(
            'header'	=> $this->__('Needed'),
            'align'		=> 'center',
            'index'		=> 'to_order',
            'type'      => 'number',
            'frame_callback' => array($this, 'toOrderFormat'),
            'filter'    => false
        ));

        $this->addColumn('to_order_case', array(
            'header'	=> $this->__('To Order'),
            'align'		=> 'center',
            'index'		=> 'to_order',
            'type'      => 'number',
            'frame_callback' => array($this, 'toOrderCaseCalculate'),
            'filter'    => false
        ));

		return parent::_prepareColumns();
	}

    public function dailySalesAverageFormat ($renderedValue)
    {
        return round($renderedValue, 3);
    }

    public function toOrderCaseCalculate ($renderedValue, $row)
    {
        $quantityInCase = $row->getCasePackQuantity();

        if ($quantityInCase) {
            $quantity = ceil($row->getToOrder() / $quantityInCase);
            $newCount = $quantity * $quantityInCase;

            $renderedValue = sprintf("%s (%sc of %s)", $newCount, $quantity, $quantityInCase);
        } else {
            if ($row->getToOrder() < 0) {
                $renderedValue = "0";
            } else {
                $renderedValue = $row->getToOrder();
            }
        }
        return $renderedValue;
    }

    public function toOrderFormat ($renderedValue, $row)
    {
        if ($row->getQtyOrdered() > 0) {

            if ($renderedValue < 0) {
                $renderedValue = "0";
            }

            if ($this->getFilter()) {
                if (!$this->getFilter()->getReorderTimeframe()) {
                    if (!$row->getReorderPeriod()) {
                        $renderedValue = $this->__('Reorder Not Specified');
                    } else {
                        $renderedValue = $this->__('%s (%sR, %sL)', $renderedValue, $row->getReorderPeriod(), $row->getLeadTime());
                    }
                } else {
                    if ($row->getLeadTime()) {
                        $renderedValue = $this->__('%s (%sL)', $renderedValue, $row->getLeadTime());
                    }
                }
            }

            return $renderedValue;
        } else {
            return '';
        }
    }

    public function toStockoutFormat ($renderedValue, $row)
    {
        if ($renderedValue <= 0 && $row->getQtyOrdered() > 0) {
            $renderedValue = "<strong>Out of Stock!</strong>";
        }
        return $renderedValue;
    }

    public function reorderLeftFormat ($renderedValue, $row)
    {
//        if ($renderedValue <= 0 && $row->getQtyOrdered() > 0) {
//            $renderedValue = "<strong>0 - Reorder Now</strong>";
//        }
        return $renderedValue;
    }


    public function getGridUrl($params = array())
    {
        return $this->getUrl('*/*/salesOrderingGrid', array('_current'=>true));
    }


}