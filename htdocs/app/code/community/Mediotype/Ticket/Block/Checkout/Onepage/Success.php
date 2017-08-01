<?php
class Mediotype_Ticket_Block_Checkout_Onepage_Success extends Mage_Checkout_Block_Onepage_Success
{
    public function showPrintLinks()
    {
        return Mage::getStoreConfig('mediotype_ticket/ticket_email/ticket_print_download');
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::getModel('sales/order')->load(Mage::getSingleton('checkout/session')->getLastOrderId());
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getModel('sales/quote')->load($this->getOrder()->getQuoteId());
    }

    public function hasTicketProductsInCart(){
        foreach ($this->getQuote()->getAllItems() as $index => $_item) {
            if ($_item->getProduct()->getTypeId() == Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function getTickets()
    {
        /** @var $purchasedTickets Mediotype_Ticket_Model_Resource_Order_Collection */
        $purchasedTickets = Mage::getModel('mediotype_ticket/order')->getCollection();
        $purchasedTickets->addFieldToFilter('customer_id', array('eq' => $this->getCustomerSession()->getCustomerId()));
        $purchasedTickets->addFieldToFilter('order_id', array('eq' => $this->getOrder()->getId()));
        $purchasedTickets->load();
        $tickets = array();
        foreach ($purchasedTickets as $ticket) {
            /** @var $ticket Mediotype_Ticket_Model_Order */
            $tickets[] = array(
                "product" => $ticket->getProduct(),
                "ticket_model" => $ticket,
            );
        }
        return $tickets;
    }

    /**
     * @param $ticket
     * @return bool
     */
    public function getPassbookEnabled($ticket)
    {
         if ((bool)(int)Mage::getStoreConfig('mediotype_ticket/apple_passbook_settings/passbook_enabled') && $ticket->getPassbookEnabled()) {
             return true;
         }
        return false;
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }
}