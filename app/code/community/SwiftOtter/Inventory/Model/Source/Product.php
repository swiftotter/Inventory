<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

class SwiftOtter_Inventory_Model_Source_Product extends SwiftOtter_Base_Model_Source_Abstract
{
    public function getAllOptions()
    {
        $products = Mage::getResourceModel('catalog/product_collection')
			->addAttributeToSelect('name')
			->setOrder('sku');

		$products->joinTable(
			array('inventory' => $products->getTable('cataloginventory/stock_item')),
			'product_id = entity_id',
			array('manage_stock')
		);

		Mage::helper('SwiftOtter_Inventory')->addManageStockFilter($products->getSelect(), 'inventory');

        $output = array('' => Mage::helper('SwiftOtter_Inventory')->__(' -- No selection -- '));

		/** @var Mage_Catalog_Model_Product $product */
        foreach ($products as $product) {
            $output[$product->getId()] = sprintf('%s - %s', $product->getSku(), $product->getName());
        }

        return $output;
    }

	public function getFlatColums()
	{
		$attributeCode = $this->getAttribute()->getAttributeCode();

		$column = array(
			'unsigned'  => false,
			'default'   => null,
			'extra'     => null
		);

		if (Mage::helper('core')->useDbCompatibleMode()) {
			$column['type']     = 'int';
			$column['is_null']  = true;
		} else {
			$column['type']     = Varien_Db_Ddl_Table::TYPE_INTEGER;
			$column['nullable'] = true;
		}

		return array(
			$attributeCode => $column
		);
	}

	/**
	 * Retrieve Select for update Attribute value in flat table
	 *
	 * @param   int $store
	 * @return  Varien_Db_Select|null
	 */
	public function getFlatUpdateSelect($store)
	{
		return Mage::getResourceModel('eav/entity_attribute_option')
			->getFlatUpdateSelect($this->getAttribute(), $store, false);
	}


}