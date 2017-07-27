<?php

class Diecrema_Startpage_Block_Adminhtml_Startpage_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('startpage_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('diecrema_startpage')->__('News Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('diecrema_startpage')->__('Item Information'),
            'title'     => Mage::helper('diecrema_startpage')->__('Item Information'),
            'content'   => $this->getLayout()->createBlock('diecrema_startpage/adminhtml_startpage_edit_tab_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}