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
 * Class SwiftOtter_Inventory_Model_Vendor
 *
 * @method string getAccountNumber()
 * @method string getAbbrev()
 * @method string getName()
 * @method string getNotes()
 * @method string getPhone()
 * @method string getFax()
 * @method string getEmail()
 * @method string getWebsite()
 * @method string getBillingCompany()
 * @method string getBillingContact()
 * @method string getBillingAddress1()
 * @method string getBillingAddress2()
 * @method string getBillingCity()
 * @method string getBillingState()
 * @method string getBillingPostCode()
 * @method string getBillingRegion()
 * @method array getDropShipAlert()
 * @method array getInventoryAlert()
 */
class SwiftOtter_Inventory_Model_Vendor extends Mage_Core_Model_Abstract
{
    protected $_stockItems;

    public function __construct()
    {
        $this->_init('SwiftOtter_Inventory/Vendor');
    }

    protected function _beforeSave()
    {
        if (is_array($this->getDropShipAlert()) || $this->getEditDropShipAlert()) {
            $values = $this->getDropShipAlert();
            if (!is_array($values)) {
                $values = array();
            }

            $this->setData('drop_ship_alert', implode(',', $values));
        }

        if (is_array($this->getInventoryAlert()) || $this->getEditDropShipAlert()) {
            $values = $this->getInventoryAlert();
            if (!is_array($values)) {
                $values = array();
            }

            $this->setData('inventory_alert', implode(',', $values));
        }
    }

    protected function _afterLoad()
    {
        if ($this->getDropShipAlert()) {
            $this->setData('drop_ship_alert', explode(',', $this->getDropShipAlert()));
        }

        if ($this->getInventoryAlert()) {
            $this->setData('inventory_alert', explode(',', $this->getInventoryAlert()));
        }
    }

    /**
     * @param array $stockItems
     */
    public function setStockItems($stockItems)
    {
        $this->_stockItems = $stockItems;
    }

    /**
     * @param Mage_CatalogInventory_Model_Stock_Item $stockItem
     * @return $this
     */
    public function addStockItem($stockItem)
    {
        if (!$this->_stockItems) {
            $this->_stockItems = array();
        }

        $this->_stockItems[] = $stockItem;

        return $this;
    }

    /**
     * @return array
     */
    public function getStockItems()
    {
        return $this->_stockItems;
    }
}