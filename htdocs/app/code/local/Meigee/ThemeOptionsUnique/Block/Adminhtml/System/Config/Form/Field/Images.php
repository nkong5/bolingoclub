<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsUnique_Block_Adminhtml_System_Config_Form_Field_Images extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Override method to output our custom image
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        // Get the default HTML for this option
        $html = parent::_getElementHtml($element);

	    $html = '<div class="sidebars-row">';
        $value = $element->getValue();
        if ($values = $element->getValues()) {
            foreach ($values as $option) {
                $html.= $this->_optionToHtml($element, $option, $value);
            }
        }
        $html.= $element->getAfterElementHtml();
	    $html.= '</div>';

        return $html;
    }

	/**
	 * Override method to output wrapper
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @param Array $option
	 * @param String $selected
	 * @return String
	 */
    protected function _optionToHtml($element, $option, $selected)
    {        
        $html = '<div class="meigee-radio">';
	    $html .= '<div class="meigee-thumb"><img src="'.Mage::getDesign()->getSkinUrl('images/'.$option['value'].'.png').'" /></div>';
	    $html .= '<input type="radio"'.$element->serialize(array('name', 'class', 'style'));
        if (is_array($option)) {
            $html.= 'value="'.htmlspecialchars($option['value'], ENT_COMPAT).'"  id="'.$element->getHtmlId().$value.'"';
            if ($option['value'] == $selected) {
                $html.= ' checked="checked"';
            }
            $html.= ' />';
            $html.= '<label class="inline" for="'.$element->getHtmlId().$option['value'].'">'.$option['label'].'</label>';
        }
        elseif ($option instanceof Varien_Object) {
            $html.= 'id="'.$element->getHtmlId().$option->getValue().'"'.$option->serialize(array('label', 'title', 'value', 'class', 'style'));
            if (in_array($option->getValue(), $selected)) {
                $html.= ' checked="checked"';
            }
            $html.= ' />';
            $html.= '<label class="inline" for="'.$element->getHtmlId().$option->getValue().'">'.$option->getLabel().'</label>';
        }
        $html.= '</div>';
        $html.= $element->getSeparator() . "\n";
        return $html;
    }

}