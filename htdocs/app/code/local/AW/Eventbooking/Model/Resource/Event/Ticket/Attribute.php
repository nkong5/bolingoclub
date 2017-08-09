<?php

class AW_Eventbooking_Model_Resource_Event_Ticket_Attribute extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('aw_eventbooking/event_ticket_attribute', 'entity_id');
    }
}