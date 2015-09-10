<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jmaxwell
 * Date: 7/19/13
 * Time: 6:44 PM
 * To change this template use File | Settings | File Templates.
 */

class SwiftOtter_Inventory_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CONFIG_SECTION = 'cataloginventory';
    const CONFIG_GROUP = 'management';

    const REPORT_REGISTRY_NODE = 'inventory_filter';

    protected $_configManageStock;

    public function getStoreConfig($field)
    {
        return Mage::getStoreConfig(self::CONFIG_SECTION . '/' . self::CONFIG_GROUP . '/' . $field);
    }

    public function getStoreConfigFlag($field)
    {
        return Mage::getStoreConfigFlag(self::CONFIG_SECTION . '/' . self::CONFIG_GROUP . '/' . $field);
    }

    public function getQtyOnHandThreshold()
    {
        return $this->getStoreConfig('ignore_quantity_threshold');
    }

    /**
     * @param Varien_Db_Select $select
     * @param string $alias
     * @return $this
     */
    public function addManageStockFilter($select, $alias)
    {
        if ($this->getConfigManageStock()) {
            $select->where("`{$alias}`.manage_stock = 1 OR `{$alias}`.use_config_manage_stock = 1");
        } else {
            $select->where("`{$alias}`.manage_stock = 1", 1);
        }

        return $this;
    }

    /**
     * @param Mage_Eav_Model_Resource_Entity_Attribute_Collection $collection
     * @return $this
     */
    public function addStockableProductFilter($collection)
    {
        $collection->addAttributeToFilter(
            'type_id',
            array('in' => array('simple', 'virtual'))
        );

        return $this;
    }

    /**
     * @return bool
     */
    public function getConfigManageStock()
    {
        return Mage::getStoreConfigFlag('cataloginventory/item_options/manage_stock');
    }

    /**
     * Retrieves all orders that have the specified product on backorder status
     *
     * @param Mage_Catalog_Model_Product|int $product
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    public function getPendingBackordersByProduct($product)
    {
        if (is_object($product)) {
            $product = $product->getId();
        }

        $orders = Mage::getResourceModel('sales/order_collection');
        $select = $orders->getSelect();
        $table = $orders->getTable('sales/order_item');

        $select->join(
            array('order_items' => $table),
            '`order_items`.order_id = `main_table`.entity_id AND `order_items`.qty_backordered > 0',
            array('qty_backordered_sum' => 'SUM(qty_backordered)')
        );

        $select->where('`order_items`.product_id = ?', $product);
        $select->group(array(
            new Zend_Db_Expr('`order_items`.order_id'))
        );

        return $orders;
    }

    /**
     * Retrieves all order items that correspond to the product specified and are backordered
     *
     * @param Mage_Catalog_Model_Product|int $product
     * @return Mage_Sales_Model_Resource_Order_Item_Collection
     */
    public function getPendingBackorderedItems($product)
    {
        if (is_object($product)) {
            $product = $product->getId();
        }

        $orderItems = Mage::getResourceModel('sales/order_item_collection');
        $orderItems
            ->addFieldToFilter('qty_backordered', array('gt' => 0))
            ->addFieldToFilter('product_id', array('eq' => $product->getId()));

        return $orderItems;
    }
}