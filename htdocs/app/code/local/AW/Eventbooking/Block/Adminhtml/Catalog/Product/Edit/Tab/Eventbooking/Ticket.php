<?php
class AW_Eventbooking_Block_Adminhtml_Catalog_Product_Edit_Tab_Eventbooking_Ticket
    extends Mage_Adminhtml_Block_Widget
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Form element instance
     *
     * @var Varien_Data_Form_Element_Abstract
     */
    protected $_element;

    /**
     * Initialize block
     */
    public function __construct()
    {
        $this->setTemplate('aw_eventbooking/product/edit/ticket/settings.phtml');
    }

    /**
     * Retrieve current product instance
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * Render HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * Set form element instance
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Group_Abstract
     */
    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * Retrieve form element instance
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Retrieve 'add group price item' button HTML
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('aw_eventbooking_add_button');
    }

    /**
     * Prepare global layout
     *
     * Add "Add Group Price" button to layout
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Group
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'price_type',
            $this->getLayout()->createBlock('adminhtml/html_select')->setData(
                array(
                    'id'    => 'ticket_{{id}}_price_type',
                    'name'  => 'event[ticket_data][{{id}}][global][price_type]',
                    'class' => 'select price-type',
                )
            )
        );

        $this->getChild('price_type')->setOptions(
            Mage::getSingleton('adminhtml/system_config_source_product_options_price')->toOptionArray()
        );

        $this->setChild('confirmation_email',
            $this->getLayout()->createBlock('adminhtml/html_select')->setData(
                array(
                    'id'      => 'ticket_{{id}}_confirmation_template_id',
                    'name'    => 'event[ticket_data][{{id}}][value][confirmation_template_id]',
                    'class'   => 'select',
                    'extra_params' => 'disabled="disabled"'
                )
            )
        );

        $this->getChild('confirmation_email')->setOptions(
            Mage::getSingleton('aw_eventbooking/source_email_template')->toOptionArray()
        );

        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id'      => 'aw_eventbooking_ticket_add_new_row',
                'label'   => $this->__('Add New Row'),
                'class'   => 'add'
            ));
        if ($this->canDisplayUseDefault()) {
            $button->setData('disabled', true);
            $button->setData('class',  $button->getData('class') . ' disabled');
        }
        $button->setName('aw_eventbooking_add_button');

        /* @var $button Mage_Adminhtml_Block_Widget_Button */
        $this->setChild('aw_eventbooking_add_button', $button);
        return parent::_prepareLayout();
    }

    public function getValues()
    {
        $values = array();
        $data = $this->getElement()->getValue();

        if (is_array($data)) {
            $values = $this->_sortValues($data);
        }

        return $values;
    }

    /**
     * Sort values
     *
     * @param array $data
     * @return array
     */
    protected function _sortValues($data)
    {
        return $data;
    }

    /**
     * Get html of Price Type select element
     *
     * @return string
     */
    public function getPriceTypeSelectHtml()
    {
        return $this->getChildHtml('price_type');
    }

    /**
     * Get html of Confirmation Email Template select element
     *
     * @return string
     */
    public function getConfirmationEmailSelectHtml()
    {
        return $this->getChildHtml('confirmation_email');
    }

    public function canDisplayUseDefault()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if ($storeId !== 0) {
            return true;
        }
        return false;
    }
}