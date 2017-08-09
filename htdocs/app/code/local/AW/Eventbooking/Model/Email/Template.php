<?php

if (Mage::helper('aw_eventbooking')->isCustomSMTPInstalled()) {
    class AW_Eventbooking_Model_Email_TemplateCommon extends AW_Customsmtp_Model_Email_Template
    {
    }
} else {
    class AW_Eventbooking_Model_Email_TemplateCommon extends Mage_Core_Model_Email_Template
    {
    }
}

class AW_Eventbooking_Model_Email_Template extends AW_Eventbooking_Model_Email_TemplateCommon
{
    public function addAttachment($name, $content)
    {
        /** @var Zend_Mime_Part $attach */
        $attach = $this->getMail()->createAttachment($content);
        $attach->filename = $name;
        return $this;
    }
}
