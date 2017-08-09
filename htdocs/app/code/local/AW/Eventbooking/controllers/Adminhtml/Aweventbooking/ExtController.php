<?php

class AW_Eventbooking_Adminhtml_Aweventbooking_ExtController extends Mage_Adminhtml_Controller_Action
{
    protected $_publicActions = array('view', 'undoRedeem');

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/aw_eventbooking');
    }

    protected function _getTicket()
    {
        /** @var AW_Eventbooking_Helper_Ticket $ticketHelper */
        $ticketHelper = Mage::helper('aw_eventbooking/ticket');
        return $ticketHelper->getTicketByExternalRequest();
    }

    public function preDispatch()
    {
        parent::preDispatch();
        /** @var Mage_Admin_Model_Session $adminSession */
        $adminSession = Mage::getSingleton('admin/session');
        if (!$adminSession->isLoggedIn()) {
            return $this->_redirectFrontend();
        }
    }

    protected function _redirectFrontend($permissionsFail = false)
    {
        if ($ticket = $this->_getTicket()) {
            /* We have a valid ticket */
            $url = Mage::app()->getSafeStore($ticket->getStoreId())->getUrl('aw_eventbooking/ext/view', array(
                '_secure' => true,
                'code' => $ticket->getCode(),
                'hash' => $ticket->getControlHash(),
                'p' => $permissionsFail,
                'v' => 1
            ));
        } else {
            /* Bad ticket info, redirect to default store view */
            $url = Mage::app()->getDefaultStoreView()->getUrl('');
        }
        return $this->_redirectUrl($url);
    }

    protected function viewPermission($ticket, $session)
    {
        if (Mage::app()->getRequest()->getParam('code') && !$ticket) {
            $session->addError($this->__('Code not found'));
            return $ticket;
        }
        if (!$session->getNotRedeem() && $ticket) {
            if ($ticket->getRedeemed()) {
                $session->addError($this->__('Ticket already redeemed'));
                return $ticket;
            }

            if ($ticket->isPaid()) {
                $ticket->setRedeem();
            } else {
                $session->addError($this->__('Ticket not paid'));
            }

        }
        return $ticket;
    }

    public function viewAction()
    {
        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton('admin/session');
        if (!$ticket = $this->_getTicket()) {
            $session->addError($this->__('Ticket not found'));
            $this->_redirect('adminhtml/aweventbooking_tickets/list/');
        }
        if (!$ticket->getEvent()->isCurrentAdminCanRedeem()) {
            return $this->_redirectFrontend(true);
        }
        $ticket = $this->viewPermission($ticket, $session);
        $session->setNotRedeem(null);

        $this->loadLayout();
        $layout = $this->getLayout();
        $messages = $session->getMessages(true);
        $layout->getMessagesBlock()->setMessages($messages);
        $block = $layout->getBlock('ticket.view');

        $block->setData('ticket', $ticket);
        $block->setData('searchForm', false);
        $block->setData('success', (bool)!$messages->count());
        $block->setData('failed', (bool)$messages->count());
        $this->renderLayout();

    }

    public function undoRedeemAction()
    {
        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton('admin/session');

        if (!$ticket = $this->_getTicket()) {
            $session->addError($this->__('Ticket not found'));
            return $this->_redirect('adminhtml/aweventbooking_tickets/list');

        }
        if (!$ticket->getEvent()->isCurrentAdminCanRedeem()) {
            Mage::getSingleton('customer/session')
                ->addError($this->__('Sorry, you are not allowed to redeem tickets for this event.'));
            return $this->_redirectFrontend();
        }
        $ticket->undoRedeem();
        $session->setNotRedeem(true);
        return $this->_redirect('adminhtml/aweventbooking_ext/view', array(
            '_secure' => true,
            'code' => $ticket->getCode(),
            'hash' => $ticket->getControlHash()));

    }
}
