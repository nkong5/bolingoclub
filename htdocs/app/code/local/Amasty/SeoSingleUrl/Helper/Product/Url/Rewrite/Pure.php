<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */


if (!Amasty_SeoSingleUrl_Helper_Data::urlRewriteHelperEnabled()) {
    $autoloader = Varien_Autoload::instance();
    $autoloader->autoload('Amasty_SeoSingleUrl_Helper_Product_Url_Rewrite_Abstract');
} else {
    class Amasty_SeoSingleUrl_Helper_Product_Url_Rewrite_Pure extends Mage_Catalog_Helper_Product_Url_Rewrite {}
}

