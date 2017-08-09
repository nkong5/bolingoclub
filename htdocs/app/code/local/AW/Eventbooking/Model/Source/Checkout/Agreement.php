<?php
class AW_Eventbooking_Model_Source_Checkout_Agreement
{
    const PLEASE_CHOOSE_CODE  = 0;
    const PLEASE_CHOOSE_LABEL = '--Please Choose--';

    public function toOptionArray($isAddEmptyOption = false)
    {
        $optionArray = array();
        if ($isAddEmptyOption) {
            $optionArray[] = array(
                'value' => self::PLEASE_CHOOSE_CODE,
                'label' => Mage::helper('aw_eventbooking')->__(self::PLEASE_CHOOSE_LABEL),
            );
        }
        foreach (Mage::getResourceModel('checkout/agreement_collection') as $key => $item) {
            $optionArray[] = array(
                'value' => $key,
                'label' => $item->getName(),
            );
        }
        return $optionArray;
    }

    public function toArray($isAddEmptyOption = false)
    {
        $array = array();
        if ($isAddEmptyOption) {
            $array[self::PLEASE_CHOOSE_CODE] = Mage::helper('aw_eventbooking')->__(self::PLEASE_CHOOSE_LABEL);
        }
        foreach (Mage::getResourceModel('checkout/agreement_collection') as $key => $item) {
            $array[$key] = $item->getName();
        }
        return $array;
    }
}