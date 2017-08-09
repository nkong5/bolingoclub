<?php

class AW_Eventbooking_Model_Order_History extends Mage_Core_Model_Abstract
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('aw_eventbooking/order_history');
    }

    /**
     * @return AW_Eventbooking_Model_Event_Ticket|null
     */
    public function getEventTicketModel()
    {
        $eventTicketModel = Mage::getModel('aw_eventbooking/event_ticket')->load($this->getEventTicketId());
        if (is_null($eventTicketModel->getId())) {
            return null;
        }
        return $eventTicketModel;
    }

    /**
     * @return Mage_Sales_Model_Order_Item|null
     */
    public function getOrderItemModel()
    {
        $orderItemModel = Mage::getModel('sales/order_item')->load($this->getOrderItem());
        if (is_null($orderItemModel->getId())) {
            return null;
        }
        return $orderItemModel;
    }

    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @return AW_Eventbooking_Model_Order_History
     */
    public function loadByOrderItem(Mage_Sales_Model_Order_Item $item)
    {
        return $this->load($item->getId(), 'order_item_id');
    }

}