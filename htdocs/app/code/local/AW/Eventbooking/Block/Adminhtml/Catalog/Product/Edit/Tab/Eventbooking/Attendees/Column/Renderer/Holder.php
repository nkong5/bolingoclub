<?php

class AW_Eventbooking_Block_Adminhtml_Catalog_Product_Edit_Tab_Eventbooking_Attendees_Column_Renderer_Holder
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $holderName = $row->getData('holder_name');
        $holderEmail = $row->getData('holder_email');
        if (!$holderName && !$holderEmail) {
            return '';
        } else if (!$holderEmail) {
            return $holderName;
        }
        return $holderName . $this->escapeHtml(' <' . $holderEmail . '>');
    }

    public function renderExport(Varien_Object $row)
    {
        $holderName = $row->getData('holder_name');
        $holderEmail = $row->getData('holder_email');
        if (!$holderName && !$holderEmail) {
            return '';
        } else if (!$holderEmail) {
            return $holderName;
        }
        return $holderName . '|' . $holderEmail;
    }
}