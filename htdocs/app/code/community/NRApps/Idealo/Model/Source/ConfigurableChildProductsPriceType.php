<?php

class NRApps_Idealo_Model_Source_ConfigurableChildProductsPriceType extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    const PRICE_TYPE_PARENT_WITH_MODIFICATIONS = 'parent';
    const PRICE_TYPE_PARENT_WITHOUT_MODIFICATIONS = 'parent_without';
    const PRICE_TYPE_CHILD = 'child';

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
                    'value' => self::PRICE_TYPE_PARENT_WITH_MODIFICATIONS,
                    'label' => Mage::helper('nrapps_idealo')->__('Price of parent product with modifications (Magento Default)'),
                ),
                array(
                    'value' => self::PRICE_TYPE_PARENT_WITHOUT_MODIFICATIONS,
                    'label' => Mage::helper('nrapps_idealo')->__('Price of parent product without modifications'),
                ),
                array(
                    'value' => self::PRICE_TYPE_CHILD,
                    'label' => Mage::helper('nrapps_idealo')->__('Price of child product (i.e. with BCP, SCP)'),
                ),
            );
        }
        return $this->_options;
    }
}
