<?php
/**
 * Magento / Mediotype Module
 *
 *
 * @desc
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Block_Adminhtml_Reports_Scan
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Block_Adminhtml_Reports_Scan extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     *
     */
    public function __construct()
    {
        $this->_blockGroup = 'mediotype_ticket';
        $this->_controller = 'adminhtml_reports_scan';
        $this->_headerText = $this->__('Ticket Scan Report');

        parent::__construct();

        $this->_removeButton('add');
    }
}