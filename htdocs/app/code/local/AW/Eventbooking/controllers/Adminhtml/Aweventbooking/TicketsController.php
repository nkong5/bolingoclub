<?php

require_once 'AbstractController.php';

class AW_Eventbooking_Adminhtml_Aweventbooking_TicketsController extends AW_Eventbooking_Adminhtml_Aweventbooking_AbstractController
{
    public function listAction()
    {
        $this->_initAction(array('Event Tickets', 'Manage Tickets'))
            ->renderLayout();
    }

    public function ajaxgridAction()
    {
        $this->_initAction()->renderLayout();
    }

    public function ajaxchangeAction()
    {
        $ticketId = $this->getRequest()->getParam('ticketid');
        $field = $this->getRequest()->getParam('field');
        $newValue = $this->getRequest()->getParam('newvalue');
        /** @var AW_Eventbooking_Model_Ticket $ticket */
        $ticket = Mage::getModel('aw_eventbooking/ticket')->load($ticketId);
        if (!$ticket->getId() || is_null($field) || is_null($newValue)) {
            return;
        }
        if (strcmp('redeemed', $field) === 0) {
            $event = $ticket->getEvent();
            if (!$event->isCurrentAdminCanRedeem()) {
                return;
            }
        }

        $ticket->setData($field, $newValue);
        $ticket->save();
    }

    protected function _addSessionMessageByCount($count)
    {
        switch ($count) {
            case 0:
                $this->_getSession()->addWarning($this->__('Tickets were not updated'));
                break;
            case 1:
                $this->_getSession()->addSuccess($this->__('1 ticket has been successfully updated'));
                break;
            default:
                $this->_getSession()->addSuccess($this->__('%d tickets has been successfully updated', $count));
                break;
        }
        return $this;
    }

    public function masspaymentstatusAction()
    {
        $ticketIds = $this->getRequest()->getParam('ticket');
        $newPaymentStatus = (int)$this->getRequest()->getParam('status');
        $allowedPaymentStatuses = array(
            AW_Eventbooking_Model_Ticket::PAYMENT_STATUS_PAID,
            AW_Eventbooking_Model_Ticket::PAYMENT_STATUS_REFUNDED,
        );
        if (!$ticketIds || !in_array($newPaymentStatus, $allowedPaymentStatuses)) {
            return $this->_redirectReferer();
        }

        /** @var AW_Eventbooking_Model_Resource_Ticket_Collection $ticketsCollection */
        $ticketsCollection = Mage::getModel('aw_eventbooking/ticket')->getCollection();
        $ticketsCollection
            ->addFieldToFilter('id', array('in' => $ticketIds))
            ->addFieldToFilter(
                'payment_status',
                array('in' => array_diff($allowedPaymentStatuses, array($newPaymentStatus)))
            );

        $ticketsCount = $ticketsCollection->getSize();
        foreach ($ticketsCollection as $ticket) {
            /** @var AW_Eventbooking_Model_Ticket $ticket */
            $ticket->setData('payment_status', $newPaymentStatus);
            $ticket->save();
        }

        return $this
            ->_addSessionMessageByCount($ticketsCount)
            ->_redirectReferer();
    }

    public function massredeemAction()
    {
        $ticketIds = $this->getRequest()->getParam('ticket');
        $redeemedStatus = (int)$this->getRequest()->getParam('status');
        /** @var AW_Eventbooking_Model_Source_Ticket_Redeem $redeemStatuses */
        $redeemStatuses = Mage::getModel('aw_eventbooking/source_ticket_redeem');

        if (!$ticketIds || !$redeemStatuses->getOption($redeemedStatus)) {
            return $this->_redirectReferer();
        }
        /** @var Magento_Db_Adapter_Pdo_Mysql $dbConnection */
        $dbConnection = Mage::getSingleton('core/resource')->getConnection('aw_eventbooking_write');
        $rowCount = $dbConnection->update(
            Mage::getSingleton('core/resource')->getTableName('aw_eventbooking/ticket'),
            array('redeemed' => $redeemedStatus),
            new Zend_Db_Expr(
                '(id IN (' . implode(',', $ticketIds) . '))
                    AND (redeemed != ' . $redeemedStatus . ')'
            )
        );
        return $this
            ->_addSessionMessageByCount($rowCount)
            ->_redirectReferer();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/aw_eventbooking/tickets');
    }


    protected function _getTicketByCode()
    {
        return Mage::helper('aw_eventbooking/ticket')->getTicketByCodeRequest();
    }

    protected function viewPermission(AW_Eventbooking_Model_Ticket $ticket, $session)
    {
        if ($ticket && !$ticket->getEvent()->isCurrentAdminCanRedeem()) {
            $session->addError($this->__('Sorry, you are not allowed to redeem tickets for this event.'));
            return null;
        }
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
        /** @var AW_Eventbooking_Model_Ticket $ticket */
        $ticket = $this->_getTicketByCode();
        if (Mage::app()->getRequest()->getParam('code') && is_null($ticket)) {
            $session->addError($this->__('Ticket not found'));
        }
        if (!is_null($ticket)) {
            $ticket = $this->viewPermission($ticket, $session);
            $session->setNotRedeem(null);
        }
        $this->loadLayout();
        $layout = $this->getLayout();
        $block = $layout->getBlock('ticket.view');
        $messages = $session->getMessages(true);
        $layout->getMessagesBlock()->setMessages($messages);

        $block->setData('ticket', $ticket);
        $block->setData('searchForm', true);

        if (Mage::app()->getRequest()->getParam('code')) {
            $block->setData('success', !$messages->count());
            $block->setData('failed', $messages->count());
        }
        $this->renderLayout();
    }

    public function undoRedeemAction()
    {
        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton('admin/session');
        /** @var AW_Eventbooking_Model_Ticket $ticket */
        if (!$ticket = $this->_getTicketByCode()) {
            $session->addError($this->__('Ticket not found'));
            return $this->_redirect('adminhtml/aweventbooking_tickets/list');

        }

        $ticket->undoRedeem();

        $session->setNotRedeem(true);
        return $this->_redirect('adminhtml/aweventbooking_tickets/view', array(
            'code' => $ticket->getCode()));

    }
}
