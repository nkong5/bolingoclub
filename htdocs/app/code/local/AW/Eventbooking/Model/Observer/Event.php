<?php

class AW_Eventbooking_Model_Observer_Event
{
    /**
     * @param Varien_Object $observer
     * @return $this
     */
    public function saveEvent($observer)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();
        if (is_null($product->getId()) || !$product->getData('_edit_mode')) {
            return $this;
        }

        $data = Mage::app()->getRequest()->getParam('event', null);
        if (is_null($data) || !is_array($data)) {
            return $this;
        }
        if ($data['is_enabled'] === '0') {
            $event = Mage::getModel('aw_eventbooking/event')->loadByProductId($product->getId());
            if (!is_null($event->getId())) {
                $event->setData('is_enabled', 0);
                $event->save();
            }
            return $this;
        }

        $event = $this->_processEvent($product->getId(), $data, $product->getStoreId());
        if (array_key_exists('event_data', $data)) {
            $this->_processEventAttributes($event, $data['event_data']);
        }
        if (!array_key_exists('ticket_data', $data)) {
            $data['ticket_data'] = array();
        }
        $this->_processTickets($event, $data['ticket_data']);
        return $this;
    }

    /**
     * Observer for validate item before adding to cart
     * checking of event ticket availability
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function duplicateProduct($observer)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();
        if (is_null($product->getId()) || is_null($product->getIsDuplicate()) || is_null($product->getOriginalId())) {
            return $this;
        }
        $originalProductEvent = Mage::getModel('aw_eventbooking/event')
            ->loadByProductId($product->getOriginalId())
        ;
        if (is_null($originalProductEvent->getId())) {//if event for original product does not exist
            return $this;
        }
        $newProductEvent = Mage::getModel('aw_eventbooking/event')
            ->loadByProductId($product->getId())
        ;
        if (!is_null($newProductEvent->getId())) {//if event for new product exists
            return $this;
        }

        $newProductEvent
            ->setData($originalProductEvent->getData())
            ->unsetData('entity_id')
            ->setData('product_id', $product->getId())
        ;
        try {
            $newProductEvent->save();//copy event
        } catch (Exception $e) {
            Mage::logException($e);
            throw new Mage_Core_Exception("Unable duplicate Event Booking section");
        }
        $originalEventAttributeCollection = Mage::getModel('aw_eventbooking/event_attribute')->getCollection()
            ->addFieldToFilter('event_id', array('eq' => $originalProductEvent->getId()))
        ;
        foreach ($originalEventAttributeCollection as $eventAttribute) {
            $newEventAttribute = Mage::getModel('aw_eventbooking/event_attribute')
                ->setData($eventAttribute->getData())
                ->unsetData('entity_id')
                ->setData('event_id', $newProductEvent->getId())
            ;
            try {
                $newEventAttribute->save();//copy event attributes
            } catch (Exception $e) {
                Mage::logException($e);
                throw new Mage_Core_Exception("Unable duplicate Event Booking section");
            }
        }
        $originalTicketCollection = Mage::getResourceModel('aw_eventbooking/event_ticket_collection')
            ->addFilterOnEventId($originalProductEvent->getId())
        ;
        foreach ($originalTicketCollection as $ticket) {
            $newTicket = Mage::getModel('aw_eventbooking/event_ticket')
                ->setData($ticket->getData())
                ->unsetData('entity_id')
                ->setData('event_id', $newProductEvent->getId())
            ;
            try {
                $newTicket->save();//copy event ticket
            } catch (Exception $e) {
                Mage::logException($e);
                throw new Mage_Core_Exception("Unable duplicate Event Booking section");
            }
            $originalTicketAttributeCollection = Mage::getModel('aw_eventbooking/event_ticket_attribute')->getCollection()
                ->addFieldToFilter('ticket_id', array('eq' => $ticket->getId()))
            ;
            foreach ($originalTicketAttributeCollection as $ticketAttribute) {
                $newTicketAttribute = Mage::getModel('aw_eventbooking/event_ticket_attribute')
                    ->setData($ticketAttribute->getData())
                    ->unsetData('entity_id')
                    ->setData('ticket_id', $newTicket->getId())
                ;
                try {
                    $newTicketAttribute->save();//copy event ticket attributes
                } catch (Exception $e) {
                    Mage::logException($e);
                    throw new Mage_Core_Exception("Unable duplicate Event Booking section");
                }
            }
        }
        return $this;
    }

    /**
     * Observer for validate item before adding to cart
     * checking of event ticket availability
     *
     * @param Varien_Object $observer
     * @throws Mage_Core_Exception
     * @return $this
     */
    public function validateOnAddToCart($observer)
    {
        $transport = $observer->getEvent()->getTransport();
        $regExpForDetect = "/" . AW_Eventbooking_Model_Event::PRODUCT_OPTION_ID . "\_(\d+)/";

        foreach ($transport->options as $key => $value) {
            if (preg_match($regExpForDetect, $key, $match) == false) {
                continue;
            }
            $eventTicketId = $match[1];
            /* @var AW_Eventbooking_Model_Event_Ticket */
            $eventTicketModel = Mage::getModel('aw_eventbooking/event_ticket')->load($eventTicketId);
            if (is_null($eventTicketModel)) {
                throw new Mage_Core_Exception(
                    Mage::helper('aw_eventbooking')->__('Ticket does not exist')
                );
            }
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            try {
                $eventTicketModel->validateOnAddToCart($quote, $value);
            } catch (Exception $e) {
                throw new Mage_Core_Exception($e->getMessage());
            }
        }
        return $this;
    }

    /**
     * Observer for validate item before update qty in cart
     *
     * @param Varien_Object $observer
     * @throws Mage_Core_Exception
     * @return $this
     */
    public function validateOnUpdateItemsInCart($observer)
    {
        $cart = $observer->getEvent()->getCart();
        $info = $observer->getEvent()->getInfo();
        foreach ($info as $itemId => $itemInfo) {
            $item = $cart->getQuote()->getItemById($itemId);
            if (!$item) {
                continue;
            }

            $isQuoteItemIsEventProduct = Mage::helper('aw_eventbooking/quote')->isQuoteItemIsEventProduct($item);
            if (!$isQuoteItemIsEventProduct || !empty($itemInfo['remove'])) {
                continue;
            }
            //checking if customer try to update Event product
            if (
                $isQuoteItemIsEventProduct
                && isset($itemInfo['qty'])
                && ($itemInfo['qty'] != $item->getQty())
            ) {
                throw new Mage_Core_Exception(
                    Mage::helper('aw_eventbooking')->__(
                        'Quantity of this product can not be changed, use the "Edit" option instead.')

                );
            }
        }
    }

    /**
     * Observer for validate item on place order
     * checking of event ticket availability
     *
     * @param Varien_Object $observer
     * @throws Exception
     * @return $this
     */
    public function validateOnPlaceOrder($observer)
    {
        /*@var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();
        $eventBookingItems = Mage::helper('aw_eventbooking/order')->getAllEventbookingItemsFromOrder($order);
        foreach($eventBookingItems as $item) {
            $eventTicketCollection = Mage::helper('aw_eventbooking/order')->getEventTicketFromOrderItem($item);
            foreach ($eventTicketCollection as $eventTicketModel) {
                if (is_null($eventTicketModel)) {
                    throw new Mage_Core_Exception(
                        Mage::helper('aw_eventbooking')->__('Ticket does not exist')
                    );
                }
                try {
                    $eventTicketModel->validateOnPlaceOrder($item);
                } catch (Exception $e) {
                    throw new Mage_Core_Exception($e->getMessage());
                }
            }
        }
        return $this;
    }

    /**
     * Observer for send confirmation emails on every event
     *
     * @param Mage_Sales_Model_Order $order
     * @return $this
     */
    public function sendConfirmationEmail($order)
    {
        if (!$order->getBaseTotalDue()) {
            $eventBookingItems = Mage::helper('aw_eventbooking/order')->getAllEventbookingItemsFromOrder($order);
            foreach ($eventBookingItems as $item) {
                try {
                    Mage::helper('aw_eventbooking/mailer')->sendConfirmationEmailForOrderItem($item);
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
        return $this;
    }

    /**
     * Observer for send reminder emails to customer after invoice
     * Reminder email will be send if reminders did send via cron to other customer early
     *
     * @param Mage_Sales_Model_Order $order
     * @return $this
     */
    public function sendReminderAfterInvoice($order)
    {
        $eventBookingItems = Mage::helper('aw_eventbooking/order')->getAllEventbookingItemsFromOrder($order);
        foreach ($eventBookingItems as $orderItem) {
            if ($orderItem->getQtyInvoiced() <= $orderItem->getQtyRefunded()) {
                continue;
            }
            $eventTicketCollection = Mage::helper('aw_eventbooking/order')->getEventTicketFromOrderItem($orderItem);
            $event = $eventTicketCollection->getFirstItem()->getEventModel();
            if (!$event->getIsReminderSend()) {
                continue;//don't send if reminder did not send by cron early
            }
            if (is_null($event->getDayCountBeforeSendReminderLetter())) {
                continue;//don't send if sending reminder disabled
            }
            try {
                Mage::helper('aw_eventbooking/mailer')->sendReminderEmailForOrderItem($orderItem);
                Mage::helper('aw_eventbooking/mailer')->sendReminderEmailForHolderByOrderItem($orderItem);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        return $this;
    }

    /**
     * @param $dateTime
     * @param $errorMessage
     * @return Zend_Date
     * @throws Mage_Core_Exception
     */
    protected function _parseDateTime($dateTime, $errorMessage)
    {
        // Concert datetime to UTC
        $locale = Mage::app()->getLocale();
        $dateFormat = $locale->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        try {
            $zDate = $locale->utcDate(null, $dateTime, true, $dateFormat);
        } catch (Zend_Date_Exception $e) {
            throw new Mage_Core_Exception($errorMessage);
        }
        return $zDate;
    }

    protected function _getNullIfEmpty($value)
    {
        return $value ? $value : null;
    }

    protected function _processDatesInData($data)
    {
        /** @var AW_Eventbooking_Helper_Data $helper */
        $helper = Mage::helper('aw_eventbooking');
        $data['event_start_date'] = $this->_parseDateTime($data['event_start_date'], $helper->__("Invalid event start date"));
        $data['event_end_date'] = $data['event_end_date']
            ? $this->_parseDateTime($data['event_end_date'], $helper->__("Invalid event end date"))
            : null;
        if ($data['event_end_date'] && $data['event_start_date']) {
            if ($data['event_end_date']->getTimestamp() < $data['event_start_date']->getTimestamp()) {
                throw new Mage_Core_Exception($helper->__('Event End Date should be greater than Start Date'));
            }
        }
        if ($data['event_start_date']) {
            $data['event_start_date'] = $data['event_start_date']->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        }
        if ($data['event_end_date']) {
            $data['event_end_date'] = $data['event_end_date']->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        }
        return $data;
    }

    /**
     * Process event
     * Save event related for current product
     *
     * @param int $productId
     * @param array $data
     * @param int $storeId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID
     * @throws Mage_Core_Exception
     * @return AW_Eventbooking_Model_Event
     */
    protected function _processEvent($productId, $data, $storeId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
    {
        /** @var AW_Eventbooking_Model_Event $event */
        $event = Mage::getModel('aw_eventbooking/event')->loadByProductId($productId);

        $data = $this->_processDatesInData($data);

        $data['location'] = $this->_getNullIfEmpty($data['location']);
        /* If admin can't redeem current event - he can't change redeem roles */
        if (!$event->isCurrentAdminCanRedeem()) {
            unset($data['redeem_roles']);
        }

        if (strlen($data['day_count_before_send_reminder_letter']) === 0) {
            $data['day_count_before_send_reminder_letter'] = null;
        }
        $event->setData('product_id', $productId);
        $event->addData($data)->save();
        $event->setStoreId($storeId);
        return $event;
    }

    /**
     * Process Event attributes.
     * Save new values.
     * Delete values for sub-store if use default value
     *
     * @param AW_Eventbooking_Model_Event $event
     * @param array $data
     * @return AW_Eventbooking_Model_Observer_Event
     */
    protected function _processEventAttributes($event, $data)
    {
        foreach (Mage::helper('aw_eventbooking/attribute')->getEventAttributes() as $attributeCode) {
            $eventAttribute = $event->getAttributeByCode($attributeCode);
            if (
                !is_null($eventAttribute->getId())
                && array_key_exists('default', $data)
                && is_array($data['default'])
                && array_key_exists($eventAttribute->getAttributeCode(), $data['default'])
            ) {
                // Delete unused attributes
                $eventAttribute->delete();
            } elseif (isset($data['value']) && is_array($data['value'])) {
            // Save new attribute values
                if (isset($data['value'][$attributeCode])) {
                    $eventAttribute->addData(
                        array(
                            'event_id'       => $event->getId(),
                            'store_id'       => $event->getStoreId(),
                            'attribute_code' => $attributeCode,
                            'value'          => $data['value'][$attributeCode],
                        )
                    );
                    $eventAttribute->save();
                }
            }
        }
        return $this;
    }

    /**
     * Process tickets
     * Deleted tickets removed from db
     * New ticket types save to db
     *
     * @param AW_Eventbooking_Model_Event $event
     * @param array $data
     * @throws Mage_Core_Exception
     * @return AW_Eventbooking_Model_Observer_Event
     */
    protected function _processTickets($event, $data)
    {
        $existsTicketCount = 0;
        foreach($data as $ticketData) {
            if (!array_key_exists('delete', $ticketData) || !$ticketData['delete']) {
                $existsTicketCount++;
            }
        }
        if ($existsTicketCount === 0) {
            throw new Mage_Core_Exception("Event must contain ticket(s)");
        }
        foreach($data as $ticketId => $ticketData) {
            $ticketModel = Mage::getModel('aw_eventbooking/event_ticket')->setStoreId($event->getStoreId());
            $ticketModel->load(intval($ticketId));
            if (array_key_exists('delete', $ticketData) && $ticketData['delete']) {
                if (!is_null($ticketModel->getId())) {
                    $ticketModel->delete();
                }
            } else {
                // save ticket
                $ticketModel->setData('event_id', $event->getId());
                $ticketModel->addData($ticketData['global']);
                $ticketModel->save();

                $this->_processTicketAttributes($ticketModel, $ticketData);
            }
        }
        return $this;
    }

    /**
     * Process Ticket attributes
     * Delete attributes if using default value
     * Save new values to changed attributes
     *
     * @param AW_Eventbooking_Model_Event_Ticket $ticket
     * @param array $data
     * @return bool
     */
    protected function _processTicketAttributes($ticket, $data)
    {
        foreach (Mage::helper('aw_eventbooking/attribute')->getTicketAttributes() as $attributeCode) {
            $ticketAttribute = $ticket->getAttributeByCode($attributeCode);
            if (
                !is_null($ticketAttribute->getId())
                && array_key_exists('default', $data)
                && is_array($data['default'])
                && array_key_exists($ticketAttribute->getAttributeCode(), $data['default'])
            ) {
                // Delete unused attributes
                $ticketAttribute->delete();
            } elseif (isset($data['value']) && is_array($data['value'])) {
                // Save new attribute values
                if (isset($data['value'][$attributeCode])) {
                    $ticketAttribute->addData(
                        array(
                            'ticket_id'      => $ticket->getId(),
                            'store_id'       => $ticket->getStoreId(),
                            'attribute_code' => $attributeCode,
                            'value'          => $data['value'][$attributeCode],
                        )
                    );
                    $ticketAttribute->save();
                }
            }
        }
        return $this;
    }

    protected function _saveTicketAttribute($ticket, $attributeCode, $value, $storeId = 0)
    {
        $attribute = $ticket->getAttributeByCode($attributeCode, $storeId);
        $attribute->addData(
            array(
                'ticket_id'       => $ticket->getId(),
                'store_id'        => $storeId,
                'attribute_code'  => $attributeCode,
                'value'           => $value,
            )
        )->save();
        return $attribute;
    }

    protected function _deleteTicketAttribute($ticket, $attributeCode, $storeId = 0)
    {
        $attribute = $ticket->getAttributeByCode($attributeCode, $storeId);
        $attribute->delete();
        return true;
    }
}
