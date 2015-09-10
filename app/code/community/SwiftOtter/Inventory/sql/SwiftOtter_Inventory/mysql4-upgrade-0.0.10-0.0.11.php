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

$installer->removeAttribute('catalog_product', 'vendor');
$installer->addAttribute('catalog_product', 'vendor_id',
    array(
        'source' => 'SwiftOtter_Inventory/Source_Vendor',
        'type' => 'int',
        'grid' => true,
        'label' => 'Vendor',
        'input' => 'select',
        'required' => false,
        'used_in_product_listing' => true
    ));

$installer->endSetup();

