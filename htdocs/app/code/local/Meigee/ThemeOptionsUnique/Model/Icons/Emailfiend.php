<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsUnique_Model_Icons_Emailfiend
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'fa-envelope-o', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-envelope-o')),
            array('value'=>'fa-envelope', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-envelope')),
            array('value'=>'fa-folder-open', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-folder-open')),
            array('value'=>'fa-share', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-share')),
            array('value'=>'fa-bullhorn', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-bullhorn')),
            array('value'=>'fa-pencil-square-o', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-pencil-square-o'))
        );
    }

}