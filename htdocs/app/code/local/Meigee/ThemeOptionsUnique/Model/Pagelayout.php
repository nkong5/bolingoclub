<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsUnique_Model_Pagelayout
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'left', 'label'=>Mage::helper('ThemeOptionsUnique')->__('Left')),
            array('value'=>'right', 'label'=>Mage::helper('ThemeOptionsUnique')->__('Right')),
            array('value'=>'none', 'label'=>Mage::helper('ThemeOptionsUnique')->__('No Sidebar'))          
        );
    }

}