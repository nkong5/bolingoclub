<?php


class NRApps_Idealo_Block_Config_AttributesNoSortOrder extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    protected $_itemRenderer;

    public function _prepareToRender()
    {
        $this->addColumn('attribute_code', array(
            'label' => Mage::helper('nrapps_idealo')->__('Attribute'),
            'renderer' => $this->_getRenderer(),
            'style' => 'width:200px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('nrapps_idealo')->__('Add');
    }

    /**
     * @return NRApps_Idealo_Block_Config_Adminhtml_Form_Field_PaymentMethods
     */
    protected function  _getRenderer() {
        if (!$this->_itemRenderer) {
            $this->_itemRenderer = $this->getLayout()->createBlock(
                'nrapps_idealo/config_adminhtml_form_field_attributes', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_itemRenderer;
    }

    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getRenderer()->calcOptionHash($row->getData('attribute_code')),
            'selected="selected"'
        );
    }

}