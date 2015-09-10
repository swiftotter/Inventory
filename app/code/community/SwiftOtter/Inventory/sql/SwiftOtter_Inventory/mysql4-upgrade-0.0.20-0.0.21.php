<?php

/** @var $installer Mage_Eav_Model_Entity_Setup*/
$installer = $this;
/**
 * Prepare database for install
 */
$installer->startSetup();

if (!$installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'vendor_sku')) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'vendor_sku', array(
        'type'      => 'varchar',
        'input'     => 'text',
        'label'     => 'Vendor SKU',
        'required'  => false,
        'used_in_product_list' => false
    ));
}

/**
 * Prepare database after install
 */
$installer->endSetup();