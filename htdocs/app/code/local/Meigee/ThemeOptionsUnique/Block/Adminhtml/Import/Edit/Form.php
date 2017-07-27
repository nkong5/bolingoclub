<?php
/**
 * @version   1.0 12.0.2012
 * @author    Queldorei http://www.queldorei.com <mail@queldorei.com>
 * @copyright Copyright (C) 2010 - 2012 Queldorei
 */
class Meigee_ThemeOptionsUnique_Block_Adminhtml_Import_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $isElementDisabled = false;
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('static_blocks', array('legend'=>Mage::helper('adminhtml')->__('Import Parameters')));

        $fieldset->addField('static_block', 'select', array(
            'name'      => 'static_block',
            'label'     => Mage::helper('cms')->__('Store View'),
            'title'     => Mage::helper('cms')->__('Store View'),
            'required'  => true,
            'values'    => array (
                'skin1'      => 'Skin #1',
				'skin2'		 => 'Skin #2',
				'skin3'		 => 'Skin #3',
				'skin4'		 => 'Skin #4'
				
                ),
            'disabled'  => $isElementDisabled
        ));

        $form->setAction($this->getUrl('*/*/save'));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
