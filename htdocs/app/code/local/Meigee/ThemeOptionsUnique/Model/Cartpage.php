<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsUnique_Model_Cartpage
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'cart_standard', 'label'=>Mage::helper('ThemeOptionsUnique')->__('Default Cart')),
            array('value'=>'cart_accordion', 'label'=>Mage::helper('ThemeOptionsUnique')->__('Accordion')),
			array('value'=>'cart_new_default', 'label'=>Mage::helper('ThemeOptionsUnique')->__('New Default Cart')),
			
        );
    }

}