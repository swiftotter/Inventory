<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

class SwiftOtter_Inventory_Helper_Adjustment extends Mage_Core_Helper_Abstract
{
    protected $_silent = false;


    const INVENTORY_MASS_EDIT_BEFORE_UPDATE = 'swiftotter_inventory_mass_edit_before_update';
    const INVENTORY_MASS_EDIT_AFTER_UPDATE = 'swiftotter_inventory_mass_edit_after_update';

    /**
     * Updates product attributes and inventory columns with data in the format of:
     * array('PRODUCT_ID' => array('Attribute_Name' => 'Attribute_Value'))
     *
     * @param $items
     */
    public function updateInventory($items)
    {
        if (!$this->getSilent()) {
            Mage::dispatchEvent(self::INVENTORY_MASS_EDIT_BEFORE_UPDATE, array(
                'items' => $items
            ));
        }

        $productAttributes = $this->_getProductAttributesToUpdate();
        $productIds = array_keys($items);

        $products = Mage::getResourceModel('catalog/product_collection');
        $products->addAttributeToFilter('entity_id', array('in' => $productIds));

        Mage::getResourceSingleton('catalog/product')->beginTransaction();
        /** @var $product Mage_Catalog_Model_Product */
        foreach($products as $product) {
            $values = $items[$product->getId()];

            if (is_object($values)) {
                // Making sure that we are always working with an array
                $values = get_object_vars($values);
            }
            foreach($productAttributes as $attribute) {
                if ($values[$attribute]) {
                    $product->setData($attribute, $values[$attribute]);
                    $product->getResource()->saveAttribute($product, $attribute);
                }
            }
        }

        Mage::getResourceSingleton('catalog/product')->commit();

        Mage::getResourceSingleton('cataloginventory/stock_item')->beginTransaction();
        /** @var $product Mage_Catalog_Model_Product */
        foreach ($products as $product) {
            $stock = Mage::getModel('cataloginventory/stock_item')->load($product->getId(), 'product_id');
            $stock->setProcessIndexEvents(false);

            $values = $items[$product->getId()];

            if (is_object($values)) {
                // Making sure that we are always working with an array
                $values = get_object_vars($values);
            }

            if ($values['qty_on_hand'] == $values['original_qty_on_hand'] && $values['qty_adjustment']) {
                $values['qty_on_hand'] = $stock->getQtyOnHand() + $values['qty_adjustment'];
            } else {
                $difference = ($values['qty_on_hand'] - $values['original_qty_on_hand']);
                $values['qty_on_hand'] = $stock->getQtyOnHand() + $difference;
            }

            unset($values['qty_in_stock']);
            unset($values['original_qty_in_stock']);

            foreach ($values as $key => $value) {
                if (!in_array($key, $productAttributes)) {
                    $stock->setData($key, $value);
                }
            }

            $stock->save();
        }

        Mage::getResourceSingleton('cataloginventory/stock_item')->commit();

        if (!$this->getSilent()) {
            Mage::dispatchEvent(self::INVENTORY_MASS_EDIT_AFTER_UPDATE, array());
        }

        Mage::getSingleton('index/indexer')->indexEvents(
            Mage_CatalogInventory_Model_Stock_Item::ENTITY,
            Mage_Index_Model_Event::TYPE_SAVE
        );
    }

    /**
     * @param bool $silent
     */
    public function setSilent($silent)
    {
        $this->_silent = (bool)$silent;
    }

    /**
     * @return bool
     */
    public function getSilent()
    {
        return (bool)$this->_silent;
    }




    protected function _getProductAttributesToUpdate()
    {
        return array('raw_cost', 'price', 'special_price');
    }

}