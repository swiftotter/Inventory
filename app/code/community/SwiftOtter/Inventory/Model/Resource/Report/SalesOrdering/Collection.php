<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

class SwiftOtter_Inventory_Model_Resource_Report_SalesOrdering_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_baseCollection;

    public function _construct()
    {
        $this->_init('SwiftOtter_Inventory/Vendor');
    }

    protected function _renderOrders()
    {
        $output = parent::_renderOrders();

        $this->getSelect()->order('sku');

        return $output;
    }


    public function getReportCollection(DateTime $from = null, DateTime $to = null, $numberOfDays = null)
    {
        $select = $this->getSelect();
        $this->_initializeQuery();

        $utcTimezone = new DateTimeZone('UTC');

        $attributeCodes = array(
            'sku',
            'status',
            'name',
            'vendor_id',
            'case_pack_quantity'
        );
        $this->_loadAttributes($attributeCodes);

        $table = $this->getTable('cataloginventory/stock_item');
        $select->joinLeft(
            array('inventory' => $table),
            '`inventory`.product_id = `e`.entity_id',
            array(
                'qty' => 'MAX(inventory.qty)',
                'qty_on_hand' => 'MAX(qty_on_hand)',
                'reorder_point' => 'reorder_point'
            )
        );

        $connection = Mage::getResourceModel('core/resource')->getReadConnection();

        if (Mage::helper('core')->isModuleEnabled('SwiftOtter_Sales')) {
            $conditions = array(
                '`order_product`.product_id = `e`.entity_id'
            );

            $orderedProductsColumns = array('qty_ordered' => 'SUM(`order_product`.qty)');

            if ($from && $to) {
                $days = $from->diff($to, true)->days;

                if (!$numberOfDays) {
                    $numberOfDays = 'IF(`vendor`.lead_time > 0, `vendor`.lead_time, 0)';
                }

                $orderedProductsColumns = array_merge($orderedProductsColumns, array(
                    'daily_sales_average' => new Zend_Db_Expr(sprintf('SUM(`order_product`.qty) / %s', $days)),

                    'reorder_left' => new Zend_Db_Expr(sprintf('ROUND((MAX(`inventory`.qty) - `reorder_point`) / (SUM(`order_product`.`qty`) / %s)) - %s', $days, $numberOfDays)),

                    'to_order' => new Zend_Db_Expr(sprintf(
                        'ROUND(((SUM(`order_product`.`qty`) / %s) * (`vendor`.lead_time + %s)) + `inventory`.reorder_point - IF(`inventory`.qty > 0, `inventory`.qty, 0), 0)',
                        $days, $numberOfDays)),

                    'to_order_case' => new Zend_Db_Expr('IF(MAX(`inventory`.qty) > 0, MAX(`inventory`.qty), 0)'),

                    'to_stockout' => new Zend_Db_Expr(sprintf('ROUND(`inventory`.qty / (SUM(`order_product`.qty) / %s))', $days))
                ));


                $conditions[] = $connection->quoteInto('`order_product`.sale_date >= ?', $from->setTimezone($utcTimezone)->format('Y-m-d h:i:s'));
                $conditions[] = $connection->quoteInto('`order_product`.sale_date < ?', $to->setTimezone($utcTimezone)->format('Y-m-d h:i:s'));
            }

            $select->joinLeft(
                array('order_product' => $this->getTable('SwiftOtter_Sales/Order_Product')),
                implode(' AND ', $conditions),
                $orderedProductsColumns
            );
        } else {
            $conditions = array(
                '`ordered_products`.product_id = `e`.entity_id'
            );

            $orderedProductsColumns = array('qty_ordered' => 'SUM(qty_ordered)', 'total_value' => 'SUM(row_total)');

            if ($from && $to) {
                $days = $from->diff($to, true)->days;

                if (!$numberOfDays) {
                    $numberOfDays = 'IF(`vendor`.reorder_period > 0, `vendor`.reorder_period, 0)';
                }

                $orderedProductsColumns = array_merge($orderedProductsColumns, array(
                    'daily_sales_average' => new Zend_Db_Expr(sprintf('SUM(`ordered_products`.qty_ordered) / %s', $days)),
                    'reorder_left' => new Zend_Db_Expr(sprintf('ROUND((SUM(`inventory`.qty) - `reorder_point`) / (SUM(`ordered_products`.qty_ordered) / %s))', $days)),
                    'to_order' => new Zend_Db_Expr(sprintf('ROUND(((SUM(`qty_ordered`) / %s) * (`vendor`.lead_time + %s)) + `inventory`.reorder_point - IF(`inventory`.qty > 0, `inventory`.qty, 0), 0)', $days, $numberOfDays)),
                    'to_order_case' => new Zend_Db_Expr('IF(MAX(`inventory`.qty) > 0, MAX(`inventory`.qty), 0)'),
                    'to_stockout' => new Zend_Db_Expr(sprintf('ROUND(`inventory`.qty / (SUM(`ordered_products`.qty_ordered) / %s))', $days))
                ));
            }

            $select->joinLeft(
                array('ordered_products' => $this->getTable('sales/order_item')),
                implode(' AND ', $conditions),
                $orderedProductsColumns
            );

            $orderConditions = array(
                'ordered_products.order_id = order.entity_id'
            );
            if ($from && $to) {
                $orderConditions[] = $connection->quoteInto('`order`.created_at >= ?', $from->setTimezone($utcTimezone)->format('Y-m-d h:i:s'));
                $orderConditions[] = $connection->quoteInto('`order`.created_at <= ?', $to->setTimezone($utcTimezone)->format('Y-m-d h:i:s'));
                $orderConditions[] = sprintf('`order`.state NOT IN("%s")', implode('","', array(
                        Mage_Sales_Model_Order::STATE_CANCELED,
                        Mage_Sales_Model_Order::STATE_CLOSED)
                ));
            }

            $select->joinLeft(
                array('order' => $this->getTable('sales/order')),
                implode(' AND ', $orderConditions),
                array()
            );
        }

        $select->joinLeft(
            array('vendor' => $this->getTable('SwiftOtter_Inventory/Vendor')),
            '`vendor`.id = `at_vendor_id`.value',
            array(
                'lead_time' => 'vendor.lead_time',
                'reorder_period' => 'vendor.reorder_period'
            )
        );


        $select->where(sprintf("`e`.type_id IN ('%s')", implode("','", array(
            Mage_Catalog_Model_Product_Type::TYPE_SIMPLE
        ))));

        $select->where('at_status.value = ?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

        $select->group(array(
           new Zend_Db_Expr('`e`.entity_id')
        ));

        return $this;
    }

    protected function _initializeQuery()
    {
        $select = $this->getSelect();
        $select->reset();

        $productTable = $this->getTable('catalog/product');
        if ($this->_isFlatEnabled()) {
            $productTable = $this->_getBaseCollection()->getEntity()->getFlatTableName();
        }

        $select->from(array(
            'e' => $productTable
        ));

        return $this;
    }

    protected function _loadAttributes($attributeCodes)
    {
        $select = $this->getSelect();
        Mage::helper('SwiftOtter_Base/Db')->includeProductAttribute($attributeCodes, $select, 'e', null, 'at_%s', 'catalog/product_collection');

        return $this;
    }

    protected function _isFlatEnabled()
    {
        return $this->_getBaseCollection()->isEnabledFlat();
    }

    protected function _getBaseCollection()
    {
        if (!$this->_baseCollection) {
            $this->_baseCollection = Mage::getResourceModel('catalog/product_collection');
        }

        return $this->_baseCollection;
    }

    protected function _getAttributeFieldName($attributeCode)
    {
        if (isset($this->_joinFields[$attributeCode])) {
            return $attributeCode;
        } else {
            return parent::_getAttributeFieldName($attributeCode);
        }
    }

    /**
     * @param Varien_Db_Select $select
     * @param bool $resetLeftJoins
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);

        $countSelect->columns('COUNT(e.entity_id)');

        $outputSelect = clone $this->getSelect();
        $outputSelect->reset();
        $outputSelect->from(new Zend_Db_Expr(sprintf('(%s)', (string)$countSelect)), 'COUNT(*)');

        return $outputSelect;
    }
}