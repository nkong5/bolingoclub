<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsUnique_Model_labelsorder
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'new_sale', 'label'=>Mage::helper('ThemeOptionsUnique')->__('New, On sale')),
            array('value'=>'sale_new', 'label'=>Mage::helper('ThemeOptionsUnique')->__('On sale, New'))
        );
    }

}