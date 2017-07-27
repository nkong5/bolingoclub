<?php class Meigee_MeigeewidgetsUnique_Model_Boolean
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'1', 'label'=>Mage::helper('meigeewidgetsunique')->__('True')),
            array('value'=>'0', 'label'=>Mage::helper('meigeewidgetsunique')->__('False'))
        );
    }

}