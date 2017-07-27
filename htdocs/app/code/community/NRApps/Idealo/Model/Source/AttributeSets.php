<?php

class NRApps_Idealo_Model_Source_AttributeSets
{
    const ATTRIBUTE_SETS_ALL = 'all';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array('value' => self::ATTRIBUTE_SETS_ALL, 'label' => Mage::helper('nrapps_idealo')->__('All')),
        );

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getSingleton('catalog/product');
        /** @var $attributeSetCollection Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection */
        $attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter($product->getResource()->getEntityType()->getId());

        foreach($attributeSetCollection as $attributeSet) { /** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
            $options[] = array('value' => $attributeSet->getId(), 'label' => $attributeSet->getAttributeSetName());
        }

        return $options;
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
