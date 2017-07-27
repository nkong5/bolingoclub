<?php

class Diecrema_Startpage_Helper_Copyrecentimages extends Mage_Core_Helper_Abstract
{

    public function copyrecentimages ($storeCode)
    {
        $copied = false;

        $dirCurrentRecord = Mage::getBaseDir() . DS . 'media' . DS . 'startpage'
             . DS . "startpage_$storeCode";

        $storeName = Mage::app()->getStore($storeCode)->getName();

        $pathSkin = Mage::getBaseDir() . DS . 'skin' . DS . 'frontend'
            . DS . 'diecrema' . DS . strtolower($storeName) . DS . 'images' . DS . 'teaser' ;

        if ($handle = opendir($dirCurrentRecord)) {
            while (false !== ($file = readdir($handle))) {
                if($file == '.' || $file == '..') continue;

                if (!copy($dirCurrentRecord . DS . $file, $pathSkin . DS . $file)) {
                    return $copied;
                }
            }
        }

        return $copied;

    }

}


