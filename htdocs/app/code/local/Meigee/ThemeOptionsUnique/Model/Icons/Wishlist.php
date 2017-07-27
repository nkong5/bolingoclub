<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsUnique_Model_Icons_Wishlist
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'fa-heart-o', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-heart-o')),
            array('value'=>'fa-thumbs-o-up', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-thumbs-o-up')),
            array('value'=>'fa-star', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-star')),
            array('value'=>'fa-thumbs-up', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-thumbs-up')),
            array('value'=>'fa-heart', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-heart')),
            array('value'=>'fa-lightbulb-o', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-lightbulb-o'))
        );
    }

}