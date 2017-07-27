<?php

class Diecrema_Startpage_Block_Adminhtml_Startpage_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        # get current data
        if (Mage::getSingleton('adminhtml/session')->getStartpageData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getStartpageData();
            Mage::getSingleton('adminhtml/session')->getStartpageData(null);
        }
        elseif (Mage::registry('startpage_data'))
        {
            $data = Mage::registry('startpage_data')->getData();
        }
        else
        {
            $data = array();
        }

        $imageDirLink = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) .  DS
                      . 'startpage' . DS . "startpage_" . $data['store_code'];

//        echo '<pre>'.print_r($data, true).'</pre>';

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('first_image',
            array('legend'=>Mage::helper('diecrema_startpage')->__('First Image')));

        $fieldset->addField('startpage_id', 'hidden', array(
            'name' => 'startpage_id',
        ));

        $teaser1LargePath = (isset($data['teaser_1_large']))
                             ? $imageDirLink . '/' . $data['teaser_1_large'] : '';

        $fieldset->addField('teaser_1_large', 'file', array(
             'label'     => Mage::helper('diecrema_startpage')->__('Big Image'),
             'name'      => 'teaser_1_large',
             'note'     =>
                "Image will always be shown first in animation.
                <img src='$teaser1LargePath' width='200' />",
        ));

        $fieldset->addField('teaser_1_large_above_txt', 'text', array(
             'label'     => Mage::helper('diecrema_startpage')->__('Text above'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'teaser_1_large_above_txt',
        ));

        $fieldset->addField('teaser_1_large_below_txt', 'text', array(
             'label'     => Mage::helper('diecrema_startpage')->__('Text below:'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'teaser_1_large_below_txt',
        ));

        $teaser1SmallPath = (isset($data['teaser_1_small']))
                          ? $imageDirLink . '/' . $data['teaser_1_small'] : '';

        $fieldset->addField('teaser_1_small', 'file', array(
             'label'     => Mage::helper('diecrema_startpage')->__('Small Image'),
             'name'      => 'teaser_1_small',
             'note'     => Mage::helper('diecrema_startpage')->__(
                "Image is resized form of \"First Big Image\". Will be shown at the upper right box.
                <img src='$teaser1SmallPath' width='200' />"),
        ));

        $teaser1SmallTxtPath = (isset($data['teaser_1_small_txt']))
                          ? $imageDirLink . '/' . $data['teaser_1_small_txt'] : '';

        $fieldset->addField('teaser_1_small_txt', 'file', array(
             'label'     => Mage::helper('diecrema_startpage')->__('Small Image Text '),
             'name'      => 'teaser_1_small_txt',
             'note'     => Mage::helper('diecrema_startpage')->__(
                "Image is text placed on small image.
                <img src='$teaser1SmallTxtPath' width='200' />"),
        ));

        $fieldset->addField('teaser_1_link', 'text', array(
             'label'     => Mage::helper('diecrema_startpage')->__('Product link'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'teaser_1_link',
        ));


        /* Fields for second image */

        $fieldset = $form->addFieldset('second_image', array(
             'legend' =>Mage::helper('diecrema_startpage')->__('Second Image')
        ));

        $teaser2LargePath = (isset($data['teaser_2_large']))
                             ? $imageDirLink . '/' . $data['teaser_2_large'] : '';

        $fieldset->addField('teaser_2_large', 'file', array(
             'label'     => Mage::helper('diecrema_startpage')->__('Second Big Image'),
             'name'      => 'teaser_2_large',
             'note'     =>
                "Image will always be shown second in animation.
                <img src='$teaser2LargePath' width='200' />"
        ));

        $fieldset->addField('teaser_2_large_above_txt', 'text', array(
             'label'     => Mage::helper('diecrema_startpage')->__('Second Big Image (Text above):'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'teaser_2_large_above_txt',
        ));

        $fieldset->addField('teaser_2_large_below_txt', 'text', array(
             'label'     => Mage::helper('diecrema_startpage')->__('Second Big Image (Text below):'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'teaser_2_large_below_txt',
        ));

        $teaser2SmallPath = (isset($data['teaser_2_small']))
                          ? $imageDirLink . '/' . $data['teaser_2_small'] : '';

        $fieldset->addField('teaser_2_small', 'file', array(
             'label'     => Mage::helper('diecrema_startpage')->__('Second Small Image:'),
             'name'      => 'teaser_2_small',
             'note'     => Mage::helper('diecrema_startpage')->__(
                "Image is resized form of \"Second Big Image\". Will be shown at the upper right box.
                <img src='$teaser2SmallPath' width='200' />"),
        ));

        $teaser2SmallTxtPath = (isset($data['teaser_2_small_txt']))
                          ? $imageDirLink . '/' . $data['teaser_2_small_txt'] : '';
        $fieldset->addField('teaser_2_small_txt', 'file', array(
             'label'     => Mage::helper('diecrema_startpage')->__('Second Small Image (Text): '),
             'name'      => 'teaser_2_small_txt',
             'note'     => Mage::helper('diecrema_startpage')->__(
                "Image is text placed on small image.
                <img src='$teaser2SmallTxtPath' width='200' />"),
        ));

        $fieldset->addField('teaser_2_link', 'text', array(
             'label'     => Mage::helper('diecrema_startpage')->__('Second Image (Product link):'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'teaser_2_link',
        ));

        /* Fields for third image */

        $fieldset = $form->addFieldset('third_image', array(
             'legend' =>Mage::helper('diecrema_startpage')->__('Third Image')
        ));

        $fieldset->addField('teaser_3_large', 'file', array(
             'label'     => Mage::helper('diecrema_startpage')->__('Third Big Image'),
             'name'      => 'teaser_3_large',
             'note'     => Mage::helper('diecrema_startpage')->__(
                'Image will always be shown third in animation:'),
        ));

        $fieldset->addField('teaser_3_large_above_txt', 'text', array(
             'label'     => Mage::helper('diecrema_startpage')->__('Third Big Image (Text above):'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'teaser_3_large_above_txt',
        ));

        $fieldset->addField('teaser_3_large_below_txt', 'text', array(
             'label'     => Mage::helper('diecrema_startpage')->__('Third Big Image (Text below):'),
             'name'      => 'teaser_3_large_below_txt',
        ));

        $teaser3SmallPath = (isset($data['teaser_3_small']))
                          ? $imageDirLink . '/' . $data['teaser_3_small'] : '';

        $fieldset->addField('teaser_3_small', 'file', array(
             'label'     => Mage::helper('diecrema_startpage')->__('Third Small Image:'),
             'name'      => 'teaser_3_small',
             'note'     => Mage::helper('diecrema_startpage')->__(
                "Image is resized form of \"Third Big Image\". Will be shown at the upper right box.
                <img src='$teaser3SmallPath' width='200' />"),
        ));

        $teaser3SmallTxtPath = (isset($data['teaser_3_small_txt']))
                          ? $imageDirLink . '/' . $data['teaser_3_small_txt'] : '';

        $fieldset->addField('teaser_3_small_txt', 'file', array(
             'label'     => Mage::helper('diecrema_startpage')->__('Third Small Image (Text): '),
             'name'      => 'teaser_3_small_txt',
             'note'     => Mage::helper('diecrema_startpage')->__(
                "Image is text placed on small image.
                <img src='$teaser3SmallTxtPath' width='200' />"),
        ));

        $fieldset->addField('teaser_3_link', 'text', array(
             'label'     => Mage::helper('diecrema_startpage')->__('Third Image (Product link):'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'teaser_3_link',
        ));



         /*
        $fieldset->addField('intro_txt', 'editor', array(
            'name'      => 'intro_txt',
            'label'     => Mage::helper('diecrema_startpage')->__('Content'),
            'title'     => Mage::helper('diecrema_startpage')->__('Content'),
            'style'     => 'width:98%; height:400px;',
            'wysiwyg'   => false,
            'required'  => true,
        ));
 *
 */


        $fieldset = $form->addFieldset('misc_set',
            array('legend'=>Mage::helper('diecrema_startpage')->__('Miscellaneous')));

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('diecrema_startpage')->__('Status'),
            'name'      => 'status',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('diecrema_startpage')->__('Active'),
                ),

                array(
                    'value'     => 0,
                    'label'     => Mage::helper('diecrema_startpage')->__('Inactive'),
                ),
            ),
        ));

        $stores = Mage::app()->getStores();
        $storeOptions = array();
        foreach ($stores as $key => $store) {
            $storeOptions[] = array('value' => $store->getCode(), 'label' => $store->getName());
        }

        $fieldset->addField('store_code', 'select', array(
            'label'     => Mage::helper('diecrema_startpage')->__('Store'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'store_code',
            'values'    => $storeOptions,
        ));

        if ( Mage::getSingleton('adminhtml/session')->getStartpageData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getStartpageData());
            Mage::getSingleton('adminhtml/session')->setStartpageData(null);
        } elseif ( Mage::registry('startpage_data') ) {
            $form->setValues(Mage::registry('startpage_data')->getData());
        }
        return parent::_prepareForm();
    }
}