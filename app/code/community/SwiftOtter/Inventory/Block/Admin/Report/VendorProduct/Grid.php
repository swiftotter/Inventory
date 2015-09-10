<?php

class SwiftOtter_Inventory_Block_Admin_Report_VendorProduct_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('vendor_product_report')
             ->setDefaultSort('name')
             ->setDefaultDir('asc')
             ->setUseAjax(true)
             ->setSaveParametersInSession(true)
             ->setCountTotals(true);
    }

    protected function getSelectedProducts() {

    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('SwiftOtter_Inventory/Report_VendorProduct_Collection')->initJoins();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _afterLoadCollection()
    {
        $collection = $this->getCollection();

        $total = 0;
        $qtyOnHand = 0;

        foreach ($collection as $row) {
            $row->setValue($row->getRawCost() * $row->getQtyOnHand());
            $row->setVendorId($row->getVendorId() > 0 ? (int)$row->getVendorId() : "");

            $total += $row->getValue();
            $qtyOnHand += $row->getQtyOnHand();
        }

        $this->setTotals(new Varien_Object(
            array (
                'value' => $total
            )
        ));

        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {
        $currencyCode = (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);

        $this->addColumn('sku', array(
            'header'	=> $this->__('SKU'),
            'align'		=> 'left',
            'index'		=> 'sku',
            'width'     => '70px'
        ));

        $this->addColumn('name', array(
            'header'	=> $this->__('Name'),
            'align'		=> 'left',
            'index'		=> 'name',
        ));

        $this->addColumn('vendor_id', array(
            'header'	=> $this->__('Vendor'),
            'align'		=> 'left',
            'index'		=> 'vendor_id',
            'type'      => 'options',
            'options'   => Mage::getModel('SwiftOtter_Inventory/Source_Vendor')->getAllOptions()
        ));

        $this->addColumn('raw_cost', array(
            'header'	=> $this->__('Cost'),
            'align'		=> 'center',
            'index'		=> 'raw_cost',
            'type'      => 'price',
            'currency_code' => $currencyCode,
            'validate_class' => 'raw_cost',
        ));

        $this->addColumn('price', array(
            'header'	=> $this->__('Price'),
            'align'		=> 'center',
            'index'		=> 'price',
            'type'      => 'price',
            'currency_code' => $currencyCode,
            'validate_class' => 'price'
        ));

        $this->addColumn('qty_on_hand', array(
            'header'	=> $this->__('QTY on Hand'),
            'align'		=> 'center',
            'index'		=> 'qty_on_hand',
            'type'      => 'number',
            'filter_index' => 'qty_on_hand'
        ));

        $this->addColumn('value', array(
            'header'	=> $this->__('Value'),
            'align'		=> 'center',
            'index'		=> 'value',
            'type'      => 'price',
            'currency_code' => $currencyCode,
            'filter'    => false
        ));

        return parent::_prepareColumns();
    }

    public function manageStockCallback ($value, $row)
    {
        if (!$row->getId()) {
            return $value;
        }

        if ($this->getManageStock($row)) {
            return $value;
        } else {
            return "-";
        }
    }

    protected function getManageStock ($row)
    {
        $stockFlag = Mage::helper('SwiftOtter_Inventory')->getConfigManageStock();
        return ($row->getManageStock() && !$row->getUseConfigManageStock()) || ($stockFlag && $row->getUseConfigManageStock());
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/catalog_product/view', array ('id' => $row->getId()));
    }

    public function getGridUrl($params = array())
    {
        return $this->getUrl('*/*/vendorproductgrid', array('_current'=>true));
    }


}