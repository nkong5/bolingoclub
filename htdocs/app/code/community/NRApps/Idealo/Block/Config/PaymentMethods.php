<?php


class NRApps_Idealo_Block_Config_PaymentMethods extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    protected $_itemRenderer;

    public function _prepareToRender()
    {
        $this->addColumn('payment_method', array(
            'label' => Mage::helper('nrapps_idealo')->__('Payment Method'),
            'renderer' => $this->_getRenderer(),
            'style' => 'width:100px',
        ));

        $this->addColumn('surcharge', array(
            'label' => Mage::helper('nrapps_idealo')->__('Fixed Surcharge'),
            'style' => 'width:100px',
        ));

        $this->addColumn('percental_surcharge', array(
            'label' => Mage::helper('nrapps_idealo')->__('Percental Surcharge'),
            'style' => 'width:100px',
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
                'nrapps_idealo/config_adminhtml_form_field_paymentMethods', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_itemRenderer;
    }

    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getRenderer()->calcOptionHash($row->getData('payment_method')),
            'selected="selected"'
        );
    }

}