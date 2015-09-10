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
 * @copyright Swift Otter Studios, 7/11/14
 * @package default
 **/

class SwiftOtter_Inventory_Block_Admin_Edit_Control extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id'=> $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
            'class' => 'fieldset-wide'
        ));

        $form->setUseContainer(true);

        $this->setForm($form);

        $fieldset = $form->addFieldset('allow_edit', array('legend' => $this->__('Form Settings')));

        $fieldset->addField('allow_edit_qty_available', 'checkbox', array(
            'label' => $this->__('Allow QTY Available Edit'),
            'required' => false,
            'name' => 'allow_edit_qty_available',
            'value' => 1
        ));

//        $form->setValues(array(
//            'allow_edit_qty_available' => Mage::app()->getRequest()->getParam('allow_edit_qty_available_internal')
//        ));

        return parent::_prepareForm();
    }
}