<?php

class AW_Eventbooking_Helper_Mailer
{
    const DEFAULT_CONFIRMATION_TEMPLATE = 'aw_eventbooking_template_confirm';
    const PDF_NAME = 'tickets.pdf';

    /**
     * @param array $ticketIds
     * @param string $subject
     * @param string $body
     * @return AW_Eventbooking_Helper_Mailer
     */
    public function sendMessageToAttendees($ticketIds, $subject, $body)
    {
        $orderHistoryCollection = Mage::getModel('aw_eventbooking/order_history')->getCollection();
        $orderItemCollection = $orderHistoryCollection->getTicketsWithRelatedOrderItemCollection();
        $orderItemCollection->addFieldToFilter('id', array('in' => $ticketIds));
        $emailDataList = array();
        foreach ($orderItemCollection as $item) {
            $order = $item->getOrder();
            if (is_null($order)) {
                continue;
            }
            $senderName = Mage::getStoreConfig('trans_email/ident_general/name', $item->getStoreId());
            $senderEmail = Mage::getStoreConfig('trans_email/ident_general/email', $item->getStoreId());
            $emailDataList[$item->getId()] = array(
                'customer_name' => Mage::helper('aw_eventbooking/order')->getCustomerNameFromOrder($order),
                'customer_email' => $order->getCustomerEmail(),
                'sender_name' => $senderName,
                'sender_email' => $senderEmail,
            );
        }

        //removing duplicate customer_email
        $customerEmailList = array();
        foreach ($emailDataList as $key => $item) {
            $customerEmailList[$key] = $item['customer_email'];
        }
        $customerEmailList = array_unique($customerEmailList);
        $emailDataList = array_intersect_key($emailDataList, $customerEmailList);

        foreach ($emailDataList as $emailData) {
            $mail = new Zend_Mail('utf-8');
            $mail
                ->setFrom($emailData['sender_email'], $emailData['sender_name'])
                ->addTo($emailData['customer_email'], $emailData['customer_name'])
                ->setSubject($subject)
                ->setBodyHtml($body);
            try {
                $mail->send();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return array
     */
    protected function _getPDFAttachment(Mage_Sales_Model_Order_Item $orderItem)
    {
        /** @var AW_Eventbooking_Model_Resource_Ticket_Collection $ticketsCollection */
        $ticketsCollection = Mage::getModel('aw_eventbooking/ticket')->getCollection()
            ->addFieldToFilter('order_item_id', array('eq' => $orderItem->getId()))
            ->addPaymentStatusFilter();

        /** @var AW_Eventbooking_Helper_Ticket $ticketHelper */
        $ticketHelper = Mage::helper('aw_eventbooking/ticket');
        $attachments = $ticketHelper->renderTicketsPdf($ticketsCollection);

        return $attachments;
    }

    /**
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @throws Exception
     * @return AW_Eventbooking_Helper_Mailer
     */
    public function sendConfirmationEmailForOrderItem(Mage_Sales_Model_Order_Item $orderItem)
    {
        $eventTicketCollection = Mage::helper('aw_eventbooking/order')->getEventTicketFromOrderItem($orderItem);
        $eventTicket = $eventTicketCollection->getFirstItem();
        if ($eventTicket->getId()) {
            $event = $eventTicket->getEventModel();
            $templateId = $event->getConfirmationTemplateId();

            if ($templateId === null) {
                $templateId = self::DEFAULT_CONFIRMATION_TEMPLATE;
            } elseif (intval($templateId) === AW_Eventbooking_Model_Source_Email_Template::PLEASE_CHOOSE_CODE) {
                return $this;
            }

            $storeId = $orderItem->getStoreId();
            $sender = Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $storeId);
            $order = $orderItem->getOrder();
            $eventStartDate = Mage::helper('core')->formatDate(
                $event->getData('event_start_date'), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true
            );
            $eventEndDate = $event->getData('event_end_date')
                ? Mage::helper('core')->formatDate($event->getData('event_end_date'), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true)
                : null;
            /** @var AW_Eventbooking_Helper_Order $orderHelper */
            $orderHelper = Mage::helper('aw_eventbooking/order');
            $customerName = $orderHelper->getCustomerNameFromOrder($order);
            $customerFirstName = $orderHelper->getCustomerFirstNameFromOrder($order);
            list($totalTickets, $ticketTypes) = $this->_getTicketsCountAndTypes($eventTicketCollection, $orderItem);

            /** @var AW_Rma_Model_Email_Template $emailTemplate */
            $emailTemplate = Mage::getModel('aw_eventbooking/email_template');
            if ($event->getData('generate_pdf_tickets')) {
                $attachment = $this->_getPDFAttachment($orderItem);
                $emailTemplate->addAttachment(self::PDF_NAME, $attachment);
                unset($attachment);
            }

            $orderItemData = new Varien_Object();
            $orderItemData->addData(
                array(
                    'qty_ordered' => $totalTickets,
                    'name' => $orderItem->getName()
                )
            );

            $eventTicket->setTitle(implode(', ', $ticketTypes));
            $emailTemplate
                ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
                ->sendTransactional(
                    $templateId,
                    $sender,
                    $order->getCustomerEmail(),
                    $customerName,
                    array(
                        'order' => $order,
                        'order_item' => $orderItemData,
                        'event_ticket' => $eventTicket,
                        'event' => $event,
                        'event_date' => $eventStartDate, // Backward compatibility
                        'event_start_date' => $eventStartDate,
                        'event_end_date' => $eventEndDate,
                        'customer_name' => $customerFirstName,
                        'total_tickets' => $totalTickets,
                        'ticket_types' => implode(', ', $ticketTypes)
                    )
                );
        }
        return $this;
    }

    /**
     * @param AW_Eventbooking_Model_Event $event
     * @return AW_Eventbooking_Helper_Mailer
     */
    public function sendReminderEmailForEvent(AW_Eventbooking_Model_Event $event)
    {
        $ticketCollection = $event->getTicketCollection();
        /**@var AW_Eventbooking_Model_Resource_Order_History_Collection $orderItemCollection */
        $orderItemCollection = Mage::getModel('aw_eventbooking/order_history')->getCollection()
            ->addFilterOnEventTicketCollection($ticketCollection)
            ->getRelatedOrderItemCollection()
            ->addFieldToFilter('qty_invoiced', array('gt' => new Zend_Db_Expr("qty_refunded")));

        $emailDataList = array();
        foreach ($orderItemCollection as $orderItem) {
            $order = $orderItem->getOrder();
            if (is_null($order)) {
                continue;
            }
            $eventModel = Mage::getModel('aw_eventbooking/event')
                ->setStoreId($orderItem->getStoreId())
                ->load($event->getId());
            $eventTicketCollection = Mage::helper('aw_eventbooking/order')->getEventTicketFromOrderItem($orderItem);
            $eventTicket = $eventTicketCollection->getFirstItem();
            $eventStartDate = Mage::helper('core')->formatDate(
                $event->getData('event_start_date'), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true
            );
            $eventEndDate = $event->getData('event_end_date')
                ? Mage::helper('core')->formatDate($event->getData('event_end_date'), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true)
                : null;
            /** @var AW_Eventbooking_Helper_Order $orderHelper */
            $orderHelper = Mage::helper('aw_eventbooking/order');
            $customerName = $orderHelper->getCustomerNameFromOrder($order);
            $customerFirstName = $orderHelper->getCustomerFirstNameFromOrder($order);

            $templateId = $eventModel->getReminderTemplateId();

            if (intval($templateId) === AW_Eventbooking_Model_Source_Email_Template::PLEASE_CHOOSE_CODE) {
                continue;
            }
            $emailDataList[$orderItem->getId()] = array(
                'store_id' => $orderItem->getStoreId(),
                'customer_name' => $customerName,
                'customer_email' => $order->getCustomerEmail(),
                'sender' => 'general',
                'template_id' => $templateId,
                'vars' => array(
                    'order' => $order,
                    'order_item' => $orderItem,
                    'event_ticket' => $eventTicket,
                    'event' => $eventModel,
                    'event_date' => $eventStartDate, // Backward compatibility
                    'event_start_date' => $eventStartDate,
                    'event_end_date' => $eventEndDate,
                    'customer_name' => $customerFirstName,
                )
            );
        }

        //send emails
        foreach ($emailDataList as $emailData) {
            $emailTemplateModel = Mage::getModel('core/email_template')
                ->setDesignConfig(array('area' => 'frontend', 'store' => $emailData['store_id']));
            try {
                $emailTemplateModel
                    ->sendTransactional(
                        $emailData['template_id'],
                        $emailData['sender'],
                        $emailData['customer_email'],
                        $emailData['customer_name'],
                        $emailData['vars']
                    );
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @throws Exception
     * @return AW_Eventbooking_Helper_Mailer
     */
    public function sendReminderEmailForOrderItem(Mage_Sales_Model_Order_Item $orderItem)
    {
        $eventTicketCollection = Mage::helper('aw_eventbooking/order')->getEventTicketFromOrderItem($orderItem);
        $eventTicket = $eventTicketCollection->getFirstItem();
        if ($eventTicket->getId()) {
            $event = $eventTicket->getEventModel();
            $templateId = $event->getReminderTemplateId();
            if (intval($templateId) === AW_Eventbooking_Model_Source_Email_Template::PLEASE_CHOOSE_CODE) {
                return $this;
            }

            $storeId = $orderItem->getStoreId();
            $sender = 'general';
            $order = $orderItem->getOrder();
            $eventStartDate = Mage::helper('core')->formatDate(
                $event->getData('event_start_date'), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true
            );
            $eventEndDate = $event->getData('event_end_date')
                ? Mage::helper('core')->formatDate($event->getData('event_end_date'), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true)
                : null;
            /** @var AW_Eventbooking_Helper_Order $orderHelper */
            $orderHelper = Mage::helper('aw_eventbooking/order');
            $customerName = $orderHelper->getCustomerNameFromOrder($order);
            $customerFirstName = $orderHelper->getCustomerFirstNameFromOrder($order);

            Mage::getModel('core/email_template')
                ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
                ->sendTransactional(
                    $templateId,
                    $sender,
                    $order->getCustomerEmail(),
                    $customerName,
                    array(
                        'order' => $order,
                        'order_item' => $orderItem,
                        'event_ticket' => $eventTicket,
                        'event' => $event,
                        'event_date' => $eventStartDate, // Backward compatibility
                        'event_start_date' => $eventStartDate,
                        'event_end_date' => $eventEndDate,
                        'customer_name' => $customerFirstName,
                    )
                );
        }
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @throws Exception
     * @return AW_Eventbooking_Helper_Mailer
     */
    public function sendReminderEmailForHolderByOrderItem($orderItem)
    {
        $ticketsCollection = Mage::getModel('aw_eventbooking/ticket')->getCollection()
            ->addFieldToFilter('order_item_id', array('eq' => $orderItem->getId()))
            ->addPaymentStatusFilter();
        $this->_sendReminderEmailForHolderByTickets($ticketsCollection);

        return $this;
    }

    /**
     * @param AW_Eventbooking_Model_Event $event
     * @return $this
     */
    public function sendReminderEmailForHolderByEvent(AW_Eventbooking_Model_Event $event)
    {
        $eventTicketsCollection = $event->getTicketCollection();
        $orderItemCollection = Mage::getModel('aw_eventbooking/order_history')->getCollection()
            ->addFilterOnEventTicketCollection($eventTicketsCollection)
            ->addOrderItemsToCollection()
            ->addFieldToFilter('qty_invoiced', array('gt' => new Zend_Db_Expr("qty_refunded")));
        $oderItemIds = $orderItemCollection->getColumnValues('order_item_id');

        $ticketsCollection = Mage::getModel('aw_eventbooking/ticket')->getCollection()
            ->addFieldToFilter('order_item_id', array('in' => $oderItemIds))
            ->addPaymentStatusFilter();

        $this->_sendReminderEmailForHolderByTickets($ticketsCollection, $event);
        return $this;
    }

    /**
     * @param mixed $ticketsCollection
     * @param AW_Eventbooking_Model_Event|null $event
     * @return $this
     */
    protected function _sendReminderEmailForHolderByTickets($ticketsCollection, $event = null)
    {
        foreach ($ticketsCollection as $ticket) {
            if (!$event) {
                $event = $ticket->getEvent();
            }
            if (!$event->getPersonalizationEnabled()) {
                continue;
            }
            $templateId = $event->getReminderTemplateId();
            if (intval($templateId) === AW_Eventbooking_Model_Source_Email_Template::PLEASE_CHOOSE_CODE) {
                return $this;
            }

            if ($ticket->getHolderEmail() && $ticket->getHolderName()) {
                $orderItem = Mage::getModel('sales/order_item')->load($ticket->getOrderItemId());
                Mage::getModel('core/email_template')
                    ->setDesignConfig(array('area' => 'frontend', 'store' => $ticket->getOrderItemid()))
                    ->sendTransactional(
                        $templateId,
                        'general',
                        $ticket->getHolderEmail(),
                        $ticket->getHolderName(),
                        array(
                            'event' => $event,
                            'order_item' => $orderItem,
                            'event_date' => $event->getEventStartDate(),
                            'event_start_date' => $event->getEventStartDate(),
                            'event_end_date' => $event->getEventEndDate(),
                            'customer_name' => $ticket->getHolderName(),
                        )
                    );
            }
        }
        return $this;
    }

    /**
     * @param mixed $collection
     * @param Mage_Sales_Model_Order_Item $orderItem
     * @return array
     */
    protected function _getTicketsCountAndTypes($collection, $orderItem)
    {
        $totalTickets = 0;
        $ticketTypes = array();

        foreach ($collection as $item) {
            if ($item->getId()) {
                if (!in_array($item->getAttributeByCode('title')->getValue(), $ticketTypes)) {
                    $ticketTypes[] = $item->getAttributeByCode('title')->getValue();
                }
                $totalTickets += $item->getRequestedQtyForOrderItem($orderItem);
            }
        }
        return [$totalTickets, $ticketTypes];
    }
}
