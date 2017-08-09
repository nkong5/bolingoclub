<?php

class AW_Eventbooking_Model_Source_Yesno extends AW_Eventbooking_Model_Source_Abstract
{
    const CODE_YES = 1;
    const CODE_NO = 0;

    const LABEL_YES = 'Yes';
    const LABEL_NO = 'No';

    protected function _toOptionArray()
    {
        return array(
            array('value' => self::CODE_YES, 'label' => Mage::helper('adminhtml')->__(self::LABEL_YES)),
            array('value' => self::CODE_NO, 'label' => Mage::helper('adminhtml')->__(self::LABEL_NO)),
        );
    }

    public function toArray()
    {
        return $this->toShortOptionArray();
    }
}
