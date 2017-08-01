<?php
/**
 * Magento / Mediotype Module
 *
 *
 * @desc
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Block_Adminhtml_Order_View
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Block_Adminhtml_Order_View extends Mage_Adminhtml_Block_Widget_View_Container
{
    protected $_ticket = null;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_headerText = "Ticket # " . $this->getTicket()->getId();
        $this->removeButton("back");
        $this->removeButton("edit");


        if (!$this->getTicket()->hasBeenRevoked()) {
            $this->_addButton('revoke', array(
                'label' => Mage::helper('mediotype_ticket')->__('Revoke Ticket'),
                'class' => 'delete',
                'onclick' => 'window.location.href=\'' . $this->getUrl("*/*/revoke", array("id" => $this->getTicket()->getId())) . '\'',
            ));
        } else {
            $this->_addButton('reinstate', array(
                'label' => Mage::helper('mediotype_ticket')->__('Reinstate Ticket'),
                'class' => 'save',
                'onclick' => 'window.location.href=\'' . $this->getUrl("*/*/reinstate", array("id" => $this->getTicket()->getId())) . '\'',
            ));
        }
    }

    /**
     * @return string
     */
    public function getViewHtml()
    {
        return $this->getChildHtml('content');
    }

    /**
     * @param Mediotype_Ticket_Model_Order $ticket
     * @return Mediotype_Ticket_Block_Adminhtml_Order_View
     */
    public function setTicket($ticket)
    {
        $this->_ticket = $ticket;
        return $this;
    }

    /**
     * @return Mediotype_Ticket_Model_Order
     */
    public function getTicket()
    {
        if (is_null($this->_ticket)) {
            // Load the requested ticket order model
            $id = $this->getRequest()->getParam('id');
            /** @var $model Mediotype_Ticket_Model_Order */
            $this->_ticket = Mage::getModel('mediotype_ticket/order')->load($id);
        }

        return $this->_ticket;
    }
}