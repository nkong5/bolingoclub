<?php

class AW_Eventbooking_Model_Resource_Event_Attribute_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('aw_eventbooking/event_attribute');
    }

    /**
     * Get attribute collection for store,
     * if for current store value does n't exists,
     * than default store value returned
     *
     * @param int $eventId
     * @param int $storeId
     * @return AW_Eventbooking_Model_Resource_Event_Attribute_Collection
     */
    public function getAttributeCollectionByStoreId($eventId, $storeId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
    {
        $this->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(array('entity_id', 'event_id', 'attribute_code'))
            ->where('main_table.event_id = ?', $eventId)
            ->where('main_table.store_id = ?', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
            ->joinLeft(
                array('current_store_attribute' => $this->getTable('aw_eventbooking/event_attribute')),
                "main_table.attribute_code = current_store_attribute.attribute_code
                AND main_table.event_id = current_store_attribute.event_id
                AND current_store_attribute.store_id = $storeId",
                array(
                    'value'    => 'IFNULL(current_store_attribute.value, main_table.value)',
                    'store_id' => "IFNULL(current_store_attribute.store_id, main_table.store_id)",
                )
            )
        ;
        return $this;
    }

    /**
     * @param string $attributeCode
     *
     * @return $this
     */
    public function addFilterOnAttributeCode($attributeCode)
    {
        $this->addFieldToFilter('attribute_code', array('eq' => $attributeCode));
        return $this;
    }
}