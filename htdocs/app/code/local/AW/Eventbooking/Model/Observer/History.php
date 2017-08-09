<?php

class AW_Eventbooking_Model_Observer_History
{
    protected function _createTickets($orderItem, $eventTicket)
    {
        return Mage::getModel('aw_eventbooking/ticket')->createTickets($orderItem, $eventTicket);
    }

    /**
     * Observer for storing data about tickets when has been
     * @param Mage_Sales_Model_Order $order
     * @return $this
     */
    public function addOrderToHistory(Mage_Sales_Model_Order $order)
    {
        $eventBookingItems = Mage::helper('aw_eventbooking/order')->getAllEventbookingItemsFromOrder($order);
        foreach($eventBookingItems as $item) {
            $orderHistoryItems = Mage::getModel('aw_eventbooking/order_history')
                ->getCollection()
                ->addFieldToFilter('order_item_id', array('eq' => $item->getId()));

            if ($orderHistoryItems->getSize()) {
                continue;
            }
            $eventTicketCollection = Mage::helper('aw_eventbooking/order')->getEventTicketFromOrderItem($item);
            foreach ($eventTicketCollection as $eventTicketModel) {
                $eventOrderHistory = Mage::getModel('aw_eventbooking/order_history');
                $eventOrderHistory->setData(
                    array(
                        'event_ticket_id' => $eventTicketModel->getId(),
                        'order_item_id' => $item->getId(),
                        'qty' => $this->_getQtyForOrderItem($eventTicketModel->getId(), $item)
                    )
                );
                try {
                    $eventOrderHistory->save();
                    $this->_createTickets($item, $eventTicketModel);
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
        return $this;
    }

    /**
     * @param int $eventTicketIdToFind
     * @param Mage_Sales_Model_Order_Item $item
     * @return int
     */
    protected function _getQtyForOrderItem($eventTicketIdToFind, $item)
    {
        $qty = 1;
        $options = $item->getProductOptionByCode('options');

        if ($options) {
            foreach ($options as $option) {
                if (AW_Eventbooking_Model_Event::PRODUCT_OPTION_ID . '_' . $eventTicketIdToFind != $option['option_id']) {
                    continue;
                }
                $qty = (int)$option['option_value'];
            }
        }
        return $qty;
    }
}
