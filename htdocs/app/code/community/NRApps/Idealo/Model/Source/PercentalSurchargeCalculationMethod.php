<?php

class NRApps_Idealo_Model_Source_PercentalSurchargeCalculationMethod extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    const CALCULATION_METHOD_PRODUCT_PRICE = 'product';
    const CALCULATION_METHOD_PRODUCT_PRICE_AND_SHIPPING_COST = 'product_shipping';

    public function getOptionArray()
    {
        $options = array();
        foreach($this->getAllOptions() as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    /**
     * Method used for configuration
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach($this->getAllOptions() as $option) {
            if ($option['value']) {
                $options[$option['value']] = $option['label'];
            }
        }
        return $options;
    }

    /**
     * Method used for product attribute
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'value' => self::CALCULATION_METHOD_PRODUCT_PRICE,
                    'label' => Mage::helper('nrapps_idealo')->__('Based on Product Price'),
                ),
                array(
                    'value' => self::CALCULATION_METHOD_PRODUCT_PRICE_AND_SHIPPING_COST,
                    'label' => Mage::helper('nrapps_idealo')->__('Based on Product Price plus Shipping Cost'),
                ),
            );
        }
        return $this->_options;
    }
}
