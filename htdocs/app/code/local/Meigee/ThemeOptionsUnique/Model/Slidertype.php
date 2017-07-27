<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsUnique_Model_Slidertype
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('ThemeOptionsUnique')->__('Wide Slider')),
            array('value'=>1, 'label'=>Mage::helper('ThemeOptionsUnique')->__('Header 1 Boxed Slider')),
			array('value'=>2, 'label'=>Mage::helper('ThemeOptionsUnique')->__('Header 1 Boxed Slider with banners')),
			array('value'=>3, 'label'=>Mage::helper('ThemeOptionsUnique')->__('Header 2 and Header 3 Boxed Slider')),
			array('value'=>4, 'label'=>Mage::helper('ThemeOptionsUnique')->__('Header 2 and Header 3 Boxed Slider with banners'))
        );
    }

}