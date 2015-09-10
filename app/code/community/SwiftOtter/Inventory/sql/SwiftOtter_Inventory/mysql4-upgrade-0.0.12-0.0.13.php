<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/28/14
 * @package default
 **/

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'reverse_inventory_decrement',
	array(
		'type' => 'int',
		'grid' => true,
		'required' => false,
		'source' => 'eav/entity_attribute_source_boolean',
		'input' => 'select',
		'group' => 'General',
		'label' => 'Reverse Sale Inventory Decrement',
		'used_in_product_listing' => true
	));

$installer->endSetup();