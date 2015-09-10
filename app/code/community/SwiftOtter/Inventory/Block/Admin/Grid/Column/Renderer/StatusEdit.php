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
 * @copyright Swift Otter Studios, 7/22/14
 * @package default
 **/

class SwiftOtter_Inventory_Block_Admin_Grid_Column_Renderer_StatusEdit extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Select
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $name = $this->getColumn()->getName() ? $this->getColumn()->getName() : $this->getColumn()->getId();

        $class = $this->getColumn()->getValidateClass();
        if ($class) {
            $class = rtrim($class, '"');
            $class .= " status-edit";
        } else {
            $class = 'class="status-edit"';
        }

        $html = '<select name="' . $this->escapeHtml($name) . '" ' . $class . ' data-id="' . $row->getId() . '">';
        $value = $row->getData($this->getColumn()->getIndex());
        foreach ($this->getColumn()->getOptions() as $val => $label){
            $selected = ( ($val == $value && (!is_null($value))) ? ' selected="selected"' : '' );
            $html .= '<option value="' . $this->escapeHtml($val) . '"' . $selected . '>';
            $html .= $this->escapeHtml($label) . '</option>';
        }
        $html.='</select>';
        return $html;
    }
}