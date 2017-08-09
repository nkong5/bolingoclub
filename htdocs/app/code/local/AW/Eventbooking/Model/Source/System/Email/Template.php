<?php

class AW_Eventbooking_Model_Source_System_Email_Template extends AW_Eventbooking_Model_Source_Abstract
{
    /**
     * @return Mage_Core_Model_Resource_Email_Template_Collection
     */
    protected function _getCollection()
    {
        if (!$collection = Mage::registry('config_system_email_template')) {
            $collection = Mage::getResourceModel('core/email_template_collection')->load();
            Mage::register('config_system_email_template', $collection);
        }
        return $collection;
    }

    protected function _toOptionArray()
    {
        /**
         * The same as Mage_Adminhtml_Model_System_Config_Source_Email_Template
         * but not including the 'Default template from Locale' item
         */
        return $this->_getCollection()->toOptionArray();
    }
}
