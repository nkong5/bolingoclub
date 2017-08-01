<?php
/**
 * Magento / Mediotype Module
 *
 * @method setTicketAvailable bool
 *
 * @desc
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Model_Order
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Model_Order extends Mage_Core_Model_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        $this->_init('mediotype_ticket/order', 'id');
    }

    /**
     * @param int $id
     * @param string|null $field
     * @return Mediotype_Ticket_Model_Order
     */
    public function load($id, $field = null)
    {
        $this->_beforeLoad($id, $field);
        $this->_getResource()->load($this, $id, $field);

        $customerData = Mage::getModel('customer/customer')->load($this->_data['customer_id'])->getData();
        foreach ($customerData as $key => $value) {
            if ($key != 'id') {
                $this->_data['customer_' . $key] = $value;
            }
        }

        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasBeenRedeemed(){
        return !is_null($this->getData('date_redeemed'));
    }

    /**
     * @return bool
     */
    public function hasBeenRevoked(){
        return !is_null($this->getData('date_revoked'));
    }

    /**
     * @return string
     */
    public function getRedeemUrl()
    {
        $useSecure = Mage::getStoreConfig('web/secure/use_in_adminhtml');
        if($useSecure){
            return Mage::getUrl('ticket/index/redeem', array("id" => $this->getId(), "_secure" => true ));
        } else {
            return Mage::getUrl('ticket/index/redeem', array("id" => $this->getId()));
        }
    }

    /**
     * @return string
     */
    public function getQrcodeUrl()
    {
        return Mage::getUrl('ticket/index/qrimage', array("id" => $this->getId()));
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder(){
        return Mage::getModel('sales/order')->load($this->getData('order_id'));
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct(){
        return  Mage::helper('mediotype_ticket')->loadTicketBySku($this->getData('sku'));
    }
}
