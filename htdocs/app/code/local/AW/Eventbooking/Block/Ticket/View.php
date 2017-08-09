<?php

class AW_Eventbooking_Block_Ticket_View extends Mage_Core_Block_Template
{

    public function getOrderUrl($id){
        return Mage::getUrl('sales/order/view',array('_secure'=>true,'order_id'=>$id));
    }
    public function getBackUrl()
    {
        return Mage::getUrl('aw_eventbooking/ticket/index',array('_secure'=>true));
    }
    public function getConfirmationUrl($id)
    {
        return Mage::getUrl('aw_eventbooking/ticket/resendConfirmation',array('_secure'=>true,'id'=>$id));
    }
    public function getProductOptions($ticket)
    {
        $orderItem = $ticket->getOrderItem();
        /** @var AW_Eventbooking_Helper_Ticket $ticketHelper */
        $ticketHelper = Mage::helper('aw_eventbooking/ticket');
        return $ticketHelper->getOrderItemOptions($orderItem);
    }

}