<?php
/**
 * Magento / Mediotype Module
 *
 *
 * @desc
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_IndexController
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     *
     */
    public function indexAction()
    {
        if (!$this->_getCustomerSession()->isLoggedIn()) {
            $this->_getCustomerSession()->addNotice('You must be logged in to view your tickets page');
            $this->_redirect('customer/account/login');
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /**
     *
     */
    public function emailAction()
    {
        if ($this->getRequest()->getParam('event')) {
            try {
                //need to get ticket ids
                $customer = Mage::getSingleton('customer/session')->getCustomer();

                /** @var $emailModel  Mediotype_Ticket_Model_Email */
                $emailModel = Mage::getModel('mediotype_ticket/email');
                $emailModel->sendEmail($this->getRequest()->getParam('event'), $customer);

                $this->_getCustomerSession()->addSuccess('Email with ticket information sent successfully');
                $this->_redirectReferer();
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getCustomerSession()->addError('An error occurred during the email process');
                $this->_redirectReferer();
            }
        } else {
            $this->_getCustomerSession()->addError('An error occurred during the email process');
            $this->_redirectReferer();
        }

    }

    /**
     *
     */
    public function printAction()
    {
        if (!$this->_getCustomerSession()->isLoggedIn()) {
            $this->_getCustomerSession()->addNotice('You must be logged in to print your tickets');
            $this->_redirect('customer/account/login');
            $this->_getCustomerSession()->setBeforeAuthUrl($this->getRequest()->getOriginalPathInfo());
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     *
     */
    public function redeemAction()
    {

        // Load the requested ticket order model
        $id = $this->getRequest()->getParam('id');

        // Create scan log record
        $scanRecordModel = Mage::getModel("mediotype_ticket/scanrecord");
        $scanRecordModel->setData("date_scanned", Mage::getSingleton("core/date")->timestamp());
        $scanRecordModel->setData("ticket_id", $id);

        /** @var $model Mediotype_Ticket_Model_Order */
        $model = Mage::getModel('mediotype_ticket/order')->load($id);

        // Check if admin user is logged in
        $user = $this->_getAdminUser();
        if ($user->validate() === true) {
            // Increment admin_scancount by 1
            if ($model->getId()) {
                try {
                    $model->setData('admin_scancount', ((int)$model->getData('admin_scancount')) + 1);
                    $model->save();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            // Set scan log data
            $scanRecordModel->setData("scanned_by", $user->getFirstname() . " " . $user->getLastname());
            $scanRecordModel->setData("user_type", Mediotype_Ticket_Model_Scanrecord::USER_TYPE_ADMIN);
            $scanRecordModel->save();

            //TODO this should work when not using secure url for admin via system config
            // Redirect request to admin controller
            $params = $this->getRequest()->getParams();
            $params['_secure'] = true;
            $params['_nosecret'] = true;
            $params['redeem'] = true;
            $url = Mage::helper('adminhtml')->getUrl('mediotype_ticket/adminhtml_index/index', $params);
            $this->getResponse()->setRedirect($url);
            return;
        }

        // If we hit this point in the code, the current user is NOT an admin
        if (!$model->getId()) {
            $this->_redirect('ticket');
            return;
        }

        // Increment user_scancount by 1
        try {
            $model->setData('user_scancount', ((int)$model->getData('user_scancount')) + 1);
            $model->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        // Set scan log data
        if ($this->_getCustomerSession()->isLoggedIn()) {
            $customer = $this->_getCustomerSession()->getCustomer();
            $scanRecordModel->setData("scanned_by", $customer->getFirstname() . " " . $customer->getLastname());
            $scanRecordModel->setData("user_type", Mediotype_Ticket_Model_Scanrecord::USER_TYPE_USER);

            $scanRecordModel->save();
            return;
        } else {
            // Redirect user to
            $scanRecordModel->setData("user_type", Mediotype_Ticket_Model_Scanrecord::USER_TYPE_GUEST);
            $product = $model->getProduct();
            $this->getResponse()->setRedirect($product->getProductUrl());
        }
        $scanRecordModel->save();
    }

    /**
     *
     */
    public function qrimageAction()
    {
        require_once("phpqrcode/qrlib.php");
        $ticketOrder = Mage::getModel('mediotype_ticket/order')->load($this->getRequest()->getParam('id'));
        $this->getResponse()->setHeader("Content-Type", "image/png");
        ob_start();
        QRcode::png($ticketOrder->getRedeemUrl());
        $code = ob_get_clean();
        $this->getResponse()->setBody($code);
    }

    /**
     * @desc generates passbook contents, and sends to device
     */
    public function passbookAction()
    {
        // todo write file to downloadable resource available to account holder who is signed in
        // verify ticket belongs to customer
        if (!$this->_getCustomerSession()->isLoggedIn()) {
            $this->_getCustomerSession()->addNotice('You must be logged in to send your tickets to Passbook');
            $this->_redirect('customer/account/login');
        }

        /** @var $purchasedTickets Mediotype_Ticket_Model_Resource_Order_Collection */
        $purchasedTickets = Mage::getModel('mediotype_ticket/order')->getCollection();
        $purchasedTickets->addFieldToFilter('customer_id', array('eq' => $this->_getCustomerSession()->getCustomerId()));
        $purchasedTickets->addFieldToFilter('sku', array('eq' => $this->getRequest()->getParam('event')));
        $purchasedTickets->addFieldToFilter('ticket_available', true);
        $purchasedTickets->load();

        $files = array();
        foreach ($purchasedTickets as $ticketOrder) {

            /** @var $passbookFile Mediotype_Ticket_Model_Passbook */
            $passbookFile = Mage::getModel('mediotype_ticket/passbook');
            $passbookFile->setPurchasedData($ticketOrder);
            $fileName = $passbookFile->createPassbook();
            $fileDownloadName = basename($fileName);
            $files[] = $fileDownloadName;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($files));
    }

    /**
     *
     */
    public function downloadpassAction()
    {
        $filename = $this->getRequest()->getParam("filename");

        if (!$filename
        ) {
            $url = Mage::getUrl('ticket');
            $this->getResponse()->setRedirect($url);
            return;
        }

        $pathTmp = Mage::getBaseDir('var') . '/export/';
        $content = array(
            'type' => 'filename',
            'value' => $pathTmp . $filename,
            'rm' => true // can delete file after use
        );

        $fileDownloadName = basename($filename);
        $this->_prepareDownloadPassbook($fileDownloadName, $content);
    }

    /**
     * @param string $fileName
     * @param string $content
     * @param string $contentType
     * @param null $contentLength
     * @return Mediotype_Ticket_IndexController
     */
    protected function _prepareDownloadPassbook($fileName, $content, $contentType = 'application/vnd.apple.pkpass', $contentLength = null)
    {
        $isFile = false;
        $file = null;
        if (is_array($content)) {
            if (!isset($content['type']) || !isset($content['value'])) {
                return $this;
            }
            if ($content['type'] == 'filename') {
                $isFile = true;
                $file = $content['value'];
                $contentLength = filesize($file);
            }
        }

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'no-cache', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength, true)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"', true)
            ->setHeader('Last-Modified', date('r'), true);

        if (!is_null($content)) {
            if ($isFile) {
                $this->getResponse()->clearBody();
                $this->getResponse()->sendHeaders();

                $ioAdapter = new Varien_Io_File();
                $ioAdapter->open(array('path' => $ioAdapter->dirname($file)));
                $ioAdapter->streamOpen($file, 'r');
                while ($buffer = $ioAdapter->streamRead()) {
                    print $buffer;
                }
                $ioAdapter->streamClose();
                if (!empty($content['rm'])) {
                    $ioAdapter->rm($file);
                }
            } else {
                $this->getResponse()->setBody($content);
            }
        }

        return $this;
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * @return Mage_Admin_Model_Session
     */
    protected function _getAdminSession()
    {
        return Mage::getSingleton('admin/session');
    }

    /**
     * @return Mage_Admin_Model_User
     */
    protected function _getAdminUser()
    {
        $mediotypeCookieId = Mage::getModel('core/cookie')->get('mediotype_ticket_reference');
        $sessionSaveConfig = Mage::getConfig()->getNode('global/session_save');

        /** @var $user Mage_Admin_Model_User */
        $user = Mage::getModel('admin/user');

        if($sessionSaveConfig == 'db'){
            $dbSession = Mage::getResourceModel('core/session');
            $grabDbSession = $dbSession->read($mediotypeCookieId);
            $oldSession = $_SESSION;
            session_decode($grabDbSession);
            $adminSession = $_SESSION;
            $_SESSION = $oldSession;
            if (array_key_exists('user', $adminSession['admin'])) {
                $user = $adminSession['admin']['user'];
            }
        } else {
            $sessionSavePath = Mage::getModel('core/session')->getSessionSavePath();
            $sessionFilePath = $sessionSavePath . DS . 'sess_' . $mediotypeCookieId;
            $sessionFile = file_get_contents($sessionFilePath);
            $oldSession = $_SESSION;
            session_decode($sessionFile);
            $adminSession = $_SESSION;
            $_SESSION = $oldSession;
            if (array_key_exists('user', $adminSession['admin'])) {
                $user = $adminSession['admin']['user'];
            }
        }

        return $user;
    }

}
