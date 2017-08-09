<?php

class AW_Eventbooking_Model_Ticket extends Mage_Core_Model_Abstract
{
    const CODE_LENGTH = 8;

    const REDEEMED = 1;
    const NOT_REDEEMED = 0;

    const PAYMENT_STATUS_UNPAID = 0;
    const PAYMENT_STATUS_PAID = 1;
    const PAYMENT_STATUS_REFUNDED = 2;

    protected $_orderItem;
    protected $_eventTicket;
    protected $_product;
    protected $_agreement;

    protected function _construct()
    {
        $this->_init('aw_eventbooking/ticket');
    }

    public function generateCode($prefix = '')
    {
        do {
            /* Generate random code */
            $code = '';
            while (strlen($code) < self::CODE_LENGTH) {
                while (!preg_match("/[A-Z0-9]/", $char = chr(rand(48, 90)))) {
                };
                $code .= $char;
            }
            $code = $prefix . $code;
            /* Uniqueness check */
            $ticketWithSameCode = $this->load($code, 'code');
        } while ($ticketWithSameCode->getId());

        return $code;
    }

    public function createTickets(Mage_Sales_Model_Order_Item $orderItem, AW_Eventbooking_Model_Event_Ticket $eventTicket)
    {
        $options = $orderItem->getProductOptionByCode('options');
        $isPersonalizationEnabled = $eventTicket->getEventModel()->getPersonalizationEnabled();
        $personalizationData = array();

        if ($isPersonalizationEnabled) {
            $productOptions = $orderItem->getProductOptions();
            $personalizationData = $this->_getPersonalizationDataFromOptions($productOptions);
        }

        foreach ($options as $key => $option) {
            if (AW_Eventbooking_Model_Event::PRODUCT_OPTION_ID . '_' . $eventTicket->getId() != $option['option_id']) {
                continue;
            }
            for ($i = 1; $i <= $option['option_value']; $i++) {
                $newTicket = new self;
                $data = array(
                    'event_ticket_id' => $eventTicket->getId(),
                    'order_item_id' => $orderItem->getId(),
                    'code' => $newTicket->generateCode($eventTicket->getData('codeprefix')),
                );
                if ($isPersonalizationEnabled) {
                    if (isset($personalizationData[$eventTicket->getId() . '_' . $i]['name'])) {
                        $data['holder_name'] = $personalizationData[$eventTicket->getId() . '_' . $i]['name'];
                    }
                    if (isset($personalizationData[$eventTicket->getId() . '_' . $i]['email'])) {
                        $data['holder_email'] = $personalizationData[$eventTicket->getId() . '_' . $i]['email'];
                    }
                }
                $newTicket->setData($data);
                $newTicket->save();
            }
        }
        
    }

    protected function _updateTicketsPaymentStatusToPaid($eventTicketId, $orderItemId)
    {
        /** @var AW_Eventbooking_Model_Resource_Ticket_Collection $collection */
        $collection = $this->getCollection();
        $collection
            ->addFieldToFilter('event_ticket_id', array('eq' => $eventTicketId))
            ->addFieldToFilter('order_item_id', array('eq' => $orderItemId))
            ->addFieldToFilter('payment_status', array('eq' => self::PAYMENT_STATUS_UNPAID))
        ;
        foreach ($collection as $item) {
            $item->setData('payment_status', self::PAYMENT_STATUS_PAID);
            $item->save();
        }
        return $collection->getSize();
    }

    public function markTicketsAsPaid(Mage_Sales_Model_Order_Invoice $invoice)
    {
        /** @var AW_Eventbooking_Helper_Order $orderHelper */
        $orderHelper = Mage::helper('aw_eventbooking/order');
        foreach ($invoice->getAllItems() as $item) {
            /** @var Mage_Sales_Model_Order_Invoice_Item $item */
            /** @var Mage_Sales_Model_Order_Item $orderItem */
            $orderItem = $item->getOrderItem();
            $eventTicketCollection = $orderHelper->getEventTicketFromOrderItem($orderItem);
            foreach ($eventTicketCollection as $eventTicket) {
                if ($eventTicket) {
                    $this->_updateTicketsPaymentStatusToPaid($eventTicket->getId(), $orderItem->getId());
                }
            }
        }
        return $this;
    }

    /**
     * @return Mage_Sales_Model_Order_Item
     */
    public function getOrderItem()
    {
        if (is_null($this->_orderItem) && ($orderItemId = $this->getData('order_item_id'))) {
            $orderItem = Mage::getModel('sales/order_item')->load($orderItemId);
            if ($orderItem->getId()) {
                $this->_orderItem = $orderItem;
            }
        }
        return $this->_orderItem;
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($orderItem = $this->getOrderItem()) {
            return $orderItem->getOrder();
        }
    }

    /**
     * @return Mage_Core_Model_Store|null
     */
    public function getStore()
    {
        return $this->getStoreId()
            ? Mage::app()->getSafeStore($this->getStoreId())
            : null;
    }

