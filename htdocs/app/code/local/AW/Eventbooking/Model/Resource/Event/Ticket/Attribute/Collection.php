<?php

class AW_Eventbooking_Model_Resource_Event_Ticket_Attribute_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('aw_eventbooking/event_ticket_attribute');
    }

    /**
     * Get attribute collection for store,
     * if for current store value does n't exists,
     * than default store value returned
     *
     * @param int $ticketId
     * @param int $storeId
     * @return AW_Eventbooking_Model_Resource_Event_Ticket_Attribute_Collection
     */
    public function getAttributeCollectionByStoreId($ticketId, $storeId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
    {
        $this->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(array('entity_id', 'ticket_id', 'attribute_code'))
            ->where('main_table.ticket_id = ?', $ticketId)
            ->where('main_table.store_id = ?', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
            ->joinLeft(
                array('current_store_attribute' => $this->getTable('aw_eventbooking/event_ticket_attribute')),
                "main_table.attribute_code = current_store_attribute.attribute_code
                AND main_table.ticket_id = current_store_attribute.ticket_id
                AND current_store_attribute.store_id = {$storeId}",
                array(
                    'value'    => "IFNULL(current_store_attribute.value, main_table.value)",
                    'store_id' => "IFNULL(current_store_attribute.store_id, main_table.store_id)",
                )
            )
        ;
        return $this;
    }
}