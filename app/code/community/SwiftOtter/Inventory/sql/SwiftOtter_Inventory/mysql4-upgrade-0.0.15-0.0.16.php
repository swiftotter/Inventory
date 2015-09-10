<?php

/** @var $installer Mage_Eav_Model_Entity_Setup*/
$installer = $this;
/**
 * Prepare database for install
 */
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('SwiftOtter_Inventory/Vendor')}`
      ADD COLUMN reorder_period SMALLINT(3) NOT NULL DEFAULT 90,
      ADD COLUMN lead_time SMALLINT(3) NOT NULL DEFAULT 90;
");

/**
 * Prepare database after install
 */
$installer->endSetup();