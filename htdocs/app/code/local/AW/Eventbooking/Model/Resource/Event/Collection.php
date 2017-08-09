<?php

class AW_Eventbooking_Model_Resource_Event_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('aw_eventbooking/event');
    }

    /**
     * Add attribute values to collection as columns
     * if for current store value not exists
     * default value will be chosen
     *
     * @param int $storeId
     * @return AW_Eventbooking_Model_Resource_Event_Collection
     */
    public function addEventAttributes($storeId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
    {
        foreach (Mage::helper('aw_eventbooking/attribute')->getEventAttributes() as $attributeCode) {
            $defaultStoreAlias = $attributeCode . '_ds';
            $currentStoreAlias = $attributeCode . '_cs';
            $this->getSelect()
                ->joinLeft(
                    array($defaultStoreAlias => $this->getTable('aw_eventbooking/event_attribute')),
                    "main_table.entity_id = {$defaultStoreAlias}.event_id AND
                    {$defaultStoreAlias}.attribute_code = '{$attributeCode}' AND
                    {$defaultStoreAlias}.store_id = " . (int)Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID,
                    array()
                )
                ->joinLeft(
                    array($currentStoreAlias => $this->getTable('aw_eventbooking/event_attribute')),
                    "main_table.entity_id = {$currentStoreAlias}.event_id
                    AND {$currentStoreAlias}.attribute_code = '{$attributeCode}'
                    AND {$currentStoreAlias}.attribute_code = {$defaultStoreAlias}.attribute_code
                    AND {$currentStoreAlias}.store_id = {$storeId}",
                    array($attributeCode => "IFNULL({$currentStoreAlias}.value, {$defaultStoreAlias}.value)")
                )
            ;
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function addIsEnabledFilter()
    {
        $this->addFieldToFilter('is_enabled', array('eq' => 1));
        return $this;
    }

    /**
     * @param array $productIds
     *
     * @return $this
     */
    public function addProductIdsFilter($productIds)
    {
        $this->addFieldToFilter('product_id', array('in' => $productIds));
        return $this;
    }

    /**
     * @return $this
     */
    public function addPendingReminderEmailFilter()
    {
        $this
            ->addFieldToFilter('day_count_before_send_reminder_letter', array('notnull' => 1))
            ->addFieldToFilter('is_reminder_send', array('eq' => 0))
        ;
        return $this;
    }
}