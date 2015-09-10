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
 * @copyright Swift Otter Studios, 1/5/15
 * @package default
 **/

class SwiftOtter_Inventory_Block_Email_ETAAlert extends Mage_Core_Block_Template
{
    protected $_products;
    protected $_etaColumn = false;
    protected $_showUser = false;

    protected function _construct()
    {
        $this->setTemplate('SwiftOtter/Inventory/Email/ETAAlert.phtml');
    }

    /**
     * @return boolean
     */
    public function isShowUser()
    {
        return $this->_showUser;
    }

    /**
     * @param boolean $showUser
     * @return $this;
     */
    public function setShowUser($showUser)
    {
        $this->_showUser = $showUser;
        return $this;
    }

    public function getETAColumn()
    {
        return $this->_etaColumn;
    }

    public function setETAColumn($value)
    {
        $this->_etaColumn = $value;

        return $this;
    }

    public function getUser()
    {
        /** @var Mage_Admin_Model_User $user */
        if ($user = Mage::getSingleton('admin/session')->getUser()) {
            return $user->getName();
        } else {
            return '';
        }
    }

    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $products
     * @return $this
     */
    public function setProducts($products)
    {
        $this->_products = $products;
        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProducts()
    {
        return $this->_products;
    }
}