<?php

/** @var $installer Mage_Eav_Model_Entity_Setup*/
$installer = $this;
/**
 * Prepare database for install
 */
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('sales/order_item')}`
      ADD COLUMN drop_shipped TINYINT(1) DEFAULT 0;
");

/**
 * Prepare database after install
 */
$installer->endSetup();