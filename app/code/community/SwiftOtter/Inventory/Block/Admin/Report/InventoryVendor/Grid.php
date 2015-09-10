<?php

class SwiftOtter_Inventory_Block_Admin_Report_InventoryVendor_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('inventory_vendor_report')
             ->setDefaultSort('id')
             ->setDefaultDir('desc')
             ->setUseAjax(true)
             ->setSaveParametersInSession(true)
             ->setCountTotals(true);;
    }

    protected function getSelectedProducts() {

    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('SwiftOtter_Inventory/Report_InventoryVendor_Collection')->loadJoins();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _afterLoadCollection()
    {
        $collection = $this->getCollection();

        $total = 0;
        $qtyInStock = 0;
        $qtyOnHand = 0;

        foreach ($collection as $row) {
            $total += $row->getTotalValueOnHand();
            $qtyInStock += $row->getQtyAvailable();
            $qtyOnHand += $row->getQtyOnHand();
        }

        $this->setTotals(new Varien_Object(
            array (
                'total_value_on_hand' => $total,
                'qty_available' => $qtyInStock,
                'qty_on_hand' => $qtyOnHand
            )
        ));

        //$this->setCountTotals(true);

        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {
        $renderers = array(
            'input_edit' => 'SwiftOtter_Inventory/Admin_Grid_Column_Renderer_InputEdit'
        );
        $this->setColumnRenderers($renderers);

        $editable = true;

        $this->addColumn('id', array(
            'header'    => $this->__('Vendor Name'),
            'align'     => 'right',
            'index'     => 'id',
            'type'      => 'options',
            'options'   => Mage::getModel('SwiftOtter_Inventory/Source_Vendor')->getAllOptions()
        ));

        $this->addColumn('total_value_on_hand', array(
            'header'    => $this->__('Value (On Hand)'),
            'align'     => 'center',
            'index'     => 'total_value_on_hand',
            'total'     => 'sum',
            'type'      => 'price',
            'filter'    => false,
            'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode()
        ));

        $this->addColumn('qty_on_hand', array(
            'header'    => $this->__('Qty On Hand'),
            'align'     => 'center',
            'index'     => 'qty_on_hand',
            'total'     => 'sum',
            'type'      => 'number',
            'precision' => '0',
            'filter' => false
        ));

        return parent::_prepareColumns();
    }


    public function getGridUrl($params = array())
    {
        return $this->getUrl('*/*/vendorgrid', array('_current'=>true));
    }


}