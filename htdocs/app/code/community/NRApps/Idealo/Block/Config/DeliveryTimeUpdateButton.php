<?php


class NRApps_Idealo_Block_Config_DeliveryTimeUpdateButton extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->getLayout()->createBlock('adminhtml/template')->setTemplate('nrapps/idealo/delivery_time_update_button.phtml')->toHtml();
    }

    /**
     * Enter description here...
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $id = $element->getHtmlId();

        $html = '<tr id="row_' . $id . '">'
            . '<td colspan="3">' . $this->_getElementHtml($element) . '</td>';


        $html.= '<td></td>';
        $html.= '</tr>';
        return $html;
    }
}