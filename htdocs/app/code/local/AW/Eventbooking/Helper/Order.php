<?php

class AW_Eventbooking_Helper_Order
{
    /**
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function getAllEventbookingItemsFromOrder(Mage_Sales_Model_Order $order)
    {
        $eventItems = array();
        if (is_null($order)) {
            return $eventItems;
        }
        foreach ($order->getAllItems() as $item) {
            if ($this->isOrderItemIsEventProduct($item)) {
                $eventItems[] = $item;
            }
        }
        return $eventItems;
    }

    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @return bool
     */
    public function isOrderItemIsEventProduct(Mage_Sales_Model_Order_Item $item)
    {
        $eventTicketId = $this->getEventTicketIdFromOrderItem($item);
        if (!count($eventTicketId)) {
            return false;
        }
        return true;
    }

    /**
     * @param Mage_Sales_Model_Order_Item $item
     *
     * @return array
     */
    public function getEventTicketIdFromOrderItem(Mage_Sales_Model_Order_Item $item)
    {
        $result = array();
        $options = $item->getProductOptionByCode('options');
        if (!is_array($options)) {
            return $result;
        }
        $regExpForDetect = "/" . AW_Eventbooking_Model_Event::PRODUCT_OPTION_ID . "\_(\d+)/";
        foreach ($options as $option) {
            if (preg_match($regExpForDetect, $option['option_id'], $match) == false) {
                continue;
            }
            $result[$match[1]] = $option['option_value'];
        }
        return $result;
    }

    /**
     * @param Mage_Sales_Model_Order_Item $item
     *
     * @return AW_Eventbooking_Model_Resource_Event_Ticket_Collection
     */
    public function getEventTicketFromOrderItem(Mage_Sales_Model_Order_Item $item)
    {
        $eventTicketIds = $this->getEventTicketIdFromOrderItem($item);
        $ids = array();
        foreach ($eventTicketIds as $eventTicketId => $qty) {
            if ($qty) {
                $ids[] = $eventTicketId;
            }
        }
        $eventTicketCollection = Mage::getModel('aw_eventbooking/event_ticket')->getCollection()
                ->addFieldToFilter('entity_id', array('in' => $ids));

        return $eventTicketCollection;
    }


    /**
     * @param Mage_Sales_Model_Mysql4_Order_Item_Collection $collection
     * @return Mage_Sales_Model_Mysql4_Order_Item_Collection
     */
    public function addOrderInfoToOrderItemCollection($collection)
    {
        $collection->getSelect()
            ->joinLeft(
                array('order_table' => $collection->getTable('sales/order')),
                "order_item_table.order_id = order_table.entity_id",
                array(
                    'order_increment_id' => "order_table.increment_id",
                    'order_customer_id' => "order_table.customer_id",
                    'order_customer_email' => "order_table.customer_email",
                    'order_customer_name' => "CONCAT(order_table.customer_firstname, ' ', order_table.customer_lastname)",
                )
            );
        return $collection;
    }

    /**
     * @param Mage_Sales_Model_Mysql4_Order_Item_Collection $collection
     * @return Mage_Sales_Model_Mysql4_Order_Item_Collection
     */
    public function addEventTicketInfoToOrderItemCollection($collection)
    {
        $collection->getSelect()
            ->joinLeft(
                array('event_ticket_table' => $collection->getTable('aw_eventbooking/event_ticket')),
                "main_table.event_ticket_id = event_ticket_table.entity_id",
                array()
            );

        //add event ticket data
        $storeId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
        foreach (Mage::helper('aw_eventbooking/attribute')->getTicketAttributes() as $attributeCode) {
            $defaultStoreAlias = 'event_ticket_attribute_' . $attributeCode . '_ds';
            $currentStoreAlias = 'event_ticket_attribute_' . $attributeCode . '_cs';
            $collection->getSelect()
                ->joinLeft(
                    array($defaultStoreAlias => $collection->getTable('aw_eventbooking/event_ticket_attribute')),
                    "event_ticket_table.entity_id = {$defaultStoreAlias}.ticket_id AND
                    {$defaultStoreAlias}.attribute_code = '{$attributeCode}' AND
                    {$defaultStoreAlias}.store_id = " . (int)Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID,
                    array()
                )
                ->joinLeft(
                    array($currentStoreAlias => $collection->getTable('aw_eventbooking/event_ticket_attribute')),
                    "event_ticket_table.entity_id = {$currentStoreAlias}.ticket_id
                    AND {$currentStoreAlias}.attribute_code = '{$attributeCode}'
                    AND {$currentStoreAlias}.attribute_code = {$defaultStoreAlias}.attribute_code
                    AND {$currentStoreAlias}.store_id = {$storeId}",
                    array('event_ticket_' . $attributeCode => "IFNULL({$currentStoreAlias}.value, {$defaultStoreAlias}.value)")
                );
        }
        return $collection;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getCustomerNameFromOrder(Mage_Sales_Model_Order $order)
    {
        if (!$order->getId()) {
            return '';
        }
        if ($order->getData('customer_is_guest')) {
            return $order->getBillingAddress()->getName();
        } else {
            return $order->getCustomerName();
        }
    }

    public function getCustomerFirstNameFromOrder(Mage_Sales_Model_Order $order)
    {
        if ($order->getCustomerIsGuest()) {
            return $order->getBillingAddress()->getFirstname();
        } else {
            return $order->getCustomerFirstname();
        }
    }
}
