<?php

/** @var $installer Mage_Eav_Model_Entity_Setup*/
$installer = $this;
/**
 * Prepare database for install
 */
$installer->startSetup();

$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'disable_backorder');

/**
 * Prepare database after install
 */
$installer->endSetup();