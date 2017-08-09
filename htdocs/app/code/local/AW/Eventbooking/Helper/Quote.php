<?php

class AW_Eventbooking_Helper_Quote
{
    /**
     * @param Mage_Sales_Model_Quote $quote
     *
     * @return array
     */
    public function getAllEventbookingItemsFromQuote(Mage_Sales_Model_Quote $quote)
    {
        $eventItems = array();
        if (is_null($quote)) {
            return $eventItems;
        }
        foreach ($quote->getItemsCollection() as $item) {
            if ($this->isQuoteItemIsEventProduct($item)) {
                $eventItems[] = $item;
            }
        }
        return $eventItems;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $item
     *
     * @return bool
     */
    public function isQuoteItemIsEventProduct(Mage_Sales_Model_Quote_Item $item)
    {
        $event = Mage::getModel('aw_eventbooking/event')
            ->loadByProductId($item->getProduct()->getId())
        ;
        return !is_null($event->getId());
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $item
     *
     * @return AW_Eventbooking_Model_Event_Ticket|null
     */
    public function getEventTicketFromQuoteItem(Mage_Sales_Model_Quote_Item $item)
    {
        $optionList = $item->getOptionsByCode();
        $regExpForDetect = "/" . AW_Eventbooking_Model_Event::PRODUCT_OPTION_ID . "\_(\d+)/";

        foreach ($optionList as $key => $value) {
            if (preg_match($regExpForDetect, $key, $match) == false) {
                continue;
            }
            $eventTicketId = $match[1];
            $eventTicketModel = Mage::getModel('aw_eventbooking/event_ticket')->load($eventTicketId);
            if (is_null($eventTicketModel->getId())) {
                return null;
            }
            return $eventTicketModel;
        }
        return null;
    }
}