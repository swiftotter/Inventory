<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 10/29/2013
 * @package default
 **/

class SwiftOtter_Inventory_Admin_Inventory_EditController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function updateAction()
    {
        $items = get_object_vars(json_decode($this->getRequest()->getParam('in_update_product')));
        $silent = $this->getRequest()->getParam('perform_silent') == 'false' ? false : true;
        Mage::helper('SwiftOtter_Inventory/Adjustment')->setSilent($silent);

        Mage::helper('SwiftOtter_Inventory/Adjustment')->updateInventory($items);

        $this->getResponse()->setRedirect($this->getUrl('*/*/index', array('_current' => true)));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/mass_edit');
    }
}