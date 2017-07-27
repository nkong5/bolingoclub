<?php

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$attributeCode = 'is_ecommerce_checkout_approved';
Mage::getSingleton('nrapps_idealo/attribute')->createAttribute($attributeCode, 'yesno');
$installer->setConfigData('nrapps_idealo/attributes/' . $attributeCode, $attributeCode);

$installer->endSetup();