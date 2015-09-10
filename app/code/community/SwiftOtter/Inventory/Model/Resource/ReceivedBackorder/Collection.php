<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

class SwiftOtter_Inventory_Model_Resource_ReceivedBackorder_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('SwiftOtter_Inventory/ReceivedBackorder');
    }

    /**
     * Retrieves all open back orders
     *
     * @return $this
     */
    public function retrieveShippableBackorders()
    {
        $this->_reset();
        $select = $this->getSelect();
        $select->reset($select::COLUMNS);


        $select->columns(array(
            'qty_received',
            'created_at',
            'order_id',
            'order_item_id',
            'shipped',
            'shipment_item_id'
        ));

        $select->joinLeft(
            array('order' => $this->getTable('sales/order')),
            '`order`.entity_id = `main_table`.order_id',
            array(
                'order_increment_id' => 'increment_id',
                'order_date' => 'created_at',
                new Zend_Db_Expr('CONCAT(`order`.customer_firstname, \' \', `order`.customer_lastname) AS customer_name')
            )
        );

        $select->joinLeft(
            array('order_item' => $this->getTable('sales/order_item')),
            '`order_item`.item_id = `main_table`.order_item_id',
            array(
                'qty_ordered' => 'qty_ordered',
                new Zend_Db_Expr('`order_item`.qty_ordered - `order_item`.qty_shipped AS qty_to_ship')
            )
        );

        $productTable = $this->getTable('catalog/product');
        $select->joinLeft(
            array('product' => $productTable),
            '`order_item`.product_id = `product`.entity_id',
            array('sku')
        );

        $includeAttributes = array('name', 'eta', 'vendor_id');
        foreach ($includeAttributes as $attributeCode) {
            $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeCode);
            if ($attribute->getId()) {
                $joinTable = sprintf('product_%s_table', $attributeCode);

                $select->joinLeft(
                    array($joinTable => $attribute->getBackendTable()),
                    sprintf('`order_item`.product_id = `%s`.entity_id AND `%s`.attribute_id = %s', $joinTable, $joinTable, $attribute->getAttributeId()),
                    array($attributeCode => new Zend_Db_Expr(sprintf('`%s`.value', $joinTable)))
                );
            }
        }

        $select->where('order.state <> ?', Mage_Sales_Model_Order::STATE_CANCELED);

        return $this;
    }

    public function addDateFilter(DateTime $from, DateTime $to)
    {
        $select = $this->getSelect();

        $utcTimezone = new DateTimeZone('UTC');

        $select->where('`order`.created_at >= ?', $from->setTimezone($utcTimezone)->format('Y-m-d h:i:s'));
        $select->where('`order`.created_at <= ?', $to->setTimezone($utcTimezone)->format('Y-m-d h:i:s'));

        return $this;
    }
}