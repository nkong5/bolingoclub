<?php

class AW_Eventbooking_Model_Source_Status extends AW_Eventbooking_Model_Source_Abstract
{
    const ENABLED = 1;
    const DISABLED = 0;

    const ENABLED_LABEL = 'Enabled';
    const DISABLED_LABEL = 'Disabled';

    protected function _toOptionArray()
    {
        $_helper = $this->_getHelper();
        return array(
            array('value' => self::ENABLED, 'label' => $_helper->__(self::ENABLED_LABEL)),
            array('value' => self::DISABLED, 'label' => $_helper->__(self::DISABLED_LABEL))
        );
    }
}
