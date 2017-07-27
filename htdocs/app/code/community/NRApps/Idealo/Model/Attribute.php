<?php
class NRApps_Idealo_Model_Attribute
{
    /**
     * Create a new product attribute
     *
     * @param string $attributeCode
     * @param string $attributeType
     */
    public function createAttribute($attributeCode, $attributeType)
    {
        $attributeData = array(
            'type'              => 'varchar',
            'input'             => $attributeType,
            'label'             => Mage::getSingleton('nrapps_idealo/source_feedAttributes')->getOption($attributeCode)->getLabel(),
            'user_defined'      => 1,
            'group'             => 'idealo',
            'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
            'required'          => 0,
            'visible'           => 1,
            'searchable'        => 1,
            'filterable'        => 0,
            'filterable_in_search' => 0,
            'visible_on_front'  => 0,
            'is_configurable'   => 0,
        );

        $attributeInfo = Mage::getSingleton('nrapps_idealo/source_feedAttributes')->getOption($attributeCode);
        if (isset($attributeInfo['note'])) {
            $attributeData['note'] = $attributeInfo['note'];
        }

        if ($attributeType == 'select') {
            $attributeData['type'] = 'int';
            $attributeData['source'] = 'eav/entity_attribute_source_table';
            $attributeData['global'] = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE;
        }

        if ($attributeType == 'yesno') {
            $attributeData['type'] = 'varchar';
            $attributeData['input'] = 'select';
            $attributeData['source'] = 'nrapps_idealo/source_yesNoDefault';
            $attributeData['global'] = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE;
        }

        if ($attributeType == 'decimal') {
            $attributeData['type'] = 'decimal';
            $attributeData['input'] = 'text';
            $attributeData['global'] = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE;
        }

        if ($attributeType == 'price') {
            $attributeData['type'] = 'decimal';
            $attributeData['input'] = 'price';
            $attributeData['global'] = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE;
        }

        $this->_getSetup()->addAttribute('catalog_product', $attributeCode, $attributeData);
    }

    /**
     * @return Mage_Catalog_Model_Resource_Setup
     */
    protected function _getSetup()
    {
        return Mage::getResourceModel('catalog/setup', 'catalog_setup');
    }
}