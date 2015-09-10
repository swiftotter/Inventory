<?php

/** @var $installer Mage_Eav_Model_Entity_Setup*/
$installer = $this;
/**
 * Prepare database for install
 */
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('SwiftOtter_Inventory/Vendor')}`
      ADD COLUMN drop_ship_alert TEXT;
");

/**
 * Prepare database after install
 */
$installer->endSetup();