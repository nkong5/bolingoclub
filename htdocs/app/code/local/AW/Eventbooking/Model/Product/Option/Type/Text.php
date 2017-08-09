<?php

class AW_Eventbooking_Model_Product_Option_Type_Text extends Mage_Catalog_Model_Product_Option_Type_Text
{
    /**
     * Return Price for selected option
     *
     * @param string $optionValue Prepared for cart option value
     * @param float $basePrice For percent price type
     * @return float
     */
    public function getOptionPrice($optionValue, $basePrice)
    {
        $option = $this->getOption();
        $isEventBookingOption = strpos($option->getOptionId(), (string)AW_Eventbooking_Model_Event::PRODUCT_OPTION_ID);
        if ($isEventBookingOption === false) {
            return $this->_getChargableOptionPrice(
                $option->getPrice(),
                $option->getPriceType() == 'percent',
                $basePrice
            );
        } else {
            return $this->_getChargableTicketOptionPrice(
                $option->getPrice(),
                intval($optionValue)
            );
        }
    }

    /**
     * Return final chargable price for option
     *
     * @param float $price Price of option
     * @param int $optionValue
     * @return float
     */
    protected function _getChargableTicketOptionPrice($price, $optionValue)
    {
        return $price * $optionValue;
    }
}
