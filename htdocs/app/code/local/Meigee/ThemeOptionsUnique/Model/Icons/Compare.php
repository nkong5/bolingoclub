<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsUnique_Model_Icons_Compare
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'fa-signal', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-signal')),
            array('value'=>'fa-compress', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-compress')),
            array('value'=>'fa-exchange', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-exchange')),
            array('value'=>'fa-arrows-alt', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-arrows-alt')),
            array('value'=>'fa-bar-chart-o', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-bar-chart-o')),
            array('value'=>'fa-random', 'label'=>Mage::helper('ThemeOptionsUnique')->__('fa-random'))
        );
    }

}