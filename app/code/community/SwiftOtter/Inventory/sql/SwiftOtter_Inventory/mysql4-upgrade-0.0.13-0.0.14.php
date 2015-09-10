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

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'enable_increment_product_qty',
	array(
		'type' => 'int',
		'grid' => true,
		'required' => false,
		'source' => 'eav/entity_attribute_source_boolean',
		'input' => 'select',
		'group' => 'General',
		'label' => 'Enable Increment Product Qty',
		'used_in_product_listing' => true
	));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'increment_product_id',
	array(
		'type' => 'int',
		'grid' => true,
		'required' => false,
		'source' => 'SwiftOtter_Inventory/Source_Product',
		'input' => 'select',
		'group' => 'General',
		'label' => 'Increment Product',
		'used_in_product_listing' => true
	));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'increment_product_qty',
	array(
		'type' => 'int',
		'grid' => true,
		'required' => false,
		'input' => 'text',
		'group' => 'General',
		'label' => 'Increment Product Quantity',
		'used_in_product_listing' => true
	));

$installer->endSetup();