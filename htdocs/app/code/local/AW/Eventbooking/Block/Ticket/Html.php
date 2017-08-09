<?php

class AW_Eventbooking_Block_Ticket_Html extends Mage_Core_Block_Template
{
    protected $_ticket;

    protected function _beforeToHtml()
    {
        if (!$this->getTemplate()) {
            $this->setTemplate('aw_eventbooking/ticket/html.phtml');
        }
        return parent::_beforeToHtml();
    }

    public function getArea()
    {
        return 'frontend';
    }

    public function setTicket(AW_Eventbooking_Model_Ticket $ticket)
    {
        $this->_ticket = $ticket;
        return $this;
    }

    /**
     * @return AW_Eventbooking_Model_Ticket
     */
    public function getTicket()
    {
        return $this->_ticket;
    }

    public function getStore()
    {
        return $this->getTicket()->getStore();
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->getTicket()->getStoreId();
    }

    public function getLogoSrc()
    {
        $logoFile = Mage::getStoreConfig('design/header/logo_src', $this->getStoreId());
        return $this->getSkinUrl($logoFile);
    }

    public function getStoreName()
    {
        return Mage::getStoreConfig('general/store_information/name', $this->getStoreId());
    }

    public function getEventName()
    {
        return $this->getEvent()
            ? $this->getEvent()->getName()
            : null;
    }

    /**
     * @return AW_Eventbooking_Model_Event|null
     */
    public function getEvent()
    {
        return $this->getTicket()->getEvent();
    }

    protected function _getStoreDate($date)
    {
        /** @var Zend_Date $zDate */
        $zDate = Mage::app()->getLocale()->storeDate($this->getStore(), $date, true);
        $zDate->setTimestamp(strtotime($date));
        return $zDate;
    }

    public function getEventStartDate()
    {
        return $this->_getStoreDate($this->getEvent()->getData('event_start_date'));
    }

    public function getEventEndDate()
    {
        $eventEndDate = $this->getEvent()->getData('event_end_date');
        return $eventEndDate
            ? $this->_getStoreDate($eventEndDate)
            : null;
    }

    public function getQRImageSrc()
    {
        $ticket = $this->getTicket();
        $externalTicketUrl = $this->getStore()->getUrl('aw_eventbooking_external/ext/view', array(
            '_secure' => true,
            'code' => $ticket->getData('code'),
            'hash' => $ticket->getControlHash(),
        ));
        $qrWidth = Mage::helper('aw_eventbooking/config')->getQRWidth($this->getStore());
        return sprintf('https://api.qrserver.com/v1/create-qr-code/?%s',
            http_build_query(array(
                'data' => $externalTicketUrl,
                'size' => sprintf('%dx%d', $qrWidth, $qrWidth),
            ))
        );
    }

    public function getTicketCode()
    {
        return $this->getTicket()->getData('code');
    }

    public function getEventTicket()
    {
        return $this->getTicket()->getEventTicket();
    }

    public function getAdmissionType()
    {
        return $this->getTicket()->getEventTicketTitle();
    }

    public function getAttendeeName()
    {
        $attendeeName = $this->getTicket()->getCustomerName();
        if ($this->getTicket()->getHolderName()) {
            $attendeeName = $this->getTicket()->getHolderName();
        }

        return $attendeeName;
    }

    public function getLocation()
    {
        return $this->getEvent()->getData('location');
    }

    public function getOrderIncrementId()
    {
        return $this->getTicket()->getOrderIncrementId();
    }

    public function getOrderDate()
    {
        $orderDate = $this->_getStoreDate($this->getTicket()->getOrderDate());
        return $orderDate->toString(Mage::app()->getLocale()->getDateFormat('short'));
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->getTicket()->getProduct();
    }

    public function getProductImage()
    {
        /** @var Mage_Catalog_Helper_Image $imageHelper */
        $imageHelper = Mage::helper('catalog/image');
        $product = $this->getProduct();
        return $product
            ? $imageHelper->init($product, 'image')->resize(200)
            : null;
    }

    public function getAgreementText()
    {
        $agreement = $this->getTicket()->getAgreement();
        return $agreement
            ? $agreement->getData('content')
            : null;
    }

    public function getProductOptions()
    {
        $orderItem = $this->getTicket()->getOrderItem();
        /** @var AW_Eventbooking_Helper_Ticket $ticketHelper */
        $ticketHelper = Mage::helper('aw_eventbooking/ticket');
        return $ticketHelper->getOrderItemOptions($orderItem);
    }
}
