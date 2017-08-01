<?php
/**
 * Magento / Mediotype Module
 *
 *
 * @desc
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Block_Print_View
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Block_Print_View extends Mage_Customer_Block_Account_Dashboard
{
    /**
     * @return Mediotype_Ticket_Model_Resource_Order_Collection
     */
    public function getTickets()
    {
        /** @var $purchasedTickets Mediotype_Ticket_Model_Resource_Order_Collection */
        $purchasedTickets = Mage::getModel('mediotype_ticket/order')->getCollection();
        $purchasedTickets->addFieldToFilter('customer_id', array('eq' => $this->getCustomerSession()->getCustomerId()));
        $purchasedTickets->addFieldToFilter('sku', array('eq' => $this->getEventName()));
        $purchasedTickets->addFieldToFilter('ticket_available', true);
        $purchasedTickets->load();

        return $purchasedTickets;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getTicketProduct()
    {
        return Mage::helper('mediotype_ticket')->loadTicketBySku($this->getEventName());
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * @return string|null
     */
    public function getEventName()
    {
        return $this->getRequest()->getParam('event');
    }
}