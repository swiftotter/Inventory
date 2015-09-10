<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 10/24/2013
 * @package default
 **/

class SwiftOtter_Inventory_Model_Resource_Report_InventoryVendor_Collection extends SwiftOtter_Inventory_Model_Resource_Vendor_Collection
{
    const ENTITY_TYPE = 'catalog_product';
    const VENDOR_ATTRIBUTE_CODE = 'vendor_id';
    const COST_ATTRIBUTE_CODE = 'raw_cost';
    const STATUS_ATTRIBUTE_CODE = 'status';

    protected function _construct()
    {
        $this->_init('SwiftOtter_Inventory/Vendor');
    }

    public function loadJoins()
    {
        $select = $this->getSelect();
        $select->reset(Varien_Db_Select::COLUMNS);

        $subquery = $this->_loadSubquery();

        $select->joinLeft(array(
                'totals' => new Zend_Db_Expr(sprintf('(%s)', (string)$subquery))
            ),
            'vendor_id = id',
            array(
                'qty_on_hand' => new Zend_Db_Expr('SUM(`totals`.`qty_on_hand`)'),
                'total_value_on_hand' => new Zend_Db_Expr('SUM(`totals`.`total_value_on_hand`)')
            )
        );

        $select->columns('name')
            ->columns('id');

        $select->group(new Zend_Db_Expr('`main_table`.id'));

        return $this;
    }

    protected function _loadSubquery()
    {
        $select = clone $this->getSelect();
        $select->reset();

        $select->from(array(
            'main_table' => $this->getTable('catalog/product')
        ));

        Mage::helper('SwiftOtter_Base/Db')->includeProductAttribute(array(
            self::VENDOR_ATTRIBUTE_CODE,
            self::COST_ATTRIBUTE_CODE,
            self::STATUS_ATTRIBUTE_CODE
        ), $select);

        $joinCond = '';
        $threshold = Mage::helper('SwiftOtter_Inventory')->getQtyOnHandThreshold();
        if ((int)$threshold > 0) {
            $joinCond = new Zend_Db_Expr(sprintf(' AND qty_on_hand < %s', $threshold));
        }

        $stockItemTable = $this->getTable('cataloginventory/stock_item');
        $select->join(
            array('inventory' => $stockItemTable),
            '`inventory`.product_id = `main_table`.entity_id' . $joinCond,
            array('qty_on_hand' => 'qty_on_hand')
        );

        $select->where("main_table.type_id = ?", Mage_Catalog_Model_Product_Type::TYPE_SIMPLE);
        $select->where(sprintf('product_%s_table.value = ?', self::STATUS_ATTRIBUTE_CODE), Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

        $costTable = sprintf('product_%s_table', self::COST_ATTRIBUTE_CODE);

        $select->columns(array('total_value_on_hand' => new Zend_Db_Expr(sprintf('`%s`.`value` * qty_on_hand', $costTable))));

        return $select;
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