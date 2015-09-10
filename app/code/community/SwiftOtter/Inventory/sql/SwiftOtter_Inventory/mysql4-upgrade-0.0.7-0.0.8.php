<?php

/** @var $installer Mage_Eav_Model_Entity_Setup*/
$installer = $this;
/**
 * Prepare database for install
 */
$installer->startSetup();

$installer->run("
    CREATE TABLE `{$this->getTable('SwiftOtter_Inventory/AdjustmentLog')}`
      (
        id INT UNSIGNED AUTO_INCREMENT,
        product_id INT UNSIGNED NOT NULL,
        original_quantity INT NOT NULL,
        current_quantity INT NOT NULL,
        orders_affected INT UNSIGNED DEFAULT 0,
        items_affected INT UNSIGNED DEFAULT 0,
        user_id INT UNSIGNED NOT NULL,
        created_at DATETIME,
        PRIMARY KEY (id),
        KEY `CATALOG_PRODUCT_ENTITY_ID` (`product_id`),
        CONSTRAINT `FK_CATALOG_PRODUCT_ENTITY_ID` FOREIGN KEY (`product_id`)
            REFERENCES `{$this->getTable('catalog/product')}` (`entity_id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,

        KEY `ADMIN_USER_USER_ID` (`user_id`),
        CONSTRAINT `FK_ADMIN_USER_USER_ID` FOREIGN KEY (`user_id`)
            REFERENCES `{$this->getTable('admin/user')}` (`user_id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
      );
");

/**
 * Prepare database after install
 */
$installer->endSetup();