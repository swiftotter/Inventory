<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 8/9/13
 * @package default
 **/


/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('order', 'has_backordered_items', array('type' => 'int', 'grid' => true, 'input' => 'checkbox'));
$installer->getConnection()->addColumn($installer->getTable('sales_flat_order'), 'has_backordered_items', 'tinyint');

$installer->endSetup();

