<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsUnique_Model_Maptype
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'ROADMAP', 'label'=>Mage::helper('ThemeOptionsUnique')->__('Normal street map')),
            array('value'=>'SATELLITE', 'label'=>Mage::helper('ThemeOptionsUnique')->__('Satellite images')),
			array('value'=>'TERRAIN', 'label'=>Mage::helper('ThemeOptionsUnique')->__('Map with physical features'))
        );
    }

}