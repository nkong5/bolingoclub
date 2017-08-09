<?php

class AW_Eventbooking_Model_Resource_Event_Ticket extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('aw_eventbooking/event_ticket', 'entity_id');
    }

    /**
     * Attach event ticket attribute values to model
     *
     * @param Mage_Core_Model_Abstract $ticket
     * @return AW_Eventbooking_Model_Resource_Event_Ticket
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $ticket)
    {
        foreach ($ticket->getAttributeCollection() as $attribute) {
            $ticket->setData($attribute->getAttributeCode(), $attribute->getValue());
        }
        return $this;
    }
}