<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 10/24/2013
 * @package default
 **/

class SwiftOtter_Inventory_Model_Resource_Report_VendorProduct_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    const ENTITY_TYPE = 'catalog_product';
    const VENDOR_ATTRIBUTE_CODE = 'vendor_id';
    const COST_ATTRIBUTE_CODE = 'raw_cost';

    protected function _construct()
    {
        parent::_construct();

        return $this;
    }

    public function initJoins()
    {
//        $qtyOnHand = 'qty_on_hand';
//        $threshold = Mage::helper('SwiftOtter_Inventory')->getQtyOnHandThreshold();
//        if ((int)$threshold > 0) {
//            $qtyOnHand = new Zend_Db_Expr(sprintf('IF(qty_on_hand < %s, qty_on_hand, 0)', $threshold));
//        }

        $threshold = Mage::helper('SwiftOtter_Inventory')->getQtyOnHandThreshold();
        $joinCond = '';
        if ((int)$threshold > 0) {
            $joinCond = new Zend_Db_Expr(sprintf('qty_on_hand < %s', $threshold));
        }

        $table = $this->getTable('cataloginventory/stock_item');
        $this->joinTable($table,
            'product_id = entity_id',
            array(
                'qty_on_hand' => 'qty_on_hand',
                'is_in_stock' => 'is_in_stock',
                'manage_stock' => 'manage_stock',
                'use_config_manage_stock' => 'use_config_manage_stock'
            ),
            $joinCond,
            'left'
        );

        $this->addAttributeToSelect('id');
        $this->addAttributeToSelect('sku');
        $this->addAttributeToSelect('name');
        $this->addAttributeToSelect('vendor_id');
        $this->addAttributeToSelect('price');
        $this->addAttributeToSelect('raw_cost');

        $this->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));

        Mage::helper('SwiftOtter_Inventory')->addStockableProductFilter($this);

//        echo (string)$this->getSelect();die();

        return $this;
    }

    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $sql = (string)$this->getSelect();

        $select = $this->getConnection()->select();
        $select->from(
            new Zend_Db_Expr(sprintf('(%s)', $sql))
        )->reset($select::COLUMNS);

        $select->columns('COUNT(1)');

        return $select;
    }
}