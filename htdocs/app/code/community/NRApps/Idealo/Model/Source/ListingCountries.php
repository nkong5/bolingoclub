<?php

class NRApps_Idealo_Model_Source_ListingCountries extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    const LISTING_COUNTRY_DE = 'DE';
    const LISTING_COUNTRY_AT = 'AT';
    const LISTING_COUNTRY_UK = 'UK';
    const LISTING_COUNTRY_FR = 'FR';
    const LISTING_COUNTRY_IT = 'IT';
    const LISTING_COUNTRY_ES = 'ES';
    const LISTING_COUNTRY_PL = 'PL';
    const LISTING_COUNTRY_IN = 'IN';

    public function getOptionArray()
    {
        $options = array();
        foreach($this->getAllOptions() as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    /**
     * Method used for configuration
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    /**
     * Method used for product attribute
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'value' => self::LISTING_COUNTRY_DE,
                    'label' => Mage::helper('nrapps_idealo')->__('idealo.de'),
                ),
                array(
                    'value' => self::LISTING_COUNTRY_AT,
                    'label' => Mage::helper('nrapps_idealo')->__('idealo.at'),
                ),
                array(
                    'value' => self::LISTING_COUNTRY_UK,
                    'label' => Mage::helper('nrapps_idealo')->__('idealo.co.uk'),
                ),
                array(
                    'value' => self::LISTING_COUNTRY_FR,
                    'label' => Mage::helper('nrapps_idealo')->__('idealo.fr'),
                ),
                array(
                    'value' => self::LISTING_COUNTRY_IT,
                    'label' => Mage::helper('nrapps_idealo')->__('idealo.it'),
                ),
                array(
                    'value' => self::LISTING_COUNTRY_ES,
                    'label' => Mage::helper('nrapps_idealo')->__('idealo.es'),
                ),
                array(
                    'value' => self::LISTING_COUNTRY_PL,
                    'label' => Mage::helper('nrapps_idealo')->__('idealo.pl'),
                ),
                array(
                    'value' => self::LISTING_COUNTRY_IN,
                    'label' => Mage::helper('nrapps_idealo')->__('idealo.in'),
                ),
            );
        }
        return $this->_options;
    }
}
