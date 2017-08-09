<?php

class AW_Eventbooking_Model_Event extends Mage_Core_Model_Abstract
{
    const PRODUCT_OPTION_ID = 2147483647;

    protected $_storeId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    protected $_product = null;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('aw_eventbooking/event');
    }

    /**
     * Return ticket collection with attribute values
     * assigned to current event
     *
     * @return AW_Eventbooking_Model_Resource_Event_Ticket_Collection
     */
    public function getTicketCollection()
    {
        return Mage::getResourceModel('aw_eventbooking/event_ticket_collection')
            ->addFilterOnEventId($this->getId())
            ->addTicketAttributes($this->getStoreId());
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProductModel()
    {
        if (is_null($this->_product)) {
            $product = Mage::getModel('catalog/product')
                ->load($this->getData('product_id'));
            if ($product->getId()) {
                $this->_product = $product;
            }
        }
        return $this->_product;
    }

    /**
     * @return AW_Eventbooking_Model_Event
     */
    public function validateOnAddToCart()
    {
        $this->_validate();
        return $this;
    }

    /**
     * @return AW_Eventbooking_Model_Event
     */
    public function validateOnPlaceOrder()
    {
        $this->_validate();
        return $this;
    }

    /**
     * Return attribute collection for event by store id
     * if for not default store not exists attribute, default value will be returned
     *
     * @return AW_Eventbooking_Model_Resource_Event_Attribute_Collection
     */
    public function getAttributeCollection()
    {
        return Mage::getResourceModel('aw_eventbooking/event_attribute_collection')
            ->getAttributeCollectionByStoreId($this->getId(), $this->getStoreId());
    }

    /**
     * @param string $attributeCode
     * @return AW_Eventbooking_Model_Event_Attribute
     */
    public function getAttributeByCode($attributeCode)
    {
        return Mage::getModel('aw_eventbooking/event_attribute')
            ->getEventAttributeByCode($this->getId(), $attributeCode, $this->getStoreId());
    }

    /**
     * @param integer $productId
     *
     * @return AW_Eventbooking_Model_Event
     */
    public function loadByProductId($productId)
    {
        $this->load($productId, 'product_id');
        return $this;
    }

    /**
     * Set store id to event
     *
     * @param int $storeId
     * @return AW_Eventbooking_Model_Event
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Return store id from event
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * @throws Exception
     *
     * @return AW_Eventbooking_Model_Event
     */
    protected function _validate()
    {
        if (!$this->getIsEnabled()) {
            throw new Exception(
                Mage::helper('aw_eventbooking')->__('Event is disabled')
            );
        }
        $now = new Zend_Date();
        $storeId = Mage::app()->getStore()->getId();
        $eventStartDate = new Zend_Date($this->getData('event_start_date'), Varien_Date::DATETIME_INTERNAL_FORMAT);
        $eventEndDate = $this->getData('event_end_date');
        if ($eventEndDate) {
            $eventEndDate = new Zend_Date($eventEndDate, Varien_Date::DATETIME_INTERNAL_FORMAT);
        }

        if (Mage::helper('aw_eventbooking/config')->getAllowBuyTicketsIfEventStarted($storeId)) {
            if ($eventEndDate && $eventEndDate->compare($now) === -1) {
                throw new Exception(
                    Mage::helper('aw_eventbooking')->__("Event %s already ended", $this->getName())
                );
            }
        } else {
            if ($eventStartDate->compare($now) === -1) {
                throw new Exception(
                    Mage::helper('aw_eventbooking')->__("Event %s already started", $this->getName())
                );
            }
        }
        return $this;
    }

    public function getTitle()
    {
        return $this->getData('ticket_section_title');
    }

    public function isCurrentAdminCanRedeem()
    {
        $redeemRoles = $this->getData('redeem_roles');
        if (!$redeemRoles) {
            /* Redeem allowed to all on a new event */
            return true;
        }
        $adminRoles = Mage::helper('aw_eventbooking')->getCurrentAdminRoleIds();
        return !!array_intersect($adminRoles, $this->getData('redeem_roles'));
    }

    public function getName()
    {
        return $this->getProductModel()
            ? $this->getProductModel()->getName()
            : null;
    }
}
