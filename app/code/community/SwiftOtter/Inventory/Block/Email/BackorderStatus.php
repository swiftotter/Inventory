<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 11/20/2013
 * @package default
 **/

class SwiftOtter_Inventory_Block_Email_BackorderStatus extends Mage_Core_Block_Text
{
    const CONFIG_FIELD = 'backordered_item_message';
    const CONFIG_FIELD_ETA = 'backordered_item_message_eta';

    protected function _toHtml()
    {
        if ($this->getParentBlock()->getItem()) {
            /** @var Mage_Sales_Model_Order_Item $item */
            $item = $this->getParentBlock()->getItem();

            if ($item->getQtyBackordered() > 0) {
                $product = $item->getProduct();

                $configField = self::CONFIG_FIELD;
                if ($product->getEta() && strtotime($product->getEta()) > time()) {
                    $configField = self::CONFIG_FIELD_ETA;
                }

                $message = "<br/>" . Mage::helper('SwiftOtter_Inventory')->getStoreConfig($configField);
                $replacements = array(
                    '[BACKORDER_QTY]' => round($item->getQtyBackordered()),
                    '[QTY]' => round($item->getQtyOrdered()),
                    '[ETA]' => date('m-d-Y', strtotime($product->getEta()))
                );

                foreach ($replacements as $search => $replace) {
                    $message = str_replace($search, $replace, $message);
                }

                return $message;
            }
        }
    }

}