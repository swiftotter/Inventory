<?php

$installer = $this;

$installer->startSetup();
$installer->run("
    ALTER TABLE `{$this->getTable('cataloginventory/stock_item')}`
      ADD COLUMN reorder_point INT DEFAULT 100 NOT NULL;
");

$installer->addAttribute('catalog_product', 'vendor_id',
    array(
        'source' => 'SwiftOtter_Inventory/Source_Vendor',
        'type' => 'int',
        'grid' => true,
        'label' => 'Vendor',
        'input' => 'select',
        'required' => false,
        'used_in_product_listing' => true
    ));

$installer->addAttribute('catalog_product', 'raw_cost',
    array(
        'type' => 'decimal',
        'grid' => true,
        'label' => 'Product Raw Cost',
        'input' => 'text',
        'required' => false,
        'used_in_product_listing' => true
    ));

$installer->endSetup();