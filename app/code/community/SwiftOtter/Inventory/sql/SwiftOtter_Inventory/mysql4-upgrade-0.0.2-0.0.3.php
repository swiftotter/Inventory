<?php

/** @var $installer Mage_Eav_Model_Entity_Setup*/
$installer = $this;
/**
 * Prepare database for install
 */
$installer->startSetup();

$installer->run("
    CREATE TABLE `{$this->getTable('SwiftOtter_Inventory/Vendor')}` (
      id INT auto_increment,
      account_number VARCHAR(15),
      abbrev VARCHAR(10),
      name VARCHAR(50) NOT NULL,
      notes TEXT,
      phone VARCHAR(10),
      fax VARCHAR(10),
      email VARCHAR(50),
      website VARCHAR(50),
      billing_company VARCHAR(50),
      billing_contact VARCHAR(50),
      billing_address_1 VARCHAR(50),
      billing_address_2 VARCHAR(50),
      billing_city VARCHAR(50),
      billing_postcode VARCHAR(20),
      billing_region VARCHAR(6),
      PRIMARY KEY (id)
    );
");

/**
 * Prepare database after install
 */
$installer->endSetup();