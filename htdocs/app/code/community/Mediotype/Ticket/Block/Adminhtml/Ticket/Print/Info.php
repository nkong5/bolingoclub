<?php
class Mediotype_Ticket_Block_Adminhtml_Ticket_Print_Info extends Mage_Adminhtml_Block_Sales_Order_View_Info
{
    protected function _beforeToHtml()
    {
        return $this;
    }

    public function getOrder()
    {
        return $this->getData('order');
    }
}