<?php

class AW_Eventbooking_Block_Ticket_List extends Mage_Core_Block_Template
{
    protected $_collection = null;

    public function getCollection()
    {
        if (!$this->_collection) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();

            /** @var AW_Eventbooking_Model_Resource_Ticket_Collection $collection */
            $collection = Mage::getModel('aw_eventbooking/ticket')->getCollection();
            $collection
                ->joinEventData()
                ->joinOrderData();
            $collection->addFieldToFilter('customer_id', $customer->getId());
            $this->_collection = $collection;
        }
        return $this->_collection;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'aw_eventbooking.ticket.view.pager')
            ->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        return $this;
    }


    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getProductUrl($id)
    {
        /** @var Mage_Catalog_Model_Product $productModel */
        $productModel = Mage::getModel('catalog/product')->load($id);
        return $productModel->getProductUrl();
    }

    public function getViewUrl($id)
    {
        return Mage::getUrl('aw_eventbooking/ticket/view', array('_secure'=>true,'id' => $id));
    }

    protected function _processDateTime($dateTime)
    {
        // Convert date from UTC to Store Locale
        return Mage::app()->getLocale()
            ->date($dateTime, Varien_Date::DATETIME_INTERNAL_FORMAT);
    }

    public function isToday($item)
    {
        $startDate = $this->_processDateTime($item->getData('event_start_date'));
        $now = Mage::app()->getLocale()->storeDate(null, null, true);
        if ($itemEndDate = $item->getData('event_end_date')) {
            $endDate = $this->_processDateTime($itemEndDate);
            if ($now->isLater($startDate) && $now->isEarlier($endDate)) {
                /* Event happening right now */
                return true;
            }
        }

        $startDate->setTime(0);
        $now->setTime(0);
        return $now->equals($startDate);
    }

    protected function _getFormattedTicketPrice(AW_Eventbooking_Model_Ticket $ticket)
    {
        $orderItem = $ticket->getOrderItem();
        $price = $orderItem->getRowTotal() / $orderItem->getQtyOrdered();
        return $ticket->getStore()->formatPrice($price);
    }
}
