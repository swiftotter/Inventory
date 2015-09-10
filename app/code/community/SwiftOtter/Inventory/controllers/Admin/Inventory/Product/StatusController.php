<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 10/28/2013
 * @package default
 **/

class SwiftOtter_Inventory_Admin_Inventory_Product_StatusController extends Mage_Adminhtml_Controller_Action
{
    public function updateAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();

        $productId = $request->getParam('product_id');

        $status = $request->getParam('status');

        $product = new Varien_Object(array(
            'entity_id' => $productId,
            'status' => $status
        ));

        Mage::getResourceModel('catalog/product')->saveAttribute($product, 'status');

        $response->setHeader('Content-Type', 'application/json', true);
        $response->setBody(Mage::helper('core')->jsonEncode(array('result' => 'true')));
    }
}