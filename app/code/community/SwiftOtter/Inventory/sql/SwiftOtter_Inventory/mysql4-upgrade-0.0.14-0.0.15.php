<?php

/** @var $installer Mage_Eav_Model_Entity_Setup*/
$installer = $this;
/**
 * Prepare database for install
 */
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('cataloginventory/stock_item')}`
      ADD COLUMN qty_on_hand decimal(12,4) DEFAULT 0 NOT NULL AFTER qty;

    UPDATE `{$this->getTable('cataloginventory/stock_item')}` SET qty_on_hand = qty;
");

/**
 * Prepare database after install
 */
$installer->endSetup();