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

class SwiftOtter_Inventory_Block_Admin_Vendor_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $data = Mage::registry('vendor_data');
        if (!$data) {
            $data = new Varien_Object();
        }

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id'=> $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
            'class' => 'fieldset-wide'
        ));

        $form->setUseContainer(true);

        $this->setForm($form);

        $fieldset = $form->addFieldset('vendor_form', array('legend' => $this->__('Vendor Information')));
        $yesNo = array("0" => "No", "1" => "Yes");

        $fieldset->addField('account_number', 'text', array(
                'label' => $this->__('Account Number'),
                'required' => false,
                'name' => 'account_number'
            ));

        $fieldset->addField('name', 'text', array(
            'label' => $this->__('Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'name'
        ));

        $fieldset->addField('abbrev', 'text', array(
            'label' => $this->__('Abbreviation'),
            'required' => false,
            'name' => 'abbrev'
        ));

        $fieldset->addField('notes', 'textarea', array(
            'label' => $this->__('Vendor Notes'),
            'required' => false,
            'name' => 'notes'
        ));

        $fieldset->addField('phone_order', 'text', array(
            'label' => $this->__('Phone (Orders)'),
            'required' => false,
            'name' => 'phone_order'
        ));

        $fieldset->addField('phone_media', 'text', array(
            'label' => $this->__('Phone (Media)'),
            'required' => false,
            'name' => 'phone_media'
        ));

        $fieldset->addField('phone_returns', 'text', array(
            'label' => $this->__('Phone (Returns)'),
            'required' => false,
            'name' => 'phone_returns'
        ));

        $fieldset->addField('email_order', 'text', array(
            'label' => $this->__('Email (Orders)'),
            'required' => false,
            'name' => 'email_order'
        ));

        $fieldset->addField('email_media', 'text', array(
            'label' => $this->__('Email (Media)'),
            'required' => false,
            'name' => 'email_media'
        ));

        $fieldset->addField('email_returns', 'text', array(
            'label' => $this->__('Email (Returns)'),
            'required' => false,
            'name' => 'email_returns'
        ));

        $fieldset->addField('fax', 'text', array(
            'label' => $this->__('Fax'),
            'required' => false,
            'name' => 'fax'
        ));

        $fieldset->addField('website', 'text', array(
            'label' => $this->__('Website'),
            'required' => false,
            'name' => 'website'
        ));

        $fieldset->addField('billing_company', 'text', array(
            'label' => $this->__('Company'),
            'required' => false,
            'name' => 'billing_company'
        ));

        $fieldset->addField('billing_contact', 'text', array(
            'label' => $this->__('Contact'),
            'required' => false,
            'name' => 'billing_contact'
        ));

        $fieldset->addField('billing_address_1', 'text', array(
            'label' => $this->__('Address 1'),
            'required' => false,
            'name' => 'billing_address_1'
        ));

        $fieldset->addField('billing_address_2', 'text', array(
            'label' => $this->__('Address 2'),
            'required' => false,
            'name' => 'billing_address_2'
        ));

        $fieldset->addField('billing_city', 'text', array(
            'label' => $this->__('City'),
            'required' => false,
            'name' => 'billing_city'
        ));

        $fieldset->addField('billing_state', 'text', array(
            'label' => $this->__('State'),
            'required' => false,
            'name' => 'billing_state'
        ));

        $fieldset->addField('billing_zip', 'text', array(
            'label' => $this->__('ZIP Code'),
            'required' => false,
            'name' => 'billing_zip'
        ));

        $fieldset->addField('reorder_period', 'select', array(
            'label' => $this->__('Reorder Period'),
            'required' => true,
            'name' => 'reorder_period',
            'options' => Mage::getModel('SwiftOtter_Inventory/Source_Times')->getAllOptions()
        ));

        $fieldset->addField('lead_time', 'select', array(
            'label' => $this->__('Lead Time'),
            'required' => true,
            'name' => 'lead_time',
            'options' => Mage::getModel('SwiftOtter_Inventory/Source_Times')->getAllOptions()
        ));

        $fieldset->addField('edit_drop_ship_alert', 'hidden', array(
            'name' => 'edit_drop_ship_alert'
        ));
        $data->setEditDropShipAlert(1);

        $fieldset->addField('drop_ship_alert', 'multiselect', array(
            'label' => $this->__('Drop Ship Alerts'),
            'required' => false,
            'name' => 'drop_ship_alert',
            'values' => Mage::getModel('SwiftOtter_Email/Source_Emails')->toOptionArray()
        ));

        $fieldset->addField('edit_inventory_alert', 'hidden', array(
            'name' => 'edit_inventory_alert'
        ));
        $data->setEditInventoryAlert(1);

        $fieldset->addField('inventory_alert', 'multiselect', array(
            'label' => $this->__('Inventory Reorder Alerts'),
            'required' => false,
            'name' => 'inventory_alert',
            'values' => Mage::getModel('SwiftOtter_Email/Source_Emails')->toOptionArray()
        ));

        $fieldset->addField('third_party_selling', 'select', array(
            'label' => $this->__('Third Party Selling'),
            'required' => false,
            'name' => 'third_party_selling',
            'options' => Mage::getModel('adminhtml/system_config_source_yesno')->toArray()
        ));

        $form->setValues($data);

        return parent::_prepareForm();
    }
}