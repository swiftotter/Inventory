<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

class SwiftOtter_Inventory_Model_Resource_AdjustmentLog_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('SwiftOtter_Inventory/AdjustmentLog');
    }

    public function withAdditionalFields()
    {
        $this->_reset();
        $select = $this->getSelect();
        $select->reset($select::COLUMNS);


        $select->columns(array(
            'original_quantity',
            'current_quantity',
            'orders_affected',
            'items_affected',
            'created_at',
            'user_id',
            'product_id'
        ));

        $select->joinLeft(
            array('user' => $this->getTable('admin/user')),
            '`user`.user_id = `main_table`.user_id',
            array(
                new Zend_Db_Expr('CONCAT(`user`.username, \' (\', `user`.firstname, \' \', `user`.lastname, \')\') AS user_formatted_name')
            )
        );


        $productTable = $this->getTable('catalog/product');
        $select->joinLeft(
            array('product' => $productTable),
            '`main_table`.product_id = `product`.entity_id',
            array('sku')
        );

        $includeAttributes = array('name', 'vendor_id');
        foreach ($includeAttributes as $attributeCode) {
            $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeCode);
            if ($attribute->getId()) {
                $joinTable = sprintf('product_%s_table', $attributeCode);

                $select->joinLeft(
                    array($joinTable => $attribute->getBackendTable()),
                    sprintf('`main_table`.product_id = `%s`.entity_id AND `%s`.attribute_id = %s', $joinTable, $joinTable, $attribute->getAttributeId()),
                    array($attributeCode => new Zend_Db_Expr(sprintf('`%s`.value', $joinTable)))
                );
            }
        }

        return $this;
    }
}