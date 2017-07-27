<?php

class NRApps_Idealo_Model_Backend_Serialized_Array extends Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array
{
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            foreach($value as $key => $row) {
                if (isset($row['sort_order']) && !strlen(trim($row['sort_order']))) {
                    $value[$key]['sort_order'] = 0;
                }
            }
            $this->setValue($value);
        }
        parent::_beforeSave();
    }
}