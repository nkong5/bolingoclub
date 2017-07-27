<?php
class NRApps_Idealo_Model_DeliveryTime 
{
    protected $_mapping = null;
    
    public function update()
    {
        $deliveryTimeAttributeCode = Mage::getStoreConfig('nrapps_idealo/attributes/delivery_time');
        if (!$deliveryTimeAttributeCode) {
            return;
        }
        
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $deliveryTimeAttribute */
        $deliveryTimeAttribute = Mage::getResourceModel('catalog/product')->getAttribute($deliveryTimeAttributeCode);
        if (!$deliveryTimeAttribute->getId()) {
            return;
        }
        
        if ($deliveryTimeAttribute->getFrontendInput() == 'select') {
            $this->_updateDeliveryTimeForSelectAttribute($deliveryTimeAttribute);
        } else {
            $this->_updateDeliveryTimeForTextAttribute($deliveryTimeAttribute);
        }
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $deliveryTimeAttribute
     */
    protected function _updateDeliveryTimeForSelectAttribute($deliveryTimeAttribute)
    {
        $options = $deliveryTimeAttribute->getSource()->getAllOptions();
        $newOptionsAdded = false;

        foreach($options as $option) {
            if (!$option['label'] || !$option['value']) {
                continue;
            }
            
            if (!$this->_labelExists($option['label'])) {
                $this->_addLabel($option['label']);
                $newOptionsAdded = true;
            }
        }

        $defaultLabel = Mage::getStoreConfig('nrapps_idealo/default_values/delivery_time');
        if (!$this->_labelExists($defaultLabel)) {
            $this->_addLabel($defaultLabel);
            $newOptionsAdded = true;
        }
        
        
        if ($newOptionsAdded) {
            $this->_saveMapping();
        }
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $deliveryTimeAttribute
     */
    protected function _updateDeliveryTimeForTextAttribute($deliveryTimeAttribute)
    {
        $newOptionsAdded = false;

        /** @var $productResource Mage_Catalog_Model_Resource_Product */
        $productResource = Mage::getResourceModel('catalog/product');
        
        /** @var $connection Varien_Db_Adapter_Interface */
        $connection = $productResource->getReadConnection();

        $condition = $connection->quoteInto('attribute_id = ?', $deliveryTimeAttribute->getId());
        
        $select = $connection->select()
            ->distinct()
            ->from($connection->getTableName($deliveryTimeAttribute->getBackendTable()), array('value'))
            ->where('value IS NOT NULL')
            ->where($condition)
            ->group('value');
        
        foreach($select->query()->fetchAll(Zend_Db::FETCH_COLUMN) as $value) {
            if (!$this->_labelExists($value)) {
                $this->_addLabel($value);
                $newOptionsAdded = true;
            }
        }

        $defaultLabel = Mage::getStoreConfig('nrapps_idealo/default_values/delivery_time');
        if (!$this->_labelExists($defaultLabel)) {
            $this->_addLabel($defaultLabel);
            $newOptionsAdded = true;
        }
        
        if ($newOptionsAdded) {
            $this->_saveMapping();
        }
    }

    /**
     * @param string $label
     * @return boolean
     */
    protected function _labelExists($label)
    {
        foreach($this->_getMapping() as $row) {
            if ($row['attribute_value'] == $label) {
                return true;
            }
        }
        
        return false;
    }
    
    protected function _addLabel($label) 
    {
        $this->_mapping[] = array(
            'attribute_value' => $label,
            NRApps_Idealo_Model_Source_ListingCountries::LISTING_COUNTRY_DE => $label,
            NRApps_Idealo_Model_Source_ListingCountries::LISTING_COUNTRY_AT => '',
            NRApps_Idealo_Model_Source_ListingCountries::LISTING_COUNTRY_UK => '',
            NRApps_Idealo_Model_Source_ListingCountries::LISTING_COUNTRY_FR => '',
            NRApps_Idealo_Model_Source_ListingCountries::LISTING_COUNTRY_IT => '',
            NRApps_Idealo_Model_Source_ListingCountries::LISTING_COUNTRY_ES => '',
            NRApps_Idealo_Model_Source_ListingCountries::LISTING_COUNTRY_PL => '',
            NRApps_Idealo_Model_Source_ListingCountries::LISTING_COUNTRY_IN => '',
        );
    }
    
    protected function _getMapping()
    {
        if (is_null($this->_mapping)) {
            $this->_mapping = @unserialize(Mage::getStoreConfig('nrapps_idealo/delivery_times/mapping'));
            if (!$this->_mapping)
            {
                $this->_mapping = array();
            }
        }
        
        return $this->_mapping;
    }

    protected function _saveMapping()
    {
        /** @var $installer Mage_Core_Model_Resource_Setup */
        $installer = Mage::getResourceModel('core/setup', 'core_setup');
        $installer->setConfigData('nrapps_idealo/delivery_times/mapping', serialize($this->_mapping));
    }
}