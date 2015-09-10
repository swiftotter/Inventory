<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 8/10/13
 * @package default
 **/


/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_product', 'internal_stock_update_freq',
    array(
        'type' => 'int',
        'grid' => true,
        'label' => 'Internal Stock Update Frequency',
        'input' => 'text',
        'required' => false,
        'used_in_product_listing' => true
    ));

$installer->addAttribute('catalog_product', 'customer_stock_update_freq',
    array(
        'type' => 'int',
        'grid' => true,
        'label' => 'Customer Stock Update Frequency',
        'input' => 'text',
        'required' => false,
        'used_in_product_listing' => true
    ));

$installer->endSetup();

