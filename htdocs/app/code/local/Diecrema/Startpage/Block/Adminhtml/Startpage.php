<?php
class Diecrema_Startpage_Block_Adminhtml_Startpage extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_startpage';
        $this->_blockGroup = 'diecrema_startpage';
        $this->_headerText = Mage::helper('diecrema_startpage')->__('Item Manager');
        $this->_addButtonLabel = Mage::helper('diecrema_startpage')->__('Add Item');
        parent::__construct();
    }
}