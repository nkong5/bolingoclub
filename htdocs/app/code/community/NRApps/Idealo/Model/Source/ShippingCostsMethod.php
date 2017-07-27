<?php

class NRApps_Idealo_Model_Source_ShippingCostsMethod extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    const SHIPPING_COSTS_BY_PRICE = 'price';
    const SHIPPING_COSTS_BY_WEIGHT = 'weight';

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
                    'value' => self::SHIPPING_COSTS_BY_PRICE,
                    'label' => Mage::helper('nrapps_idealo')->__('Shipping Cost based on Price'),
                ),
                array(
                    'value' => self::SHIPPING_COSTS_BY_WEIGHT,
                    'label' => Mage::helper('nrapps_idealo')->__('Shipping Cost based on Weight'),
                ),
            );
        }
        return $this->_options;
    }
}
