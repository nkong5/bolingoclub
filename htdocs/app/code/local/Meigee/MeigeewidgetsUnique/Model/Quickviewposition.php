<?php class Meigee_MeigeewidgetsUnique_Model_Quickviewposition
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'0', 'label'=>Mage::helper('meigeewidgetsunique')->__('Bottom')),
            array('value'=>'1', 'label'=>Mage::helper('meigeewidgetsunique')->__('On image'))
        );
    }

}