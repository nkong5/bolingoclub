<?php

/* Set default templates */
function aweb_setTemplateConfig($configPath, $templateCode)
{
    /** @var AW_Eventbooking_Helper_Config $configHelper */
    $configHelper = Mage::helper('aw_eventbooking/config');
    if (!$configHelper->getConfig($configPath)) {
        /** @var Mage_Core_Model_Email_Template $emailTemplate */
        $emailTemplate = Mage::getModel('core/email_template')->loadByCode($templateCode);
        if ($emailTemplate->getId()) {
            $configHelper->setConfig($configPath, $emailTemplate->getId());
        }
    }
}

/* Add templates to Transaction Emails */
function aweb_addTemplates($templates)
{
    foreach ($templates as $templateData) {
        $modelTemplate = Mage::getModel('adminhtml/email_template')->loadByCode($templateData['template_code']);
        if ($modelTemplate->getId()) {
            continue;
        }
        $modelTemplate
            ->setData($templateData)
            ->save();
    }
}

return array(
    array(
        'template_code' => '[aW Event Ticket] Ticket Confirmation [New]',
        'template_subject' => '{{var store.getFrontendName()}}: Ticket Confirmation',
        'template_text' => '<p>Hello, {{var customer_name}}!</p>
<p>Thank you for purchasing the ticket for event "<b>{{var order_item.name}}</b>". </p>
<p>The Event starts on <b>{{var event_start_date}}</b>.</p>
{{depend event_end_date}}
<p>The Event ends on <b>{{var event_end_date}}</b>.</p>
{{/depend}}
{{depend event.location}}
<p>
Event Location: <br />
<b>{{var event.location}}.</b>
</p>
{{/depend}}
<p>Your order id is <b>{{var order.increment_id}}</b>.</p>
<p>Your Ticket Type: <b>{{var event_ticket.title}}</b>.</p>
<p>Number of Tickets: <b>{{var order_item.qty_ordered}}</b>.</p>',
        'template_type' => Mage_Core_Model_Email_Template::TYPE_HTML,
    ),
    array(
        'template_code' => '[aW Event Ticket] Event Reminder [New]',
        'template_subject' => '{{var store.getFrontendName()}}: Event Reminder',
        'template_text' => '<p>Hello, <b>{{var customer_name}}</b>!</p>
<p>We remind you that event "<b>{{var order_item.name}}</b>" starts in <b>{{var event.day_count_before_send_reminder_letter}}</b> days<p>
<p>The Event starts on <b>{{var event_start_date}}</b>.
{{depend event_end_date}}
<br />
The Event ends on <b>{{var event_end_date}}</b>.
{{/depend}}
</p>
{{depend event.location}}
<p>
Event Location: <br />
<b>{{var event.location}}.</b>
</p>
{{/depend}}',
        'template_type' => Mage_Core_Model_Email_Template::TYPE_HTML,
    )
);
