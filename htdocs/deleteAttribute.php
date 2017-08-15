<?php

    error_reporting(E_ALL | E_STRICT);

    require_once './app/Mage.php';
    umask(0);
    Mage::app();

    $attr = 'nrapps_idealo_exclude'; //attribute code to remove

    $setup = Mage::getResourceModel('catalog/setup', 'core_setup');
    try {
        $setup->startSetup();
        $setup->removeAttribute('catalog_product', $attr);
        $setup->endSetup();
        echo $attr . " attribute is removed";
    } catch (Mage_Core_Exception $e) {
        print_r($e->getMessage());
    }
