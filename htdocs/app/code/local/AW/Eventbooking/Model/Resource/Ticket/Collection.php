<?php

class AW_Eventbooking_Model_Resource_Ticket_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_isEventDataJoined = false;
    protected $_isOrderDataJoined = false;

    public function _construct()
    {
        parent::_construct();
        $this->_init('aw_eventbooking/ticket');
    }

    public function joinEventData()
    {
        if ($this->_isEventDataJoined) {
            return $this;
        }

        $this
            ->join( // Event ID
                array('event_ticket' => 'aw_eventbooking/event_ticket'),
                'event_ticket_id=event_ticket.entity_id',
                array('event_id', 'price')
            )
            ->join( // Event Name
                array('event_attr_title' => 'aw_eventbooking/event_attribute'),
                "(event_ticket.event_id=event_attr_title.event_id)
                    AND (event_attr_title.attribute_code = 'ticket_section_title')
                    AND (event_attr_title.store_id = 0)",
                array('event_title' => 'value')
            )
            ->join( // Ticket Title
                array('event_ticket_attr_title' => 'aw_eventbooking/event_ticket_attribute'),
                "(event_ticket_id=event_ticket_attr_title.ticket_id)
                    AND (event_ticket_attr_title.attribute_code = 'title')
                    AND (event_ticket_attr_title.store_id = 0)",
                array('ticket_title' => 'value')
            )
            ->join( // Event
                array('event' => 'aw_eventbooking/event'),
                'event_ticket.event_id=event.entity_id'
            );

        $this->_isEventDataJoined = true;
        return $this;
    }

    /**
     * This method taken from Magento CE 1.8.1.0 to make joinEventData
     * method compatible with Magento CE 1.4.1.1
     */
    public function join($table, $cond, $cols = '*')
    {
        if (is_array($table)) {
            foreach ($table as $k => $v) {
                $alias = $k;
                $table = $v;
                break;
            }
        } else {
            $alias = $table;
        }

        if (!isset($this->_joinedTables[$table])) {
            $this->getSelect()->join(
                array($alias => $this->getTable($table)),
                $cond,
                $cols
            );
            $this->_joinedTables[$alias] = true;
        }
        return $this;
    }

    public function joinOrderData()
    {
        if ($this->_isOrderDataJoined) {
            return $this;
        }

        $this
            ->join( // Product ID, Order ID
                array('order_item' => 'sales/order_item'),
                'order_item_id=order_item.item_id',
                array('product_id', 'order_id')
            );
        $this
            ->join( // Customer ID
                array('order' => 'sales/order'),
                'order_item.order_id=order.entity_id',
                array('customer_id', 'increment_id')
            );
        $this->_isOrderDataJoined = true;
        return $this;
    }

    public function addPaymentStatusFilter($paymentStatus = AW_Eventbooking_Model_Ticket::PAYMENT_STATUS_PAID)
    {
        $this->addFieldToFilter('payment_status', array('eq' => $paymentStatus));
        return $this;
    }

    public function addAdminRoleFilter($roleId = null)
    {
        if (is_null($roleId)) {
            $roleId = Mage::helper('aw_eventbooking')->getCurrentAdminRoleIds();
            if ($roleId) {
                $roleId = array_pop($roleId);
            }
        }
        if ($roleId) {
            $this->addFieldToFilter('event.redeem_roles', array('finset' => $roleId));
        }
        return $this;
    }
}
