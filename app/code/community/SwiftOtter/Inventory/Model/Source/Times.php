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
 * @copyright Swift Otter Studios, 7/10/14
 * @package default
 **/

class SwiftOtter_Inventory_Model_Source_Times extends SwiftOtter_Base_Model_Source_Abstract
{
    public function getAllOptions()
    {
        $helper = Mage::helper('SwiftOtter_Inventory');

        $daysInMonth = 30;
        $daysInYear = (int)date("z", mktime(0,0,0,12,31,2008)) + 1;

        $days = array(
            (int)round($daysInMonth / 2) => $helper->__('15 days'),
            $daysInMonth => $helper->__('1 month'),
            $daysInMonth * 2 => $helper->__('2 months'),
            $daysInMonth * 3 => $helper->__('3 months'),
            (int)round($daysInYear / 2) => $helper->__('6 months'),
            $daysInYear => $helper->__('1 year')
        );

        return $days;
    }
}