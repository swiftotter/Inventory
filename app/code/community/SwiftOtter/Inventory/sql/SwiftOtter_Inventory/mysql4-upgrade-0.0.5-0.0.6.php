<?php

/** @var $installer Mage_Eav_Model_Entity_Setup*/
$installer = $this;
/**
 * Prepare database for install
 */
$installer->startSetup();

$installer->run("
    CREATE TABLE `{$this->getTable('SwiftOtter_Inventory/ReceivedBackorder')}`
      (
        id INT UNSIGNED AUTO_INCREMENT,
        created_at DATETIME,
        order_id INT UNSIGNED NOT NULL,
        order_item_id INT UNSIGNED NOT NULL,
        qty_received INT UNSIGNED NOT NULL,
        shipped TINYINT DEFAULT 0,
        shipment_item_id INT UNSIGNED,
        adjustment_id INT UNSIGNED NOT NULL,
        PRIMARY KEY (id),
        KEY `SALES_ORDER_ID` (`order_id`),
        CONSTRAINT `FK_SALES_ORDER_ID` FOREIGN KEY (`order_id`)
            REFERENCES `{$this->getTable('sales/order')}` (`entity_id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,

        KEY `SALES_ORDER_ITEM_ID` (`order_item_id`),
        CONSTRAINT `FK_SALES_ORDER_ITEM_ID` FOREIGN KEY (`order_item_id`)
            REFERENCES `{$this->getTable('sales/order_item')}` (`item_id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,

        KEY `SALES_SHIPMENT_ITEM_ID` (`shipment_item_id`),
        CONSTRAINT `FK_SALES_SHIPMENT_ITEM_ID` FOREIGN KEY (`shipment_item_id`)
            REFERENCES `{$this->getTable('sales/shipment_item')}` (`entity_id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,

        KEY `SWIFTOTTER_INVENTORY_ADJUSTMENT_LOG_ID` (`adjustment_id`),
        CONSTRAINT `FK_SWIFTOTTER_INVENTORY_ADJUSTMENT_LOG_ID` FOREIGN KEY (`adjustment_id`)
            REFERENCES `{$this->getTable('SwiftOtter_Inventory/AdjustmentLog')}` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
      );
");

/**
 * Prepare database after install
 */
$installer->endSetup();