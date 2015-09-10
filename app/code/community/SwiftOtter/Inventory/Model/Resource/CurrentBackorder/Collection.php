<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 08/10/2013
 * @package default
 **/

class SwiftOtter_Inventory_Model_Resource_CurrentBackorder_Collection extends Mage_Sales_Model_Resource_Order_Collection
{
    const ORDER_STATE_KNOWN_FRAUD = 'known_fraud';

    public function _construct()
    {
        $this->_init('sales/order_item');
    }

    /**
     * Retrieves all open back orders
     *
     * @return $this
     */
    public function retrieveBackorders()
    {
        $this->_reset();
        $adapter = $this->getConnection();
        $select = $this->getSelect();
        $select->reset($select::COLUMNS);


        $select->columns(array(
            'product_type',
            'qty_ordered',
            'qty_invoiced',
            'qty_shipped',
            'qty_backordered',
            'price',
            'order_id',
            'product_id',
            new Zend_Db_Expr('DATEDIFF(`order`.created_at, NOW()) AS days_waiting')
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

        $productTable = $this->getTable('catalog/product');
        $select->joinLeft(
            array('product' => $productTable),
            '`main_table`.product_id = `product`.entity_id',
            array('sku')
        );

        $includeAttributes = array('name', 'eta', 'vendor_id');
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

        $select->where('qty_backordered > 0')
//            ->where(sprintf('product.type_id NOT LIKE ?', '%configurable%'))
            ->where(sprintf('order.state NOT IN (\'%s\')', implode("','", array(
                Mage_Sales_Model_Order::STATE_COMPLETE,
                Mage_Sales_Model_Order::STATE_CANCELED,
                Mage_Sales_Model_Order::STATE_CLOSED,
                self::ORDER_STATE_KNOWN_FRAUD
            ))));

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

// 03772536833833