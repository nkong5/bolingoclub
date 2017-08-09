<?php

class AW_Eventbooking_Block_Adminhtml_Catalog_Product_Edit_Tab_Eventbooking_Attendees_Column_Renderer_Customer
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $customerId = $row->getData('order_customer_id');
        if (!$customerId) {
            $value = $this->_getValue($row);
            if (strlen(trim($value)) < 1) {
                $value = Mage::helper('sales')->__('Guest');
            }
            return $value;
        }
        $url = $this->getUrl('*/customer/edit', array('id' => $row->getData('order_customer_id')));
        return sprintf('<a href="%s">%s</a>', $url, $this->_getValue($row));
    }

    public function renderExport(Varien_Object $row)
    {
        return $this->_getValue($row);
    }
}