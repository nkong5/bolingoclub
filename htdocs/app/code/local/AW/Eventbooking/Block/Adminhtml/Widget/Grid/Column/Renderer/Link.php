<?php

class AW_Eventbooking_Block_Adminhtml_Widget_Grid_Column_Renderer_Link
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        /** @var Mage_Adminhtml_Block_Widget_Grid_Column $column */
        $column = $this->getColumn();
        $linkAction = $column->getData('link_action');
        $linkParams = $column->getData('link_params');
        if (!$linkParams) {
            $linkParams = array();
        }
        foreach ($linkParams as $k => $v) {
            if ($row->getData($v)) {
                $linkParams[$k] = $row->getData($v);
            }
        }
        return sprintf(
            "<a href='%s'>%s</a>",
            Mage::helper('adminhtml')->getUrl($linkAction, $linkParams),
            $this->_getValue($row)
        );
    }
}
