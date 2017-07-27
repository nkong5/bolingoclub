<?php

class Diecrema_Startpage_Block_Adminhtml_Startpage_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'diecrema_startpage';
        $this->_controller = 'adminhtml_startpage';

        $this->_updateButton('save', 'label', Mage::helper('diecrema_startpage')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('diecrema_startpage')->__('Delete Item'));
    }

    public function getHeaderText()
    {
//        echo '<pre>'.print_r(Mage::registry('startpage_data')->toArray(), true).'</pre>';



        if( Mage::registry('startpage_data') && Mage::registry('startpage_data')->getId() ) {
            $startpageData = Mage::registry('startpage_data')->toArray();
            $titlePart = $startpageData['teaser_1_large_above_txt'];

            return Mage::helper('diecrema_startpage')->__("Edit Startpage '%s'",
                $this->htmlEscape($titlePart));
        } else {
            return Mage::helper('diecrema_startpage')->__('Add Item');
        }
    }
}