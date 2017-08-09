<?php

class AW_Eventbooking_Model_Resource_Order_History extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('aw_eventbooking/order_history', 'entity_id');
    }
}