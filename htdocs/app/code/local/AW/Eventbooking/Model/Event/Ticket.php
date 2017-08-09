<?php

class AW_Eventbooking_Model_Event_Ticket extends Mage_Core_Model_Abstract
{
    protected $_storeId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    protected $_event;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('aw_eventbooking/event_ticket');
    }

    /**
     * Get assigned event model
     *
     * @return AW_Eventbooking_Model_Event
     */
    public function getEventModel()
    {
        $eventId = $this->getData('event_id');
        if ($eventId && is_null($this->_event)) {
            /** @var AW_Eventbooking_Model_Event $event */
            $event = Mage::getModel('aw_eventbooking/event')
                ->setStoreId($this->getStoreId())
                ->load($eventId);
            if ($event->getId()) {
                $this->_event = $event;
            }
        }
        return $this->_event;
    }

    /**
     * @return AW_Eventbooking_Model_Resource_Order_History_Collection
     */
    public function getOrderHistoryCollection()
    {
        /* @var $collection AW_Eventbooking_Model_Resource_Order_History_Collection */
        $collection = Mage::getModel('aw_eventbooking/order_history')->getCollection();
        $collection->addFilterOnEventTicket($this);
        return $collection;
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     * @param int $requestQty
     * @throws Exception
     * @return AW_Eventbooking_Model_Event_Ticket
     */
    public function validateOnAddToCart($quote, $requestQty)
    {
        $this->getEventModel()->validateOnAddToCart();

        if ($requestQty < 1) {
            return $this;
        }
        //check on available stock
        $availableQty = $this->getQty() - $this->getPurchasedTicketQty();
        $usedItemQty = 0; //get item qty in quote
        $quoteItemList = Mage::helper('aw_eventbooking/quote')->getAllEventbookingItemsFromQuote($quote);
        foreach ($quoteItemList as $item) {
            $eventTicket = Mage::helper('aw_eventbooking/quote')->getEventTicketFromQuoteItem($item);
            if (!is_null($eventTicket) && $eventTicket->getId() === $this->getId()) {
                $usedItemQty += $item->getQty();
                break;
            }
        }
        if ($availableQty < ($requestQty + $usedItemQty)) {
            $productName = "";
            $product = $this->getEventModel()->getProductModel();
            if (!is_null($product)) {
                $productName = $product->getName();
            }
            $exceptionMessage = Mage::helper('aw_eventbooking')->__(
                "Only %s tickets are available for %s of type %s",
                $availableQty, $productName, $this->getTitle()
            );
            throw new Exception($exceptionMessage);
        }
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @throws Exception
     * @return AW_Eventbooking_Model_Event_Ticket
     */
    public function validateOnPlaceOrder(Mage_Sales_Model_Order_Item $item)
    {
        $this->getEventModel()->validateOnPlaceOrder();
        //check on available stock
        $availableQty = $this->getQty() - $this->getPurchasedTicketQty();
        $requestdQty = $this->getRequestedQtyForOrderItem($item);
        if ($availableQty < $requestdQty) {
            $productName = "";
            $product = $this->getEventModel()->getProductModel();
            if (!is_null($product)) {
                $productName = $product->getName();
            }
            $exceptionMessage = Mage::helper('aw_eventbooking')->__(
                "Only %s tickets are available for %s of type %s",
                $availableQty, $productName, $this->getTitle()
            );
            throw new Exception($exceptionMessage);
        }
        return $this;
    }

    /**
     * @return AW_Eventbooking_Model_Resource_Event_Ticket_Attribute_Collection
     */
    public function getAttributeCollection()
    {
        return Mage::getResourceModel('aw_eventbooking/event_ticket_attribute_collection')
            ->getAttributeCollectionByStoreId($this->getId(), $this->getStoreId())
        ;
    }

    public function getAttributeByCode($attributeCode)
    {
        return Mage::getModel('aw_eventbooking/event_ticket_attribute')
            ->getTicketAttributeByCode($this->getId(), $attributeCode, $this->getStoreId())
        ;
    }

    public function setStoreId($storeId)
    {
        $this->_storeId = (int)$storeId;
        return $this;
    }

    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * @see Mage_Sales_Model_Order_Item -> getStatusId
     * @return float
     */
    public function getPurchasedTicketQty()
    {
        $orderItemCollection = $this->getOrderHistoryCollection()
            ->addOrderItemsToCollection()
        ;
        $orderItemCollection->addExpressionFieldToSelect(
            'sum_qty_purchased',
            'SUM(({{o}} - {{c}} - {{r}}) * {{q}})',
            array(
                'o' => 'order_item_table.qty_ordered',
                'c' => 'order_item_table.qty_canceled',
                'r' => 'order_item_table.qty_refunded',
                'q' => 'main_table.qty'
            )
        );

        $purchasedQty = 0;
        $item = $orderItemCollection->getFirstItem();
        if (!is_null($item->getId())) {
             $purchasedQty = $item->getData('sum_qty_purchased');
        }
        return $purchasedQty;
    }

    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @return int
     */
    public function getRequestedQtyForOrderItem(Mage_Sales_Model_Order_Item $item)
    {
        $options = $item->getProductOptionByCode('options');
        $requestQty = 0;

        foreach ($options as $key => $option) {
            if (AW_Eventbooking_Model_Event::PRODUCT_OPTION_ID . '_' . $this->getId() != $option['option_id']) {
                continue;
            }
            $requestQty = $option['option_value'];
        }
        return (int)$requestQty;
    }
}
