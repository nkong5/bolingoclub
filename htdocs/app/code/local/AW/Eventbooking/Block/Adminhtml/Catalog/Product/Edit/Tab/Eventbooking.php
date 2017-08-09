<?php
class AW_Eventbooking_Block_Adminhtml_Catalog_Product_Edit_Tab_Eventbooking
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_adminRolesCollection = null;
    protected $_eventByRequst = null;

    /**
     * @return null|Mage_Admin_Model_Resource_Roles_Collection
     */
    protected function _getAdminRolesCollection()
    {
        if (is_null($this->_adminRolesCollection)) {
            $this->_adminRolesCollection = Mage::getModel('admin/roles')->getCollection();
        }
        return $this->_adminRolesCollection;
    }

    protected function _prepareForm()
    {
        $this->_initForm()->_setFormsValues();
        return parent::_prepareForm();
    }

    /**
     * @return AW_Eventbooking_Block_Adminhtml_Catalog_Product_Edit_Tab_Eventbooking
     */
    protected function _initForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $statusFieldset = $form->addFieldset(
            'aw_eventbooking_status',
            array('legend' => $this->__('Event Booking'))
        );

        $statusFieldset->addField('aw_eventbooking_is_enabled', 'select', array(
            'name' => 'event[is_enabled]',
            'title' => $this->__('Enable Event Booking'),
            'label' => $this->__('Enable Event Booking'),
            'values' => Mage::getModel('aw_eventbooking/source_yesno')->toOptionArray(),
        ));

        $generalFieldset = $form->addFieldset(
            'aw_eventbooking_general',
            array('legend' => $this->__('General Settings'))
        );

        $generalFieldset->addField('aw_eventbooking_event_start_date', 'date', array(
            'name' => 'event[event_start_date]',
            'title' => $this->__('Event Start Date & Time'),
            'label' => $this->__('Event Start Date & Time'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
            'format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'locale' => Mage::app()->getLocale()->getLocaleCode(),
            'time' => true,
            'required' => true,
        ));

        $generalFieldset->addField('aw_eventbooking_event_end_date', 'date', array(
            'name' => 'event[event_end_date]',
            'title' => $this->__('Event End Date & Time'),
            'label' => $this->__('Event End Date & Time'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
            'format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'locale' => Mage::app()->getLocale()->getLocaleCode(),
            'time' => true,
        ));

        $generalFieldset->addField('aw_eventbooking_day_count_before_send_reminder_letter', 'text', array(
            'name' => 'event[day_count_before_send_reminder_letter]',
            'title' => $this->__('Send Reminder Message Before, days'),
            'label' => $this->__('Send Reminder Message Before, days'),
            'class' => 'validate-number validate-zero-or-greater',
            'note' => $this->__('Leave empty to disable reminder message sending'),
        ));

        $generalFieldset->addField('aw_eventbooking_reminder_template_id', 'select', array(
            'name' => 'event[event_data][value][reminder_template_id]',
            'title' => $this->__('Email Template for Reminder Message'),
            'label' => $this->__('Email Template for Reminder Message'),
            'values' => Mage::getModel('aw_eventbooking/source_email_template')->toOptionArray(true),
        ));

        $generalFieldset->addField('aw_eventbooking_confirmation_template_id', 'select', array(
            'name' => 'event[event_data][value][confirmation_template_id]',
            'title' => $this->__('Email Template for Confirmation Message'),
            'label' => $this->__('Email Template for Confirmation Message'),
            'values' => Mage::getModel('aw_eventbooking/source_email_template')->toOptionArray(true),
        ));

        $generalFieldset->addField('aw_eventbooking_generate_pdf_tickets', 'select', array(
            'name' => 'event[generate_pdf_tickets]',
            'title' => $this->__('Generate PDF Tickets'),
            'label' => $this->__('Generate PDF Tickets'),
            'values' => Mage::getModel('aw_eventbooking/source_yesno')->toOptionArray(),
        ));

        $generalFieldset->addField('aw_eventbooking_location', 'textarea', array(
            'name' => 'event[location]',
            'title' => $this->__('Event Location'),
            'label' => $this->__('Event Location'),
            'note' => $this->__('HTML Allowed'),
        ));

        $generalFieldset->addField('aw_eventbooking_personalization_enabled', 'select', array(
            'name' => 'event[event_data][value][personalization_enabled]',
            'title' => $this->__('Event Requires Ticket Personalization'),
            'label' => $this->__('Event Requires Ticket Personalization'),
            'values' => Mage::getModel('aw_eventbooking/source_yesno')->toOptionArray(),
        ));

        $generalFieldset->addField('aw_eventbooking_personalization_display_email', 'select', array(
            'name' => 'event[event_data][value][personalization_display_email]',
            'title' => $this->__('Display Email Field'),
            'label' => $this->__('Display Email Field'),
            'values' => Mage::getModel('aw_eventbooking/source_yesno')->toOptionArray(),
        ));

        $form->getElement('aw_eventbooking_reminder_template_id')->setRenderer(
            $this->getLayout()->createBlock('aw_eventbooking/adminhtml_catalog_product_edit_tab_eventbooking_element')
        );

        $ticketSettingsFieldset = $form->addFieldset(
            'aw_eventbooking_ticket_settings',
            array(
                'legend' => $this->__('Event Tickets'),
                'class' => 'fieldset-wide',
            )
        );

        $ticketSettingsFieldset->addField('aw_eventbooking_ticket_section_title', 'text', array(
            'name' => 'event[event_data][value][ticket_section_title]',
            'title' => $this->__('Title'),
            'label' => $this->__('Title'),
            'required' => true,
        ));

        $form->getElement('aw_eventbooking_ticket_section_title')->setRenderer(
            $this->getLayout()->createBlock('aw_eventbooking/adminhtml_catalog_product_edit_tab_eventbooking_element')
        );

        $ticketSettingsFieldset->addField('aw_eventbooking_event_ticket_data', 'text', array(
            'name' => 'event_ticket_data',
            'title' => $this->__('Ticket Types'),
            'label' => $this->__('Ticket Types'),
        ));

        $form->getElement('aw_eventbooking_event_ticket_data')->setRenderer(
            $this->getLayout()->createBlock('aw_eventbooking/adminhtml_catalog_product_edit_tab_eventbooking_ticket')
        );

        $termsFieldset = $form->addFieldset(
            'aw_eventbooking_terms',
            array('legend' => $this->__('Terms & Conditions'))
        );

        $termsFieldset->addField('aw_eventbooking_is_terms_enabled', 'select', array(
            'name' => 'event[is_terms_enabled]',
            'title' => $this->__('Enable Terms & Conditions'),
            'label' => $this->__('Enable Terms & Conditions'),
            'values' => Mage::getModel('aw_eventbooking/source_yesno')->toOptionArray(),
        ));

        $termsFieldset->addField('aw_eventbooking_terms_id', 'select', array(
            'name' => 'event[event_data][value][terms_id]',
            'title' => $this->__('Choose Terms & Conditions'),
            'label' => $this->__('Choose Terms & Conditions'),
            'values' => Mage::getModel('aw_eventbooking/source_checkout_agreement')->toOptionArray(true),
        ));

        $form->getElement('aw_eventbooking_terms_id')->setRenderer(
            $this->getLayout()->createBlock('aw_eventbooking/adminhtml_catalog_product_edit_tab_eventbooking_element')
        );

        $form->addFieldset(
            'aw_eventbooking_attendees',
            array(
                'legend' => $this->__('Attendees'),
                'html_content' => $this->getChildHtml('aw_eventbooking_attendees'),
            )
        );

        $form->addFieldset(
            'aw_eventbooking_summary',
            array(
                'legend' => $this->__('Summary'),
                'html_content' => $this->getChildHtml('aw_eventbooking_summary'),
            )
        );

        $this->setChild('form_after',
            $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap('aw_eventbooking_personalization_enabled', 'event[event_data][value][personalization_enabled]')
                ->addFieldMap('aw_eventbooking_personalization_display_email', 'event[personalization_display_email]')
                ->addFieldMap('aw_eventbooking_is_terms_enabled','event[is_terms_enabled]')
                ->addFieldMap('aw_eventbooking_terms_id','event[event_data][value][terms_id]')
                ->addFieldDependence('event[personalization_display_email]', 'event[event_data][value][personalization_enabled]', 1)
                ->addFieldDependence('event[event_data][value][terms_id]', 'event[is_terms_enabled]', 1)
        );

        $event = $this->_getEventByRequest();
        if (!$event || $event->isCurrentAdminCanRedeem()) {
            $redeemFieldset = $form->addFieldset('aw_eventbooking_redeem', array(
                'legend' => $this->__('Redeem Settings'),
            ));

            $redeemFieldset->addField('aw_eventbooking_redeem_roles', 'multiselect', array(
                'name' => 'event[redeem_roles]',
                'title' => $this->__('Admin roles who can redeem tickets'),
                'label' => $this->__('Admin roles who can redeem tickets'),
                'values' => $this->_getAdminRolesCollection()->toOptionArray(),
                'required' => true,
            ));

            if ($event && $event->isCurrentAdminCanRedeem()) {
                $redeemFieldset->addField('aw_eventbooking_redeem_link', 'link', array(
                    'name' => 'aw_eventbooking_redeem_link',
                    'href' => $this->getUrl('adminhtml/aweventbooking_tickets/view', array('_secure' => true)),
                    'label' => $this->__('Redeem Ticket by Code'),
                    'value' => $this->__('Open page'),
                    'target' => '_blank',
                ));
            }
        }
        return $this;
    }

    protected function _processDateTime($dateTime)
    {
        // Convert date from UTC to Store Locale
        $locale = Mage::app()->getLocale();
        $zDate = $locale->date($dateTime, Varien_Date::DATETIME_INTERNAL_FORMAT);
        return $zDate->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
    }

    public function _setFormsValues()
    {
        $event = $this->_getEventByRequest();
        $form = $this->getForm();
        $allAdminRoles = $this->_getAdminRolesCollection()->getColumnValues('role_id');
        if ($event) {
            if (!$event->getData('redeem_roles')) {
                $event->setData('redeem_roles', $allAdminRoles);
            }
            $formElementPrefix = "aw_eventbooking_";
            $data = $event->getData();

            $data['event_start_date'] = $this->_processDateTime($data['event_start_date']);
            $data['event_end_date'] = $data['event_end_date']
                ? $this->_processDateTime($data['event_end_date'])
                : null;

            // Process event attributes scope
            foreach ($event->getAttributeCollection() as $eventAttribute) {
                if ($eventAttribute->getData('store_id') != $event->getStoreId()) {
                    $data['event_data'][$event->getId()]['default'][$eventAttribute->getAttributeCode()] = true;
                    $this->getForm()->getElement($formElementPrefix . $eventAttribute->getAttributeCode())->setData('default', true);
                    $this->getForm()->getElement($formElementPrefix . $eventAttribute->getAttributeCode())->setData('disabled', true);
                }
            }

            // Process ticket and ticket attributes
            $data['event_ticket_data'] = array();
            foreach ($event->getTicketCollection() as $ticket) {
                $data['event_ticket_data'][$ticket->getId()] = $ticket->getData();
                foreach ($ticket->setStoreId($event->getStoreId())->getAttributeCollection() as $ticketAttribute) {
                    if ($ticketAttribute->getData('store_id') != $ticket->getStoreId()) {
                        $data['event_ticket_data'][$ticket->getId()]['default'][$ticketAttribute->getAttributeCode()] = true;
                    }
                }
            }

            foreach ($data as $key => $value) {
                $data[$formElementPrefix . $key] = $value;
                unset($data[$key]);
            }
            $form->addValues($data);
        } else {
            /** @var AW_Eventbooking_Helper_Config $configHelper */
            $configHelper = Mage::helper('aw_eventbooking/config');
            $form->addValues(array(
                'aw_eventbooking_redeem_roles' => $allAdminRoles,
                'aw_eventbooking_reminder_template_id' => $configHelper->getTemplatesReminder(),
            ));
        }
        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    public function getTabLabel()
    {
        return $this->__('Event Booking');
    }

    public function getTabTitle()
    {
        return $this->__('Event Booking');
    }

    /**
     * Check can show this tab by ACL and available product types
     *
     * @return bool
     */
    public function canShowTab()
    {
        if (Mage::getSingleton('admin/session')->isAllowed('admin/catalog/aw_eventbooking/product_tab')
            && Mage::registry('product')->getId()
        ) {
            $productType = Mage::registry('product')->getTypeId();
            return in_array($productType, $this->_getAvailableMagentoProductTypes());
        }
        return false;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _getAvailableMagentoProductTypes()
    {
        $types = array(
            Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
            Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL,
            Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE,
        );
        return $types;
    }

    /**
     * @return AW_Eventbooking_Model_Event|null
     */
    protected function _getEventByRequest()
    {
        if (is_null($this->_eventByRequst)) {
            $productId = Mage::app()->getRequest()->getParam('id', null);
            $storeId = Mage::app()->getRequest()->getParam('store', null);
            /** @var AW_Eventbooking_Model_Event $event */
            $event = Mage::getModel('aw_eventbooking/event');
            if (!is_null($storeId)) {
                $event->setStoreId($storeId);
            }
            $event->loadByProductId($productId);
            if ($event->getId()) {
                $this->_eventByRequst = $event;
            }
        }
        return $this->_eventByRequst;
    }
}
