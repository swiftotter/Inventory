<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

/**
 * Class SwiftOtter_Inventory_Model_AdjustmentLog
 *
 * @method int getProductId()
 * @method int getOriginalQty()
 * @method int getCurrentQty()
 * @method int getOrdersAffected()
 * @method int getItemsAffected()
 * @method int getUserId()
 * @method string getCreatedAt()
 */
class SwiftOtter_Inventory_Model_AdjustmentLog extends Mage_Core_Model_Abstract
{
    protected $_product;
    protected $_user;

    public function __construct()
    {
        $this->_init('SwiftOtter_Inventory/AdjustmentLog');
    }


    /**
     * @param $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = Mage::getModel('catalog/product')
                ->load($this->getProductId());
        }
        return $this->_product;
    }


    /**
     * @param $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->_user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        if (!$this->_user) {
            $this->_user = Mage::getModel('admin/user')
                ->load($this->getUserId());
        }
        return $this->_user;
    }


}