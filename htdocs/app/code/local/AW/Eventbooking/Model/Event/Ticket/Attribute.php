<?php

class AW_Eventbooking_Model_Event_Ticket_Attribute extends Mage_Core_Model_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('aw_eventbooking/event_ticket_attribute');
    }

    /**
     * @param int $ticketId
     * @param string $attributeCode
     * @param int $storeId
     *
     * @return AW_Eventbooking_Model_Event_Ticket_Attribute
     */
    public function getTicketAttributeByCode($ticketId, $attributeCode, $storeId)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('ticket_id', $ticketId)
            ->addFieldToFilter('store_id', $storeId)
            ->addFieldToFilter('attribute_code', $attributeCode)
        ;
        $collection->getSelect()->limit(1);
        return $collection->getFirstItem();
    }
}