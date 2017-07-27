<?php

class NRApps_Idealo_Block_Adminhtml_Attributes extends Mage_Adminhtml_Block_Widget
{
    /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection */
    protected $_attributes;

    /** @var string[] */
    protected $_attributeCodes = array();

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('nrapps/idealo/attributes.phtml');
        $this->setTitle($this->__('Manage Attributes'));
    }

    /**
     * Retrieve the POST URL for the form
     *
     * @return string URL
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/save');
    }

    public function getAttributeData()
    {
        return Mage::getSingleton('nrapps_idealo/source_feedAttributes')->getAllOptions();
    }

    /**
     * @param Varien_Object $attribute
     * @return bool
     */
    public function attributeExists($attribute)
    {
        Mage::log($attribute->getType());
        return in_array($attribute->getCode(), $this->getProductAttributeCodes($attribute->getType()));
    }

    public function isAttributeNone($attribute)
    {
        return Mage::getStoreConfig('nrapps_idealo/attributes/' . $attribute->getCode()) == 'none';
    }

    /**
     * @param string[]|string $types
     * @return string[]
     */
    public function getProductAttributeCodes($types = null)
    {
        return $this->getProductAttributes($types)->getColumnValues('attribute_code');
    }

    /**
     * @param string[]|string $types
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function getProductAttributes($types = null)
    {
        return Mage::helper('nrapps_idealo')->getProductAttributes($types);
    }

    /**
     * @param string $attributeType
     * @return string
     */
    public function getAttributeTypeLabel($attributeType)
    {
        switch ($attributeType) {
            case 'select':
                return $this->__('Select field (dropdown)');
            case 'yesno':
                return $this->__('Select field (yes / no)');
            case 'text':
                return $this->__('Text field');
            case 'decimal':
                return $this->__('Text field (decimal value)');
            case 'price':
                return $this->__('Text field (price)');
            default:
                return $attributeType;
        }
    }

    /**
     * @param Varien_Object $attribute
     * @return string
     */
    public function getSelectedAttributeCode($attribute)
    {
        if (Mage::getStoreConfig('nrapps_idealo/attributes/' . $attribute->getCode())) {
            return Mage::getStoreConfig('nrapps_idealo/attributes/' . $attribute->getCode());
        }

        return $attribute->getCode();
    }
}
