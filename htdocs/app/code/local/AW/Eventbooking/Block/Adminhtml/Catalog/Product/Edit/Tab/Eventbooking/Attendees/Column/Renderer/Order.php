<?php

class AW_Eventbooking_Block_Adminhtml_Catalog_Product_Edit_Tab_Eventbooking_Attendees_Column_Renderer_Order
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $url = $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getData('order_id')));
        return sprintf('<a href="%s">%s</a>', $url, $this->_getValue($row));
    }

    public function renderExport(Varien_Object $row)
    {
        return$this->_getValue($row);
    }
}