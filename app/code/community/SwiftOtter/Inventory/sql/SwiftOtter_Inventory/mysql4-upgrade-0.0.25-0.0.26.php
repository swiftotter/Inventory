<?php

/** @var $installer Mage_Eav_Model_Entity_Setup*/
$installer = $this;
/**
 * Prepare database for install
 */
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('SwiftOtter_Inventory/Vendor')}`
      CHANGE COLUMN email email_order VARCHAR(100),
      ADD COLUMN email_media VARCHAR(100),
      ADD COLUMN email_returns VARCHAR(100),
      CHANGE COLUMN phone phone_order VARCHAR(20),
      ADD COLUMN phone_media VARCHAR(20),
      ADD COLUMN phone_returns VARCHAR(20),
      ADD COLUMN third_party_selling TINYINT(1) DEFAULT 0;
");



/**
 * Prepare database after install
 */
$installer->endSetup();