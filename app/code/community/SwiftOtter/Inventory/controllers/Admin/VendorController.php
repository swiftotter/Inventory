<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

class SwiftOtter_Inventory_Admin_VendorController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $vendor = $this->_loadVendor();

        $this->loadLayout();
        $this->renderLayout();
    }

    public function salesAction()
    {
        Mage::getResourceModel('SwiftOtter_Sales/Order_Product')->indexSales();
    }

    public function dropshipAction()
    {
        $invoice = Mage::getModel('sales/order_invoice')->load(89859);
        if ($invoice->getId()) {
            Mage::helper('SwiftOtter_Inventory/Invoice')->alertDropShipments($invoice);
        }
    }

    public function kitAction()
    {
        Mage::getModel('SwiftOtter_KitProduct/Cron')->reindexKitProducts();
    }

    public function inventoryAction()
    {
        Mage::getModel('SwiftOtter_Inventory/Cron')->inventoryUpdate();
    }

    public function etaAction()
    {
        Mage::getModel('SwiftOtter_Inventory/Cron')->checkETA();
    }

    public function saveAction() {
        $vendor = $this->_loadVendor();

        if ($data = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getParam('id');
            $vendor->setData($data);

            Mage::getSingleton('adminhtml/session')->setFormData($data);

            try {
                if ($id) {
                    $vendor->setId($id);
                }
                $vendor->save();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Success!'))->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $vendor->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                if ($vendor && $vendor->getId()){
                    $this->_redirect('*/*/edit', array('id' => $vendor->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            }
            return;
        }

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('SwiftOtter_Inventory')->__('No data specified!'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        $vendor = $this->_loadVendor();

        try {
            if ($vendor->getId()) {
                $vendor->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Successfully deleted'));
                $this->_redirect('*/*/');
            }
        } catch(Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
            $this->_redirect('*/*/');
        }
    }


    protected function _loadVendor($vendorId = null)
    {
        if (!$vendorId) {
            $vendorId = $this->getRequest()->getParam('id');
        }

        $vendor = Mage::getModel('SwiftOtter_Inventory/Vendor')->load($vendorId);

        if ($vendor->getId()) {
            Mage::register('vendor_data', $vendor);
            $this->_title($this->__('Editing Vendor: %s', $vendorId));
        }

        return $vendor;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/manage_vendors');
    }
}