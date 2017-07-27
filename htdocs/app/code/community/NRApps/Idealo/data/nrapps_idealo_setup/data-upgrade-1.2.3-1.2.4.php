<?php

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

// delete existing orders on updating module
foreach(Mage::app()->getStores() as $store) {
    $installer->setConfigData('nrapps_idealo/existing_offers_deleted', 0, 'stores', $store->getId());
}

Mage::getResourceModel('nrapps_idealo/indexer')->markAllUnprocessed();

$installer->endSetup();