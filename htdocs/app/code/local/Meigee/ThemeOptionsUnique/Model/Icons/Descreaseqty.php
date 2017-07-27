<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsUnique_Model_Icons_Descreaseqty
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'fa-minus-square-o', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-minus-square-o')),
            array('value'=>'fa-angle-left', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-angle-left')),
            array('value'=>'fa-arrow-left', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-arrow-left')),
            array('value'=>'fa-long-arrow-left', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-long-arrow-left')),
            array('value'=>'fa-step-backward', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-step-backward')),
            array('value'=>'fa-angle-double-left', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-angle-double-left'))
        );
    }

}