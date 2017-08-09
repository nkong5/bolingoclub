<?php

class AW_Eventbooking_Model_Event_Attribute extends Mage_Core_Model_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('aw_eventbooking/event_attribute');
    }

    /**
     * @param int $eventId
     * @param string $attributeCode
     * @param int $storeId
     *
     * @return AW_Eventbooking_Model_Event_Attribute
     */
    public function getEventAttributeByCode($eventId, $attributeCode, $storeId)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('event_id', $eventId)
            ->addFieldToFilter('store_id', $storeId)
            ->addFieldToFilter('attribute_code', $attributeCode)
        ;
        $collection->getSelect()->limit(1);
        return $collection->getFirstItem();
    }
}