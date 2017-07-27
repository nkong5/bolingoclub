<?php

class NRApps_Idealo_Model_Source_Store extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    public function getOptionArray()
    {
        $options = array();
        foreach($this->getAllOptions() as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }


    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false);
        }
        return $this->_options;
    }

    public function getOptionText($value)
    {
        if(!$value)$value ='0';
        $isMultiple = false;
        if (strpos($value, ',')) {
            $isMultiple = true;
            $value = explode(',', $value);
        }

        if (!$this->_options) {
            $collection = Mage::getResourceModel('core/store_collection');
            $this->_options = $collection->load()->toOptionArray();
        }

        if ($isMultiple) {
            $values = array();
            foreach ($value as $val) {
                $values[] = $this->_options[$val];
            }
            return $values;
        }
        else {
            return $this->_options[$value];
        }
        return false;
    }
}
