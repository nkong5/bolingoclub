<?php

class AW_Eventbooking_Model_Resource_Order_History_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('aw_eventbooking/order_history');
    }

    /**
     * @param array $orderItemIds
     *
     * @return $this
     */
    public function addFilterOnOrderItemIds($orderItemIds)
    {
        $this->addFieldToFilter('order_item_id', array('in' => $orderItemIds));
        return $this;
    }

    /**
     * @param AW_Eventbooking_Model_Event_Ticket $eventTicket
     *
     * @return $this
     */
    public function addFilterOnEventTicket(AW_Eventbooking_Model_Event_Ticket $eventTicket)
    {
        $this->addFieldToFilter('event_ticket_id', $eventTicket->getId());
        return $this;
    }

    /**
     * @param $collection
     *
     * @return AW_Eventbooking_Model_Resource_Order_History_Collection
     */
    public function addFilterOnEventTicketCollection($collection)
    {
        $eventTicketIdsSelect = clone $collection->getSelect();
        $eventTicketIdsSelect->reset(Zend_Db_Select::COLUMNS);
        $eventTicketIdsSelect->columns(
            'main_table.entity_id'
        );
        $this->addFieldToFilter(
            'event_ticket_id',
            array('in' => $eventTicketIdsSelect)
        );
        return $this;
    }

    /**
     *
     * @return Mage_Sales_Model_Mysql4_Order_Item_Collection
     */
    public function getRelatedOrderItemCollection()
    {
        $orderItemCollection = Mage::getModel('sales/order_item')->getCollection();
        $orderItemIdsSelect = clone $this->getSelect();
        $orderItemIdsSelect->reset(Zend_Db_Select::COLUMNS);
        $orderItemIdsSelect->columns(
            'main_table.order_item_id'
        );
        $orderItemCollection->addFieldToFilter(
            'item_id',
            array('in' => $orderItemIdsSelect)
        );
        return $orderItemCollection;
    }

    /**
     *
     * @return AW_Eventbooking_Model_Resource_Ticket_Collection
     */
    public function getTicketsWithRelatedOrderItemCollection()
    {
        $ticketsCollection = Mage::getModel('aw_eventbooking/ticket')->getCollection();
        $orderItemIdsSelect = clone $this->getSelect();
        $orderItemIdsSelect->reset(Zend_Db_Select::COLUMNS);
        $orderItemIdsSelect->columns(
            'main_table.order_item_id'
        );
        $ticketsCollection->addFieldToFilter(
            'item_id',
            array('in' => $orderItemIdsSelect)
        );
        $ticketsCollection->getSelect()
            ->joinLeft(
                array('order_item_table' => $ticketsCollection->getTable('sales/order_item')),
                'main_table.order_item_id = order_item_table.item_id',
                array('order_item_table.*')
            );

        return $ticketsCollection;
    }

    /**
     * @return $this
     */
    public function addOrderItemsToCollection()
    {
        $this->getSelect()
            ->joinLeft(
                array('order_item_table' => $this->getTable('sales/order_item')),
                'main_table.order_item_id = order_item_table.item_id',
                array('order_item_table.*')
            );

        return $this;
    }
}