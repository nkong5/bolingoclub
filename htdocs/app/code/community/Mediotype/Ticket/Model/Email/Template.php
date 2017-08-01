<?php
/**
 * Class Mediotype_Ticket_Model_Email_Template
 * This file is responsible for creating and adding the attachments
 */
class Mediotype_Ticket_Model_Email_Template extends Mage_Core_Model_Email_Template
{
    /**
     * Send transactional email to recipient
     *
     * @param   int $templateId
     * @param   string|array $sender sneder informatio, can be declared as part of config path
     * @param   string $email recipient email
     * @param   string $name recipient name
     * @param   array $vars varianles which can be used in template
     * @param   int|null $storeId
     * @return  Mage_Core_Model_Email_Template
     */
    public function sendTransactional($templateId, $sender, $email, $name, $vars=array(), $storeId=null)
    {
        $this->setSentSuccess(false);
        if (($storeId === null) && $this->getDesignConfig()->getStore()) {
            $storeId = $this->getDesignConfig()->getStore();
        }

        if (is_numeric($templateId)) {
            $this->load($templateId);
        } else {
            $localeCode = Mage::getStoreConfig('general/locale/code', $storeId);
            $this->loadDefault($templateId, $localeCode);
        }

        if (!$this->getId()) {
            throw Mage::exception('Mage_Core', Mage::helper('core')->__('Invalid transactional email code: %s', $templateId));
        }

        if (!is_array($sender)) {
            $this->setSenderName(Mage::getStoreConfig('trans_email/ident_' . $sender . '/name', $storeId));
            $this->setSenderEmail(Mage::getStoreConfig('trans_email/ident_' . $sender . '/email', $storeId));
        } else {
            $this->setSenderName($sender['name']);
            $this->setSenderEmail($sender['email']);
        }

        if((bool)Mage::getStoreConfig('mediotype_ticket/apple_passbook_settings/passbook_enabled')) {
            $files = $this->getPassbookAttachments($vars['otickets']);
            foreach($files as $file) {
                $fileContents = file_get_contents($file['filePath']);
                $this->getMail()->createAttachment($fileContents,
                    Zend_Mime::TYPE_OCTETSTREAM,
                    Zend_Mime::DISPOSITION_ATTACHMENT,
                    Zend_Mime::ENCODING_BASE64,
                    $file['fileName']);
            }
        }
        if (!isset($vars['store'])) {
            $vars['store'] = Mage::app()->getStore($storeId);
        }
        $this->setSentSuccess($this->send($email, $name, $vars));
        return $this;
    }

    /**
     * @param Mediotype_Ticket_Model_Resource_Order_Collection $collection
     * @return array
     */
    protected function getPassbookAttachments(Mediotype_Ticket_Model_Resource_Order_Collection $collection)
    {
        $files = array();
        foreach ($collection as $ticketOrder) {
            if($ticketOrder->getProduct()->getPassbookEnabled()) {
                /** @var $passbookFile Mediotype_Ticket_Model_Passbook */
                $passbookFile = Mage::getModel('mediotype_ticket/passbook');
                $passbookFile->setPurchasedData($ticketOrder);
                $fileName = $passbookFile->createPassbook();
                $fileDownloadName = basename($fileName);
                $files[] = array(
                    'filePath' => $fileName,
                    'fileName' => $fileDownloadName
                );
            }
        }
        return $files;
    }
}