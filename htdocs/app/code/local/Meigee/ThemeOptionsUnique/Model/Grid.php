<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsUnique_Model_Grid
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'2', 'label'=>Mage::helper('ThemeOptionsUnique')->__('2')),
            array('value'=>'3', 'label'=>Mage::helper('ThemeOptionsUnique')->__('3')),
			array('value'=>'4', 'label'=>Mage::helper('ThemeOptionsUnique')->__('4')),
			array('value'=>'5', 'label'=>Mage::helper('ThemeOptionsUnique')->__('5')),
			array('value'=>'6', 'label'=>Mage::helper('ThemeOptionsUnique')->__('6')),
			array('value'=>'7', 'label'=>Mage::helper('ThemeOptionsUnique')->__('7')),
			array('value'=>'8', 'label'=>Mage::helper('ThemeOptionsUnique')->__('8'))
        );
    }

}