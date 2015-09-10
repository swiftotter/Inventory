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

class SwiftOtter_Inventory_Block_Admin_Edit extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct(){
        parent::__construct();
        $this->_controller = 'Admin_Edit';
        $this->_blockGroup = 'SwiftOtter_Inventory';
        $this->_headerText = Mage::helper('SwiftOtter_Inventory')->__('Inventory Mass Edit');

        $this->_addButton('save', array(
            'label' => Mage::helper('SwiftOtter_Inventory')->__('Save Changes'),
            'onclick' => 'massEditSubmit();',
            'class' => 'complete'
        ));

        $this->_addButton('view_shippable', array(
            'label' => Mage::helper('SwiftOtter_Inventory')->__('View Shippable Orders'),
            'onclick' => sprintf('setLocation(\'%s\');', $this->getUrl('*/inventory_report/shippablebackorders/'))
        ));

        $this->_addButton('save_silent', array(
            'label' => Mage::helper('SwiftOtter_Inventory')->__('Save Changes (SILENT)'),
            'onclick' => 'massEditSubmit(true);'
        ));
    }

    protected function _prepareLayout() {
        $this->_removeButton('add');
        $this->setChild('grid', $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_Grid', $this->_controller . '.Grid')->setSaveParametersInSession(true));
    }
}