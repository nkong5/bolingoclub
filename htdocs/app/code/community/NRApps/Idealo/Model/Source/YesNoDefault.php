<?php
class NRApps_Idealo_Model_Source_YesNoDefault extends Mage_Eav_Model_Entity_Attribute_Source_Boolean
{
    /**
     * Retrieve all attribute options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('catalog')->__('Yes'),
                    'value' => 1
                ),
                array(
                    'label' => Mage::helper('catalog')->__('No'),
                    'value' => 0
                ),
                array(
                    'label' => Mage::helper('catalog')->__('Use config'),
                    'value' => ''
                ),
            );
        }
        return $this->_options;
    }
}
