<?php class Meigee_MeigeewidgetsUnique_Model_Buttonsposition
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'0', 'label'=>Mage::helper('meigeewidgetsunique')->__('Above slider')),
            array('value'=>'1', 'label'=>Mage::helper('meigeewidgetsunique')->__('Inside slider'))
        );
    }

}