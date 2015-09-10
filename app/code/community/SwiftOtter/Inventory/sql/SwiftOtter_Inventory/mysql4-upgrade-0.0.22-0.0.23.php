<?php

/** @var $installer Mage_Eav_Model_Entity_Setup*/
$installer = $this;
/**
 * Prepare database for install
 */
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('sales/order_item')}`
      ADD COLUMN ship_separately TINYINT(1) DEFAULT 0;
");

if (!$installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'ship_separately')) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'ship_separately', array(
        'type'      => 'int',
        'input'     => 'select',
        'source'    => 'eav/entity_attribute_source_boolean',
        'label'     => 'Ship Separately',
        'required'  => false,
        'used_in_product_list' => false,
        'apply_to'  => 'kit'
    ));
}

/**
 * Prepare database after install
 */
$installer->endSetup();