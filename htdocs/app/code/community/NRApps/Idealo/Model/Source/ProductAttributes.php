<?php

class NRApps_Idealo_Model_Source_ProductAttributes
{

    /**
     * @return array
     */
    public function getOptionArray()
    {
        $options = array();
        /** @var Mage_Eav_Model_Entity_Attribute_Abstract[] $attributes */
        $attributes = Mage::getResourceModel('catalog/product')
            ->loadAllAttributes()
            ->getAttributesByCode();
        foreach($attributes as $attribute) {
            $options[$attribute->getAttributeCode()] = $attribute->getFrontend()->getLabel() . ' [' . $attribute->getAttributeCode() . ']';
        }
        asort($options);

        return $options;
    }

    /**
     * @param string $option
     * @return string
     */
    public function getOptionLabel($option)
    {
        foreach($this->getOptionArray() as $optionCode => $optionLabel) {
            if ($option == $optionCode) {
                return $optionLabel;
            }
        }
        return '';
    }
}
