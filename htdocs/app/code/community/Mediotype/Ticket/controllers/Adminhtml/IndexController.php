<?php
/**
 * Magento / Mediotype Module
 *
 *
 * @desc
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Adminhtml_IndexController
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{

    protected $_ticket = null;

    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = array('index');

    /**
     *
     */
    public function indexAction()
    {
        $model = $this->_getTicket();

        if (!$model->getId()) {
            $this->_getSession()->addError("Ticket # " . $this->getRequest()->getParam('id') . " does not exist.");
            $this->_redirect('adminhtml/dashboard');
            return;
        }

        Mage::register("current_order", $model->getOrder());

        $this->loadLayout();
        $headline = "";
        if (!$model->hasBeenRedeemed()) {
            if ($this->getRequest()->getParam('redeem')) {
                $this->_redeemTicket($model);
                $this->_getSession()->addSuccess("Ticket Successfully Redeemed");
                $headline = "Ticket Successfully Redeemed";
            }
        } else {
            $this->_getSession()->addNotice("<h3>Ticket was redeemed by " . $model->getData('redeemed_by') . " @ " . $model->getData('date_redeemed') . "</h3>");
            $headline = "Ticket Was Already Redeemed";
        }

        if ($model->hasBeenRevoked()) {
            $this->_getSession()->addError("Ticket revoked by " . $model->getData('revoked_by') . " @ " . $model->getData('date_revoked'));
        }

        $block = $this->getLayout()
            ->createBlock('core/text', 'status-block')
            ->setText('<h1>' . $headline . '</h1>');

        $this->getLayout()->getBlock('content')->insert($block);

//        $this->_addContent($block);


        $this->renderLayout();
    }

    /**
     *
     */
    public function revokeAction()
    {
        $model = $this->_getTicket();

        $model->setData('date_revoked', Varien_Date::now());
        /** @var $user Mage_Admin_Model_User */
        $user = $this->_getAdminSession()->getUser();
        $model->setData('revoked_by', $user->getFirstname() . ' ' . $user->getLastname());
        $model->save();

        $this->_redirect("*/*/index", $this->getRequest()->getParams());
    }

    /**
     *
     */
    public function reinstateAction()
    {
        $model = $this->_getTicket();

        $model->setData('date_revoked', NULL);
        $model->setData('revoked_by', NULL);
        $model->save();
        $this->_getSession()->addSuccess("Ticket Successfully Reinstated");

        $this->_redirect("*/*/index", $this->getRequest()->getParams());
    }

    /**
     * @param $model Mediotype_Ticket_Model_Order
     */
    protected function _redeemTicket($model)
    {
        $model->setData('date_redeemed', Varien_Date::now());
        /** @var $user Mage_Admin_Model_User */
        $user = $this->_getAdminSession()->getUser();
        $model->setData('redeemed_by', $user->getFirstname() . ' ' . $user->getLastname());
        $model->save();
    }

    /**
     * @return Mage_Admin_Model_Session
     */
    protected function _getAdminSession()
    {
        return Mage::getSingleton('admin/session');
    }

    /**
     * @return Mediotype_Ticket_Model_Order
     */
    protected function _getTicket()
    {
        $id = $this->getRequest()->getParam('id');
        return Mage::getModel('mediotype_ticket/order')->load($id);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
        /** @var $helper Mediotype_Core_Helper_Data */
        $helper = Mage::helper("mediotype_core");

        $moduleName = $this->getRequest()->getModuleName();
        $controllerName = $this->getRequest()->getControllerName();
        $actionName = $helper->explodeCamelCase($this->getRequest()->getActionName());

        $aclPath = "$moduleName/$controllerName/" . implode("/", $actionName);
        Mage::log($aclPath, null, "acl.log");
        return Mage::getSingleton('admin/session')
            ->isAllowed($aclPath);
    }

}
