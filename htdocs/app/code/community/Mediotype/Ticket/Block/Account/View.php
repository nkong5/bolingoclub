<?php
/**
 * Magento / Mediotype Module
 *
 *
 * @desc
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Block_Account_View
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Block_Account_View extends Mage_Customer_Block_Account_Dashboard
{
    /**
     * @return array
     */
    public function getUpcomingEvents()
    {
        /** @var $purchasedTickets Mediotype_Ticket_Model_Resource_Order_Collection */
        $purchasedTickets = Mage::getModel('mediotype_ticket/order')->getCollection();
        $purchasedTickets->addFieldToFilter('customer_id', array('eq' => $this->getCustomerSession()->getCustomerId()));
        $purchasedTickets->addFieldToFilter('ticket_available', true);
        $purchasedTickets->getSelect()->group('sku');
        $purchasedTickets->getSelect()->columns('COUNT(`main_table`.`id`) AS qty_purchased');
        $purchasedTickets->load();

        $tickets = array();
        foreach ($purchasedTickets as $key => $ticketOrderModel) {
            /** @var $ticketProduct Mage_Catalog_Model_Product */
            $ticketProduct = Mage::helper('mediotype_ticket')->loadTicketBySku($ticketOrderModel->getData('sku'));
            /** @var $ticketProductInstance Mediotype_Ticket_Model_Product_Type_Simpleticket */
            $ticketProductInstance = $ticketProduct->getTypeInstance();
            if ($ticketProduct->getId() && !$ticketProductInstance->hasOccurred()) {
                $tickets[] = array(
                    "product" => $ticketProduct,
                    "qty_purchased" => $ticketOrderModel->getData('qty_purchased'),
                    "passbook_url" => $ticketProductInstance->getPassbookUrl(),
                    "ticket_available" => (bool)$ticketOrderModel->getTicketAvailable(),
                );
            }
        }

        return $tickets;
    }

    public function getPastEvents()
    {
        /** @var $purchasedTickets Mediotype_Ticket_Model_Resource_Order_Collection */
        $purchasedTickets = Mage::getModel('mediotype_ticket/order')->getCollection();
        $purchasedTickets->addFieldToFilter('customer_id', array('eq' => $this->getCustomerSession()->getCustomerId()));
        $purchasedTickets->addFieldToFilter('ticket_available', true);
        $purchasedTickets->getSelect()->group('sku');
        $purchasedTickets->getSelect()->columns('COUNT(`main_table`.`id`) AS qty_purchased');
        $purchasedTickets->load();

        $tickets = array();
        foreach ($purchasedTickets as $key => $ticketOrderModel) {
            /** @var $ticketProduct Mage_Catalog_Model_Product */
            $ticketProduct = Mage::helper('mediotype_ticket')->loadTicketBySku($ticketOrderModel->getData('sku'));
            /** @var $ticketProductInstance Mediotype_Ticket_Model_Product_Type_Simpleticket */
            $ticketProductInstance = $ticketProduct->getTypeInstance();
            if ($ticketProduct->getId() && $ticketProductInstance->hasOccurred()) {
                $tickets[] = array(
                    "product" => $ticketProduct,
                    "qty_purchased" => $ticketOrderModel->getData('qty_purchased'),
                    "passbook_url" => $ticketProductInstance->getPassbookUrl()
                );
            }
        }

        return $tickets;
    }

    /**
     * @return array
     */
    public function getEventsAttended()
    {
        $attended = Array();
        /** @var $purchasedTickets Mediotype_Ticket_Model_Resource_Order_Collection */
        $purchasedTickets = Mage::getModel('mediotype_ticket/order')->getCollection();
        $purchasedTickets->addFieldToFilter('customer_id', array('eq' => $this->getCustomerSession()->getCustomerId()))->load();


        $tickets = array();
        foreach ($purchasedTickets as $ticketOrderModel) {
            $ticketProduct = Mage::helper('mediotype_ticket')->loadTicketBySku($ticketOrderModel->getData('sku'));
            /** @var $ticketProductInstance Mediotype_Ticket_Model_Product_Type_Simpleticket */
            if ($ticketProduct->getId()) {
                $ticketProductInstance = $ticketProduct->getTypeInstance();
                if ($ticketProductInstance->hasOccurred()) {
                    $attended[] = $ticketProduct;
            }
        }
        }

        return $attended;
    }

    /**
     * @param $event
     * @return Mage_Catalog_Model_Product
     */
    public function getEventById($event)
    {
        return Mage::getModel('catalog/product')->load($event->getData('entity_id'));
    }

    /**
     * @param Mage_Catalog_Model_Product $event
     * @return Mage_Catalog_Helper_Image
     */
    public function getDisplayImageUrl($event)
    {
        return Mage::helper('catalog/image')->init($event, 'small_image')->resize(255, 190);
    }


    /**
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * @param Mediotype_Ticket_Model_Order $ticket
     * @return string
     */
    public function getEmailUrl($ticket){
        return Mage::getUrl('ticket/index/email') .'event/'. $ticket->getSku();
    }
}
