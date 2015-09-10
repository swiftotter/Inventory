<?php

/** @var $installer Mage_Eav_Model_Entity_Setup*/
$installer = $this;
/**
 * Prepare database for install
 */
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('cataloginventory/stock_item')}`
      ADD COLUMN need_to_alert TINYINT DEFAULT 0 NOT NULL;
");

/**
 * Prepare database after install
 */
$installer->endSetup();