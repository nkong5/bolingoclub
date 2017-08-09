<?php

class AW_Eventbooking_Model_Source_Ticket_Paymentstatus extends AW_Eventbooking_Model_Source_Abstract
{
    const UNPAID_LABEL = 'Unpaid';
    const PAID_LABEL = 'Paid';
    const REFUNDED_LABEL = 'Cancelled';

    protected function _toOptionArray()
    {
        $_helper = $this->_getHelper();
        return array(
            array('value' => AW_Eventbooking_Model_Ticket::PAYMENT_STATUS_UNPAID, 'label' => $_helper->__(self::UNPAID_LABEL)),
            array('value' => AW_Eventbooking_Model_Ticket::PAYMENT_STATUS_PAID, 'label' => $_helper->__(self::PAID_LABEL)),
            array('value' => AW_Eventbooking_Model_Ticket::PAYMENT_STATUS_REFUNDED, 'label' => $_helper->__(self::REFUNDED_LABEL)),
        );
    }
}
