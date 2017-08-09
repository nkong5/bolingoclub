<?php
class AW_Eventbooking_Block_Adminhtml_Catalog_Product_Edit_Tab_Eventbooking_Element
    extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{
    protected function _construct()
    {
        $this->setTemplate('aw_eventbooking/product/edit/ticket/element.phtml');
    }

    public function usedDefault()
    {
        return $this->getElement()->getData('default');
    }

    public function canDisplayUseDefault()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if ($storeId !== 0) {
            return true;
        }
        return false;
    }
}