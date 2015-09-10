<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 10/29/2013
 * @package default
 **/

class SwiftOtter_Inventory_Admin_Inventory_ReportController extends Mage_Adminhtml_Controller_Action
{
    public function stockstatusAction()
    {
        $this->_title($this->__('Inventory'))->_title($this->__('Stock Status Report'));

        Mage::register('stock_status_view', $this->getRequest()->getParam('view'));

        $this->loadLayout();
        $this->renderLayout();
    }

    public function stockstatusGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function vendorAction()
    {
        $this->_title($this->__('Inventory'))->_title($this->__('Vendor Report'));

        $this->loadLayout();
        $this->renderLayout();
    }

    public function vendorgridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function salesOrderingAction()
    {
        $this->loadLayout();
        Mage::helper('SwiftOtter_Base/Date')->initDateFilterParams('inventory_filter');

        $this->_title($this->__('Inventory'))->_title($this->__('Sales/Ordering Report'));

        $this->renderLayout();
    }

    public function salesOrderingGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/inventory');
    }

    public function currentBackordersAction()
    {
        $this->loadLayout();
        Mage::helper('SwiftOtter_Base/Date')->initDateFilterParams('inventory_filter');

        $this->_title($this->__('Orders'))->_title($this->__('Current Backorder Report'));

        $this->renderLayout();
    }

    public function currentBackordersGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function shippableBackordersAction()
    {
        $this->loadLayout();
        Mage::helper('SwiftOtter_Base/Date')->initDateFilterParams('inventory_filter');

        $this->_title($this->__('Orders'))->_title($this->__('Shippable Backorder'));

        $this->renderLayout();
    }

    public function shippableBackordersGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function adjustmentLogAction()
    {
        $this->loadLayout();
        Mage::helper('SwiftOtter_Base/Date')->initDateFilterParams('inventory_filter');

        $this->_title($this->__('Orders'))->_title($this->__('Inventory Adjustment Log'));

        $this->renderLayout();
    }

    public function adjustmentLogGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function vendorProductAction()
    {
        $this->loadLayout();
        $this->_title($this->__('Inventory'))->_title($this->__('Vendor/Product Details'));

        $this->renderLayout();
    }

    public function vendorProductGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}