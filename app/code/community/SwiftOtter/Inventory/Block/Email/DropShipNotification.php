<?php
/**
 * SwiftOtter_Base is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SwiftOtter_Base is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with SwiftOtter_Base. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright: 2013 (c) SwiftOtter Studios
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 7/18/14
 * @package default
 **/

class SwiftOtter_Inventory_Block_Email_DropShipNotification extends Mage_Core_Block_Template
{
    protected $_order;
    protected $_invoice;
    protected $_products;
    protected $_vendor;

    protected function _construct()
    {
        $this->setTemplate('SwiftOtter/Inventory/Email/DropShipNotification.phtml');
    }

    /**
     * @param SwiftOtter_Inventory_Model_Vendor $vendor
     * @return $this
     */
    public function setVendor($vendor)
    {
        $this->_vendor = $vendor;
        return $this;
    }

    /**
     * @return SwiftOtter_Inventory_Model_Vendor
     */
    public function getVendor()
    {
        return $this->_vendor;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * @param array $products
     * @return $this
     */
    public function setProducts($products)
    {
        $this->_products = $products;
        return $this;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        return $this->_products;
    }

    /**
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return $this
     */
    public function setInvoice($invoice)
    {
        $this->_invoice = $invoice;
        return $this;
    }

    /**
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function getInvoice()
    {
        return $this->_invoice;
    }


}