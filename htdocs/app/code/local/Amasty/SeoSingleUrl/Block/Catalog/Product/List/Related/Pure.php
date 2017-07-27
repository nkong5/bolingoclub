<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */


if (Mage::helper('core')->isModuleEnabled('Amasty_Mostviewed')) {
    $autoloader = Varien_Autoload::instance();
    $autoloader->autoload('Amasty_SeoSingleUrl_Block_Catalog_Product_List_Related_Mostviewed');
} else {
    class Amasty_SeoSingleUrl_Block_Catalog_Product_List_Related_Pure extends Mage_Catalog_Block_Product_List_Related {}
}

