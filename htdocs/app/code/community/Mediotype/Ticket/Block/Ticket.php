<?php
class Mediotype_Ticket_Block_Ticket extends Mage_Core_Block_Template
{

    protected $_ticketOrderModel = null;

    protected function _construct(){
        parent::_construct();
        if($this->hasData('ticketOrderModel'))
            $this->setTicketOrderModel($this->getData('ticketOrderModel'));

        $this->setTemplate('mediotype/ticket/ticket.phtml');

    }

    public function setTicketOrderModel($ticketOrderModel)
    {
        $this->_ticketOrderModel = $ticketOrderModel;
        return $this;
    }

    /**
     * @return Mediotype_Ticket_Model_Order
     */
    public function getTicketOrderModel()
    {
        return $this->_ticketOrderModel;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getTicketProduct()
    {
        return Mage::helper('mediotype_ticket')->loadTicketBySku($this->getTicketOrderModel()->getData('sku'));
    }

}