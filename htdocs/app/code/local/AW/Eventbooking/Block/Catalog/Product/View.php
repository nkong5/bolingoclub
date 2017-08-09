<?php

class AW_Eventbooking_Block_Catalog_Product_View extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('aw_eventbooking/catalog/product/view.phtml');
        $this->setTicketSectionTitle();
    }

    public function getProduct()
    {
        return Mage::registry('product');
    }

    public function getEvent()
    {
        return Mage::getModel('aw_eventbooking/event')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->loadByProductId($this->getProduct()->getId());
    }

    public function isEventBookingEnabledForProduct()
    {
        $event = $this->getEvent();
        return !is_null($event->getId()) && $event->getIsEnabled();
    }

    public function getTickets()
    {
        if (($event = $this->getEvent()) && $event->getId()) {
            return $event->getTicketCollection();
        }
        return null;
    }

    public function getTicketTitles()
    {
        $result = array();
        if (!$tickets = $this->getTickets()) {
            return $result;
        }
        foreach ($tickets as $item) {
            $result[AW_Eventbooking_Model_Event::PRODUCT_OPTION_ID . '_' . $item->getId()] = $item->getTitle();
        }
        return $result;
    }

    public function isPersonalizationEnabled()
    {
        if ($this->getEvent()->getPersonalizationEnabled()) {
            return true;
        }
        return false;
    }

    public function isDisplayEmailEnabled()
    {
        if ($this->getEvent()->getPersonalizationDisplayEmail()) {
            return true;
        }
        return false;
    }

    public function setTicketSectionTitle()
    {
        if (!Mage::registry('ticket_section_title')) {
            $sectionTitle = $this->getEvent()->getTicketSectionTitle();
            Mage::register('ticket_section_title', $sectionTitle, true);
        }
    }
}