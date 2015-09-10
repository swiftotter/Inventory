<?php

class SwiftOtter_Inventory_Block_Admin_Edit_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('inventory_grid')
             ->setDefaultSort('id')
             ->setDefaultDir('desc')
             ->setUseAjax(true)
             ->setSaveParametersInSession(true);
    }

    protected function getSelectedProducts() {

    }

    protected function _prepareCollection()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = Mage::getResourceModel('catalog/product_collection');

        $select = $collection->getSelect();
        $table = $collection->getTable('cataloginventory/stock_item');

        $collection->joinTable($table, 'product_id = entity_id', array(
            'qty_available' => 'qty',
            'qty_on_hand' => 'qty_on_hand',
            'is_in_stock' => 'is_in_stock',
            'reorder_point' => 'reorder_point'
        ));

        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('vendor_id');
        $collection->addAttributeToSelect('price');
        $collection->addAttributeToSelect('special_price');
        $collection->addAttributeToSelect('raw_cost');

        $collection->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));

        Mage::helper('SwiftOtter_Inventory')
            ->addManageStockFilter($select, 'cataloginventory_stock_item')
            ->addStockableProductFilter($collection);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();

        $this->getMassactionBlock()->addItem('save_items', array(
                'label' => $this->__('Placeholder (unused)'),
                'url' => ''
            )
        );

        return $this;
    }

    protected function _prepareColumns()
    {
        $renderers = array(
            'input_edit' => 'SwiftOtter_Inventory/Admin_Grid_Column_Renderer_InputEdit'
        );
        $this->setColumnRenderers($renderers);

        $editable = true;

        if ($editable) {
            $this->setMassactionIdField('entity_id');
        }

        $this->addColumn('sku', array(
            'header'	=> $this->__('SKU'),
            'align'		=> 'center',
            'index'		=> 'sku',
            'width'     => '70px'
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
            'width'     => '100',
            'options'   => Mage::getModel('SwiftOtter_Inventory/Source_Vendor')->getAllOptions()
        ));

        $this->addColumn('raw_cost', array(
            'header'	=> $this->__('Cost'),
            'align'		=> 'center',
            'index'		=> 'raw_cost',
            'type'      => 'number',
            'editable'  => $editable,
            'validate_class' => 'raw_cost',
        ));

        $this->addColumn('price', array(
            'header'	=> $this->__('Price'),
            'align'		=> 'center',
            'index'		=> 'price',
            'type'      => 'number',
            'editable'  => $editable,
            'validate_class' => 'price'
        ));

        $this->addColumn('special_price', array(
            'header'	=> $this->__('Special Price'),
            'align'		=> 'center',
            'index'		=> 'special_price',
            'type'      => 'number',
            'editable'  => $editable,
            'validate_class' => 'special_price'
        ));

        $this->addColumn('qty_available', array(
            'header'	=> $this->__('QTY AVL'),
            'align'		=> 'center',
            'index'		=> 'qty_available',
            'type'      => 'input_edit',
            'width'     => '45',
            'editable'  => false,
            'validate_class' => 'qty_available',
        ));

        $this->addColumn('qty_on_hand', array(
            'header'	=> $this->__('QTY on Hand'),
            'align'		=> 'center',
            'index'		=> 'qty_on_hand',
            'type'      => 'input_edit',
            'editable'  => $editable,
            'validate_class' => 'qty_on_hand',
        ));


        $this->addColumn('qty_adjustment', array(
            'header'	=> $this->__('QTY Adjustment'),
            'align'		=> 'center',
            'index'		=> 'qty_adjustment',
            'type'      => 'number',
            'editable'  => $editable,
            'filter'    => false,
            'sortable'  => false,
            'validate_class' => 'qty_adjustment',
        ));


        $this->addColumn('reorder_point', array(
            'header'	=> $this->__('Reorder Point'),
            'align'		=> 'center',
            'type'      => 'number',
            'index'		=> 'reorder_point',
            'editable'  => true,
            'validate_class' => 'reorder_point'
        ));

        return parent::_prepareColumns();
    }


    public function getGridUrl($params = array())
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }


}