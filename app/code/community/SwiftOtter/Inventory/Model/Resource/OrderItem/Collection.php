<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 08/10/2013
 * @package default
 **/

class SwiftOtter_Inventory_Model_Resource_OrderItem_Collection extends Mage_Sales_Model_Resource_Order_Item_Collection
{
    /**
     * Gets the total number of backordered items for the specified order
     *
     * @param $order
     * @return int
     */
    public function getTotalBackorderedItemsByOrder(Mage_Sales_Model_order $order)
    {
        $connection = $this->getConnection();
        $select = $connection->select();

        $select->from($this->getMainTable())
            ->reset($select::COLUMNS)
            ->columns(array(
                new Zend_Db_Expr('SUM(qty_backordered) AS quantity')
            ))
            ->where('order_id = ?', $order->getId())
            ->group('order_id');

        return (int)$connection->fetchOne($select);
    }


    /**
     * Gets the total number of backordered items for the specific product id
     *
     * @param $productId
     * @return int
     */
    public function getTotalBackorderedItems($productId)
    {
        $connection = $this->getConnection();
        $select = new Varien_Db_Select($connection);

        $select
            ->from($this->getMainTable())
            ->columns(array(
                new Zend_Db_Expr('SUM(qty_backordered) AS quantity')
            ))
            ->where('product_id = ?', $productId)
            ->group('product_id');

        return (int)$connection->fetchOne($select);
    }

    /**
     * Gets the window of backordered items (first item's backorder to the last item's backorder)
     *
     * @param $productId
     * @return Varien_Object
     */
    public function getBackorderedItemsWindow($productId)
    {
        $connection = $this->getConnection();
        $select = new Varien_Db_Select($connection);

        $select->from($this->getMainTable())
            ->joinInner(array('order' => $this->getTable('sales/order')), 'order_id = entity_id', array())
            ->columns(array(
                new Zend_Db_Expr('MIN(order.created_at) AS start'),
                new Zend_Db_Expr('MAX(order.created_at) AS end')
            ))
            ->where('product_id = ?', $productId)
            ->group(array(
               'product_id'
        ));

        return new Varien_Object($connection->fetchRow($select));
    }

    /**
     * Gets the filtered list of backordered items by product id
     *
     * @param $productId
     * @return $this
     */
    public function getBackorderedItems($productId)
    {
        $this->_reset();

        $this->getSelect()->joinInner(array('order' => $this->getTable('sales/order')), 'order_id = entity_id', array());
        $this->getSelect()->where('order.state <> ?', Mage_Sales_Model_Order::STATE_CANCELED);

        $this->addFieldToFilter('product_id', array('eq' => $productId));
        $this->addFieldToFilter('qty_backordered', array('gt' => 0));
        $this->setOrder('order.created_at', self::SORT_ORDER_ASC);

        return $this;
    }
}