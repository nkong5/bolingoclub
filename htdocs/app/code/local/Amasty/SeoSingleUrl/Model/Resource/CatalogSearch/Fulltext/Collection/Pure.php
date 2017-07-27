<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */


if (Mage::helper('core')->isModuleEnabled('TBT_Bss')) {
    $autoloader = Varien_Autoload::instance();
    $autoloader->autoload('TBT_Bss_Model_CatalogSearch_Mysql4_Fulltext_Collection');
} else {
    class Amasty_SeoSingleUrl_Model_Resource_CatalogSearch_Fulltext_Collection_Pure extends Mage_CatalogSearch_Model_Resource_Fulltext_Collection {}
}

