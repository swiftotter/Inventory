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

$installer->addAttribute(
    'catalog_product',
    'eta',
    array(
        'label' => 'ETA',
        'type' => 'datetime',
        'grid' => true,
        'frontend_label' => 'Estimated Time of Arrival (Backorder)',
        'input' => 'date',
        'required' => false,
        'is_visible_on_front' => '1',
        'used_in_product_listing' => '1',
        'backend_model' => 'eav/entity_attribute_backend_datetime',
        'frontend_model' => 'eav/entity_attribute_frontend_datetime'
    )
);

$installer->endSetup();

