<?php

class Meigee_ThemeOptionsUnique_Block_Adminhtml_Restore_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'themeoptionsunique';
        $this->_controller = 'adminhtml_restore';
        $this->_removeButton('reset');
        $this->_updateButton('save', 'label', Mage::helper('ThemeOptionsUnique')->__('Restore'));
    }
 
    public function getHeaderText()
    {
        return Mage::helper('ThemeOptionsUnique')->__('Theme Settings Restore');
    }


    


}