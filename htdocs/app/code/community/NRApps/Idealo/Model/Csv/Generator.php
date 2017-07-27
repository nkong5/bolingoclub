<?php
class NRApps_Idealo_Model_Csv_Generator extends NRApps_Idealo_Model_Generator
{
    /**
     * @return NRApps_Idealo_Model_Feed
     */
    public function getFeed()
    {
        $feed = Mage::getSingleton('nrapps_idealo/csv_feed');
        if (!is_array($feed->getAdditionalAttributeCodes())) {
            $attributeCodes = array();
            foreach (unserialize(Mage::getStoreConfig('nrapps_idealo/product_options/add_attributes_to_export', $this->_getStoreId())) as $row) {
                $attributeCodes[] = $row['attribute_code'];
            }
            $feed->setAdditionalAttributeCodes($attributeCodes);
        }
        return $feed;
    }
}