    public function getStoreId()
    {
        if (!$this->getId()) {
            return;
        }
        $order = $this->getOrder();
        return $order ? $order->getStoreId() : null;
    }

    /**
     * @return AW_Eventbooking_Model_Event_Ticket|null
     */
    public function getEventTicket()
    {
        $eventTicketId = $this->getData('event_ticket_id');
        if ($eventTicketId && is_null($this->_eventTicket)) {
            /** @var AW_Eventbooking_Model_Event_Ticket $eventTicket */
            $eventTicket = Mage::getModel('aw_eventbooking/event_ticket')->load($eventTicketId);
            if ($eventTicket->getId()) {
                $eventTicket->setStoreId($this->getStoreId());
                $this->_eventTicket = $eventTicket;
            }
        }
        return $this->_eventTicket;
    }

    public function getEvent()
    {
        return $this->getEventTicket()
            ? $this->getEventTicket()->getEventModel()
            : null;
    }

    public function getControlHash()
    {
        return md5(sprintf(
            '%d:%d:%s',
            $this->getId(),
            $this->getData('order_item_id'),
            $this->getData('code')
        ));
    }

    public function getCustomerName()
    {
        return $this->getOrder()
            ? $this->getOrder()->getCustomerName()
            : null;
    }

    public function getEventTicketTitle()
    {
        return $this->getEventTicket()
            ? $this->getEventTicket()->getData('title')
            : null;
    }

    public function getOrderIncrementId()
    {
        return $this->getOrder()
            ? $this->getOrder()->getData('increment_id')
            : null;
    }

    public function getOrderDate()
    {
        return $this->getOrder()
            ? $this->getOrder()->getCreatedAt()
            : null;
    }

    public function getProduct()
    {
        if ($this->getOrderItem()) {
            /** @var Mage_Catalog_Model_Product $product */
            $product = Mage::getModel('catalog/product')
                ->setStoreId($this->getStoreId())
                ->load($this->getOrderItem()->getProductId());
            if ($product->getId()) {
                $this->_product = $product;
            }
        }
        return $this->_product;
    }

    public function getEventName()
    {
        return $this->getProduct()
            ? $this->getProduct()->getName()
            : null;
    }

    public function getAgreement()
    {
        $event = $this->getEvent();
        if ($event && $event->getData('is_terms_enabled') && is_null($this->_agreement)) {
            /** @var Mage_Checkout_Model_Agreement $agreement */
            $agreement = Mage::getModel('checkout/agreement')->load($event->getData('terms_id'));
            if ($agreement->getId()) {
                $this->_agreement = $agreement;
            }
        }
        return $this->_agreement;
    }

    public function getStatusLabel()
    {
        $helper = Mage::helper('aw_eventbooking');
        /** @var AW_Eventbooking_Model_Source_Ticket_Paymentstatus $paymentStatusSource */
        $paymentStatusSource = Mage::getModel('aw_eventbooking/source_ticket_paymentstatus');
        $statusLabel = $paymentStatusSource->getOptionLabel($this->getData('payment_status'));
        if ($this->getData('payment_status') == self::PAYMENT_STATUS_PAID) {
            if ($this->getData('redeemed') == self::REDEEMED) {
                $statusLabel = $helper->__('Used');
            } else {
                $statusLabel = $helper->__('Valid');
            }
        }
        return $statusLabel;
    }

    public function isPaid()
    {
        if ($this->getPaymentStatus() == self::PAYMENT_STATUS_PAID) {
            return true;
        }
        return false;
    }

    public function getCustomer()
    {
        if ($customerId = $this->getData('customer_id')) {
            return Mage::getSingleton('customer/customer')->load($customerId);
        }
        return null;
    }

    public function setRedeem()
    {
        $this->setData('redeemed', AW_Eventbooking_Model_Ticket::REDEEMED);
        $this->save();
        return $this;
    }

    public function undoRedeem()
    {
        $this->setData('redeemed', AW_Eventbooking_Model_Ticket::NOT_REDEEMED);
        $this->save();
        return $this;
    }

    private function _getPersonalizationDataFromOptions($productOptions)
    {
        $personalizationData = array();
        if (isset($productOptions['info_buyRequest']['aw_eventbooking_custom_data'])) {
            $customRawData = json_decode($productOptions['info_buyRequest']['aw_eventbooking_custom_data'], true);
            $regExpForDetect = "/" . AW_Eventbooking_Model_Event::PRODUCT_OPTION_ID . "_(\d+)_c(\d+)_(email|name)/";

            foreach ($customRawData as $key => $value) {
                if (preg_match($regExpForDetect, $key, $match) == false) {
                    continue;
                }
                $personalizationData[$match[1] . '_' . $match[2]][$match[3]] = $value;
            }
        }

        return $personalizationData;
    }
}
