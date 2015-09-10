<?php

$installer = $this;
/**
 * Prepare database for install
 */
$installer->startSetup();

$status = Mage::getModel('sales/order_status');

$status->setStatus('backordered')->setLabel('Back Ordered')
    ->assignState(Mage_Sales_Model_Order::STATE_NEW)
    ->save();

/**
 * Prepare database after install
 */
$installer->endSetup();