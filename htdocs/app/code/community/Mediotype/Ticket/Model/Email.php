<?php
/**
 * Magento / Mediotype Module
 *
 *
 * @desc
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Model_Email
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Model_Email extends Mage_Core_Model_Abstract
{
    /**
     * @param string $event_sku
     * @param Mage_Customer_Model_Customer $customer
     */
    public function sendEmail($event_sku, $customer)
    {
        $purchasedTickets = Mage::getModel('mediotype_ticket/order')->getCollection()
            ->addFieldToFilter('customer_id', array('eq' => $customer->getId()))
            ->addFieldToFilter('sku', array('eq' => $event_sku))
            ->addFieldToFilter('ticket_available', true)
            ->load();

        $event = Mage::helper('mediotype_ticket')->loadTicketBySku($event_sku);
        $storeId = Mage::app()->getStore()->getStoreId();

        // Get the destination email addresses to send copies to
        $data = Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_COPY_TO, $storeId);
        $copyTo = (!empty($data)) ? explode(',', $data) : '';
        $copyMethod = Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_COPY_METHOD, $storeId);

        $templateId = Mage::getStoreConfig('mediotype_ticket/ticket_email/template', $storeId);

        $mailer = Mage::getModel('mediotype_ticket/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($customer->getEmail(), $customer->getName());

        if ($copyTo && $copyMethod == 'bcc') {
            // Add bcc to customer email
            foreach ($copyTo as $email) {
                $emailInfo->addBcc($email);
            }
        }

        $mailer->addEmailInfo($emailInfo);
        //var_dump($customer->getData());
        // Email copies are sent as separated emails if their copy method is 'copy'
        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig('sales_email/order_comment/identity', $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(
            array(
                'event' => $event,
                'otickets' => $purchasedTickets
            )
        );
        $mailer->send();

        foreach ($purchasedTickets as $ticketOrderModel) {
            $ticketOrderModel->setData('email_sent', 1)->save();
        }
    }
}