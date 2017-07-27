<?php

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

// prevent deletion of existing orders on updating module
$installer->setConfigData('nrapps_idealo/existing_offers_deleted', 1);

$installer->endSetup();