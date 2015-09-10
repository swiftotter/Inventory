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
 * @copyright Swift Otter Studios, 7/15/14
 * @package default
 **/


/**
 * Class SwiftOtter_Inventory_Model_ReceivedBackorder
 *
 * @method string getCreatedAt()
 * @method int getOrderId()
 * @method int getOrderItemId()
 * @method int getQtyReceived()
 * @method bool getShipped()
 * @method int getShipmentItemId()
 * @method int getAdjustmentId()
 */
class SwiftOtter_Inventory_Model_ReceivedBackorder extends Mage_Core_Model_Abstract
{
    protected $_order;
    protected $_orderItem;

    public function __construct()
    {
        $this->_init('SwiftOtter_Inventory/ReceivedBackorder');
    }

    /**
     * Populates a blank instance of this model with required values
     *
     * @param $orderItem
     * @param $qtyReceived
     * @return $this
     */
    public function initialize($orderItem, $qtyReceived)
    {
        $this->setQtyReceived($qtyReceived);
        $this->setOrderItemId($orderItem->getId());
        $this->setOrderId($orderItem->getOrderId());

        return $this;
    }

    /**
     * @param $order
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
        if (!$this->_orderItem) {
            $this->_orderItem = Mage::getModel('sales/order')->load($this->getOrderId());
        }

        return $this->_order;
    }


    /**
     * @param $orderItem
     * @return $this
     */
    public function setOrderItem($orderItem)
    {
        $this->_orderItem = $orderItem;
        return $this;
    }

    /**
     * @return Mage_Sales_Model_Order_Item
     */
    public function getOrderItem()
    {
        if (!$this->_orderItem) {
            $this->_orderItem = Mage::getModel('sales/order_item')->load($this->getOrderItemId());
        }

        return $this->_orderItem;
    }




}