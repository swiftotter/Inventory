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

class SwiftOtter_Inventory_Block_Admin_Vendor_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'SwiftOtter_Inventory';
        $this->_controller = 'Admin_Vendor';
        $this->_mode = 'edit';

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";

        $this->_updateButton('save', 'label', $this->__('Save Vendor'));
    }

    protected function _prepareLayout() {
        if ($this->_blockGroup && $this->_controller && $this->_mode) {
            $this->setChild('form', $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . "_" . $this->_mode . '_form'));
        }

        return parent::_prepareLayout();
    }

    public function getHeaderText()
    {
        if (Mage::registry('vendor_data') && Mage::registry('vendor_data')->getId())
        {
            return $this->__('Edit Vendor "%s" (%s)', $this->htmlEscape(Mage::registry('vendor_data')->getName()), Mage::registry('vendor_data')->getId());
        } else {
            return $this->__('New Vendor');
        }
    }
}