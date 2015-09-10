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

$installer->addAttribute('catalog_product', 'case_pack_quantity',
    array(
        'type' => 'int',
        'grid' => true,
        'label' => 'Case Pack Quantity (blank to disable)',
        'input' => 'text',
        'required' => false
    ));

$installer->endSetup();

