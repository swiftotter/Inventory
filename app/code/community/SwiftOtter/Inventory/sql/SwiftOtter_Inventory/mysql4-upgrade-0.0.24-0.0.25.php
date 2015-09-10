<?php

/** @var $installer Mage_Eav_Model_Entity_Setup*/
$installer = $this;
/**
 * Prepare database for install
 */
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('SwiftOtter_Inventory/Vendor')}`
      CHANGE COLUMN phone phone VARCHAR(20);
");

/**
 * Prepare database after install
 */
$installer->endSetup();