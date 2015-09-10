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

class SwiftOtter_Inventory_Block_Admin_Vendor_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('vendor_grid')
             ->setDefaultSort('id')
             ->setDefaultDir('desc')
             ->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('SwiftOtter_Inventory/Vendor')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'	=> $this->__('Vendor ID'),
            'align'		=> 'center',
            'index'		=> 'id',
            'width'     => '35px',
        ));

        $this->addColumn('account_number', array(
            'header'	=> $this->__('Account Number'),
            'align'		=> 'center',
            'index'		=> 'account_number',
            'width'     => '60px'
        ));

        $this->addColumn('name', array(
            'header'	=> $this->__('Name'),
            'align'		=> 'left',
            'index'		=> 'name'
        ));

        $this->addColumn('phone_order', array(
            'header'	=> $this->__('Phone'),
            'align'		=> 'center',
            'index'		=> 'phone_order',
            'width'     => '125px'
        ));

        $this->addColumn('reorder_period', array(
            'header'	=> $this->__('Reorder Period'),
            'align'		=> 'center',
            'index'		=> 'reorder_period',
            'type'      => 'options',
            'options'   => Mage::getModel('SwiftOtter_Inventory/Source_Times')->getAllOptions()
        ));

        $this->addColumn('lead_time', array(
            'header'	=> $this->__('Lead Time'),
            'align'		=> 'center',
            'index'		=> 'lead_time',
            'type'      => 'options',
            'options'   => Mage::getModel('SwiftOtter_Inventory/Source_Times')->getAllOptions()
        ));

        $this->addColumn('third_party_selling', array(
            'header'	=> $this->__('3rd Party Selling'),
            'align'		=> 'center',
            'index'		=> 'third_party_selling',
            'type'      => 'options',
            'width'     => '50px',
            'options'   => Mage::getModel('adminhtml/system_config_source_yesno')->toArray()
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
            ));

        return parent::_prepareColumns();
    }

    public function convertToSelectOptions($input)
    {
        $output = array();
        foreach ($input as $value) {
            $id = $value['value'];
            $label = $value['label'];

            $output[$id] = $label;
        }

        return $output;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array ('id' => $row->getId()));
    }
}