<?php

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

if ($comment = Mage::getStoreConfig('nrapps_idealo/default_values/shipping_comment')) {
    $comments = array
    (
        '_1438596823621_621' => array
        (
            'listing_at' => NRApps_Idealo_Model_Source_ListingCountries::LISTING_COUNTRY_DE,
            'comment' => $comment
        )

);
    $installer->setConfigData('nrapps_idealo/default_values/shipping_comment', null);
    $installer->setConfigData('nrapps_idealo/default_values/shipping_comments', serialize($comments));
}

Mage::getSingleton('nrapps_idealo/deliveryTime')->update();

$installer->endSetup();