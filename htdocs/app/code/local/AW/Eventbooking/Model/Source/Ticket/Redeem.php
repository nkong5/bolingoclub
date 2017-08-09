<?php

class AW_Eventbooking_Model_Source_Ticket_Redeem extends AW_Eventbooking_Model_Source_Abstract
{
    const REDEEMED_LABEL = 'Redeemed';
    const NOT_REDEEMED_LABEL = 'Not Redeemed';

    protected function _toOptionArray()
    {
        $_helper = $this->_getHelper();
        return array(
            array('value' => AW_Eventbooking_Model_Ticket::REDEEMED, 'label' => $_helper->__(self::REDEEMED_LABEL)),
            array('value' => AW_Eventbooking_Model_Ticket::NOT_REDEEMED, 'label' => $_helper->__(self::NOT_REDEEMED_LABEL)),
        );
    }
}
