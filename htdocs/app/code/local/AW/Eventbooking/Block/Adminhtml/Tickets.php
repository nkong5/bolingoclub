<?php

class AW_Eventbooking_Block_Adminhtml_Tickets extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_tickets';
        $this->_blockGroup = 'aw_eventbooking';
        $this->_headerText = $this->__('Manage Tickets');
        parent::__construct();
        $this->_removeButton('add');
    }
}
