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
 * @copyright Swift Otter Studios, 7/13/15
 * @package default
 **/

class SwiftOtter_Inventory_Model_ETA
{
    const EMAIL_ETA_REACHED_NOTIFICATION_CODE = 'eta_alert';
    const EMAIL_ETA_CHANGED_NOTIFICATION_CODE = 'eta_changed';
    const ETA_ATTRIBUTE = 'eta';

    public function alertChange($products)
    {
        $helper = Mage::helper('SwiftOtter_Inventory');
        if (!is_array($products)) {
            $products = array($products);
        }

        if (is_object($products) || count($products) > 0) {
            $this->_formatProducts($products);

            $this->sendEmail(
                self::EMAIL_ETA_CHANGED_NOTIFICATION_CODE,
                $products,
                $helper->__('ETA Date Changed'),
                $helper->__('The following products have had their ETA\'s changed'),
                $helper->__('ETA Date Changed'),
                true,
                true
            );
        }
    }

    public function alertDateReached()
    {
        $helper = Mage::helper('SwiftOtter_Inventory');

        /** @var Mage_Catalog_Model_Resource_Product_Collection $products */
        $products = Mage::getResourceModel('catalog/product_collection');
        $products->addAttributeToFilter(self::ETA_ATTRIBUTE, array('eq' => Mage::getModel('core/date')->date('Y-m-d')))
            ->addAttributeToSelect(array('name', 'sku'));

        if (count($products) > 0) {
            $this->sendEmail(
                self::EMAIL_ETA_REACHED_NOTIFICATION_CODE,
                $products,
                $helper->__('ETA Date Reached'),
                $helper->__('The following products have reached their specified ETA date on the website.'),
                $helper->__('ETA Date Reached Alert On: %s', $this->_convertToFriendlyDate())
            );
        }
    }


    /**
     * @param array $products
     */
    public function sendEmail($code, $products, $subject, $description, $title, $etaColumn = false, $showUser = false)
    {
        Mage::getDesign()->setArea(Mage_Core_Model_App_Area::AREA_FRONTEND);
        /** @var SwiftOtter_Inventory_Block_Email_InventoryAlert $block */
        $block = Mage::app()->getLayout()->createBlock('SwiftOtter_Inventory/Email_ETAAlert')
            ->setETAColumn($etaColumn)
            ->setShowUser($showUser)
            ->setProducts($products);

        $html = $block->toHtml();

        $details = Mage::getModel('SwiftOtter_Email/Details')
            ->setSubject($subject)
            ->setDescription($description)
            ->setText($html);

        $details->setTitle($title);

        $observer = array(
            'code' => $code,
            'details' => $details
        );

        Mage::dispatchEvent('swiftotter_email_notification_send', $observer);
    }

    protected function _formatProducts($products)
    {
        $helper = Mage::helper('SwiftOtter_Inventory');

        foreach ($products as $product) {
            $product->setHasETAAlerted(true);

            if ($product->getOrigData('eta')) {
                $oldDate = $this->_convertToFriendlyDate(strtotime($product->getOrigData('eta')));
            } else {
                $oldDate = 'Unset';
            }

            if ($product->getEta()) {
                $newDate = $this->_convertToFriendlyDate(strtotime($product->getEta()));
                $product->setFriendlyEta($helper->__('%s > %s', $oldDate, $newDate));
            } else {
                $product->setEta($helper->__('%s > Unset', $oldDate));
            }
        }

        return $products;
    }

    protected function _convertToFriendlyDate($input = null)
    {
        if ($input) {
            return date('m-d-Y', $input);
        } else {
            return Mage::getModel('core/date')->date('m-d-Y');
        }
    }
}