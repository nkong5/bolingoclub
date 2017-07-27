<?php

class NRApps_Idealo_Block_Config_DeliveryTimeMapping extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_itemRenderer;

    public function _prepareToRender()
    {
        $this->addColumn('dummy', array(
            'label' => ' ',
            'renderer' => $this->_getRenderer(),
        ));
        $this->addColumn('attribute_value', array(
            'label' => Mage::helper('nrapps_idealo')->__('Attribute value'),
            'style' => 'width:100px',
        ));

        $this->addColumn(NRApps_Idealo_Model_Source_ListingCountries::LISTING_COUNTRY_DE, array(
            'label' => Mage::helper('nrapps_idealo')->__('idealo.de'),
            'style' => 'width:100px',
        ));

        $this->addColumn(NRApps_Idealo_Model_Source_ListingCountries::LISTING_COUNTRY_AT, array(
            'label' => Mage::helper('nrapps_idealo')->__('idealo.at'),
            'style' => 'width:100px',
        ));

        $this->addColumn(NRApps_Idealo_Model_Source_ListingCountries::LISTING_COUNTRY_UK, array(
            'label' => Mage::helper('nrapps_idealo')->__('idealo.co.uk'),
            'style' => 'width:100px',
        ));

        $this->addColumn(NRApps_Idealo_Model_Source_ListingCountries::LISTING_COUNTRY_FR, array(
            'label' => Mage::helper('nrapps_idealo')->__('idealo.fr'),
            'style' => 'width:100px',
        ));

        $this->addColumn(NRApps_Idealo_Model_Source_ListingCountries::LISTING_COUNTRY_IT, array(
            'label' => Mage::helper('nrapps_idealo')->__('idealo.it'),
            'style' => 'width:100px',
        ));

        $this->addColumn(NRApps_Idealo_Model_Source_ListingCountries::LISTING_COUNTRY_ES, array(
            'label' => Mage::helper('nrapps_idealo')->__('idealo.es'),
            'style' => 'width:100px',
        ));

        $this->addColumn(NRApps_Idealo_Model_Source_ListingCountries::LISTING_COUNTRY_PL, array(
            'label' => Mage::helper('nrapps_idealo')->__('idealo.pl'),
            'style' => 'width:100px',
        ));

        $this->addColumn(NRApps_Idealo_Model_Source_ListingCountries::LISTING_COUNTRY_IN, array(
            'label' => Mage::helper('nrapps_idealo')->__('idealo.in'),
            'style' => 'width:100px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('nrapps_idealo')->__('Add');
    }

    /**
     * @return NRApps_Idealo_Block_Config_Adminhtml_Form_Field_PaymentMethods
     */
    protected function  _getRenderer() {
        if (!$this->_itemRenderer) {
            $this->_itemRenderer = $this->getLayout()->createBlock(
                'nrapps_idealo/config_adminhtml_form_field_dummy', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_itemRenderer;
    }

    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getRenderer()->calcOptionHash($row->getData('attribute_code')),
            'selected="selected"'
        );
    }
}
