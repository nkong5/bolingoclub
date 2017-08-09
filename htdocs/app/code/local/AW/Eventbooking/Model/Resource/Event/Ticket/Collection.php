<?php

class AW_Eventbooking_Model_Resource_Event_Ticket_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('aw_eventbooking/event_ticket');
    }

    /**
     * @param int $eventId
     *
     * @return $this
     */
    public function addFilterOnEventId($eventId)
    {
        $this->addFieldToFilter('main_table.event_id', intval($eventId));
        return $this;
    }

    /**
     * Join attribute values to collection
     *
     * @param int $storeId
     * @return AW_Eventbooking_Model_Resource_Event_Ticket_Collection
     */
    public function addTicketAttributes($storeId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
    {
        foreach (Mage::helper('aw_eventbooking/attribute')->getTicketAttributes() as $attributeCode) {
            $defaultStoreAlias = $attributeCode . '_ds';
            $currentStoreAlias = $attributeCode . '_cs';
            $this->getSelect()
                ->joinLeft(
                    array($defaultStoreAlias => $this->getTable('aw_eventbooking/event_ticket_attribute')),
                    "main_table.entity_id = {$defaultStoreAlias}.ticket_id AND
                    {$defaultStoreAlias}.attribute_code = '{$attributeCode}' AND
                    {$defaultStoreAlias}.store_id = " . (int)Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID,
                    array()
                )
                ->joinLeft(
                    array($currentStoreAlias => $this->getTable('aw_eventbooking/event_ticket_attribute')),
                    "main_table.entity_id = {$currentStoreAlias}.ticket_id
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
     * @param int|0 $price
     * @return AW_Eventbooking_Model_Resource_Event_Ticket_Collection
     */
    public function addOrderHistoryTotals($price = 0)
    {
        $purchasedQtyExpression = "SUM((order_item_table.qty_invoiced - order_item_table.qty_refunded) * "
            . "event_order_history_table.qty)";
        $currentRevenueExpression =
            "SUM(({$price} + main_table.price) * "
            . "((order_item_table.qty_invoiced - order_item_table.qty_refunded) * event_order_history_table.qty))";
        $this->getSelect()
            ->joinLeft(
                array('event_order_history_table' => $this->getTable('aw_eventbooking/order_history')),
                "main_table.entity_id = event_order_history_table.event_ticket_id",
                array()
            )
            ->joinLeft(
                array('order_item_table' => $this->getTable('sales/order_item')),
                "event_order_history_table.order_item_id = order_item_table.item_id",
                array(
                    "purchased_qty"         => "IFNULL({$purchasedQtyExpression}, 0)",
                    "available_qty"         => "IFNULL((main_table.qty - IFNULL({$purchasedQtyExpression}, 0)), 0)",
                    "current_revenue"       => "IFNULL({$currentRevenueExpression}, 0)",
                )
            )
            ->group("main_table.entity_id")
        ;
        return $this;
    }

}