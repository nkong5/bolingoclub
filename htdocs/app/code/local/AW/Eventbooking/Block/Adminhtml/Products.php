<?php

class AW_Eventbooking_Block_Adminhtml_Products extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_products';
        $this->_blockGroup = 'aw_eventbooking';
        $this->_headerText = $this->__('Manage Products');
        parent::__construct();
    }

    public function getCreateUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product/new');
    }
}
