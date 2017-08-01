<?php
/**
 * Magento / Mediotype Module
 *
 *
 * @desc
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Block_Adminhtml_Ticket_View_Info
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Block_Adminhtml_Ticket_View_Info extends Mage_Adminhtml_Block_Template
{
    /**
     *
     */
    public function __construct(){
        parent::__construct();

        $this->setTemplate("mediotype/ticket/view/info.phtml");

    }

    /**
     * @return Mediotype_Ticket_Model_Order
     */
    public function getTicket(){
        $id = $this->getRequest()->getParam('id');
        return Mage::getModel('mediotype_ticket/order')->load($id);
    }

}