<?php

class NRApps_Idealo_Model_Source_ConfigurableTypes
{
    const CONFIGURABLE_TYPE_EXPORT_CHILDREN     = 'export_children';
    const CONFIGURABLE_TYPE_EXPORT_PARENT       = 'export_parent';
    const CONFIGURABLE_TYPE_NO_EXPORT           = 'no_export';

    /**
     * Fetch options array
     *
     * @return array
     */
    public function getOptionArray()
    {
        return array(
            self::CONFIGURABLE_TYPE_EXPORT_CHILDREN => Mage::helper('nrapps_idealo')->__('Export Children Products'),
            self::CONFIGURABLE_TYPE_EXPORT_PARENT => Mage::helper('nrapps_idealo')->__('Export Parent Product'),
            self::CONFIGURABLE_TYPE_NO_EXPORT => Mage::helper('nrapps_idealo')->__('No Export'),
        );
    }

    public function toOptionArray()
    {
        return $this->getOptionArray();
    }
}
