<?php

class NRApps_Idealo_Block_Config_Adminhtml_Form_Field_Attributes extends Mage_Core_Block_Html_Select
{
    public function _toHtml()
    {
        $options = Mage::getSingleton('nrapps_idealo/source_productAttributes')->getOptionArray();
        foreach ($options as $a => $label) {
            $this->addOption($a, $this->_addSlashesDependingOnMagentoVersion($label));
        }

        $this->setExtraParams('style="width: 200px;"');

        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Add slashes for Magento CE 1.9.2.0+ and EE 1.14.2.0+. This is needed as Magento has introduced its own
     * escaping in app/design/adminhtml/default/default/template/system/config/form/field/array.phtml in these versions.
     *
     * @param string $label
     * @return string
     */
    protected function _addSlashesDependingOnMagentoVersion($label)
    {
        if (method_exists('Mage', 'getEdition')) {
            switch (Mage::getEdition()) {
                case Mage::EDITION_COMMUNITY:
                    if (version_compare(Mage::getVersion(), '1.9.2.0') >= 0) {
                        return $label;
                    }
                    break;
                case Mage::EDITION_ENTERPRISE:
                    if (version_compare(Mage::getVersion(), '1.14.2.0') >= 0) {
                        return $label;
                    }
                    break;
            }
        }
        return addslashes($label);
    }
}