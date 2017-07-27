<?php

class NRApps_Idealo_Model_Source_ProductTypes
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'all',
                'label' => Mage::helper('nrapps_idealo')->__('All')
            ),
            array(
                'value' => 'simple',
                'label' => Mage::helper('catalog')->__('Simple Product')
            ),
            array(
                'value' => 'virtual',
                'label' => Mage::helper('catalog')->__('Virtual Product')
            ),
            array(
                'value' => 'downloadable',
                'label' => Mage::helper('catalog')->__('Downloadable Product')
            ),
            array(
                'value' => 'grouped',
                'label' => Mage::helper('catalog')->__('Grouped Product')
            ),
            array(
                'value' => 'configurable',
                'label' => Mage::helper('catalog')->__('Configurable Product')
            ),
            array(
                'value' => 'bundle',
                'label' => Mage::helper('catalog')->__('Bundle Product')
            ),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = array();
        foreach($this->toOptionArray() as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }
}
