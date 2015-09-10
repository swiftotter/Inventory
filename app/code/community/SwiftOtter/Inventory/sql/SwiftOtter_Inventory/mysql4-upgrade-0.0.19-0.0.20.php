<?php

/** @var $installer Mage_Eav_Model_Entity_Setup*/
$installer = $this;
/**
 * Prepare database for install
 */
$installer->startSetup();

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'disable_backorder', array(
    'type'      => 'int',
    'input'     => 'boolean',
    'label'     => 'Disable Backorder',
    'required'  => false,
    'source'    => 'eav/entity_attribute_source_boolean',
    'used_in_product_list' => false,
    'is_configurable' => false,
));

if (!$installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'drop_shipped')) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'drop_shipped', array(
        'type'      => 'int',
        'input'     => 'boolean',
        'label'     => 'Drop Shipped',
        'required'  => false,
        'source'    => 'eav/entity_attribute_source_boolean',
        'used_in_product_list' => false,
        'is_configurable' => false,
    ));
}

/**
 * Prepare database after install
 */
$installer->endSetup();