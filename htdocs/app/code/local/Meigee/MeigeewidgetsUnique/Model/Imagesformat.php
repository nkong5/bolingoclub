<?php class Meigee_MeigeewidgetsUnique_Model_Imagesformat
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'.png', 'label'=>Mage::helper('meigeewidgetsunique')->__('.png')),
            array('value'=>'.jpg', 'label'=>Mage::helper('meigeewidgetsunique')->__('.jpg')),
            array('value'=>'.gif', 'label'=>Mage::helper('meigeewidgetsunique')->__('.gif'))
        );
    }

}