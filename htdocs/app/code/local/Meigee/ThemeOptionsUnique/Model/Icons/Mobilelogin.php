<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsUnique_Model_Icons_Mobilelogin
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'fa-key', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-key')),
            array('value'=>'fa-sign-in', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-sign-in')),
            array('value'=>'fa-power-off', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-power-off')),
            array('value'=>'fa-check-square-o', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-check-square-o')),
            array('value'=>'fa-user', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-user')),
            array('value'=>'fa-magnet', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-magnet'))
        );
    }

}