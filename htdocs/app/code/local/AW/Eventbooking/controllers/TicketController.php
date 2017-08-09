<?php

class AW_Eventbooking_TicketController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::helper('customer')->isLoggedIn()) {
            $this->_redirect('customer/account', array('_secure' => true));
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('aw_eventbooking')->__('My Tickets'));
        $layout = $this->getLayout();
        $layout->getMessagesBlock()->setMessages(Mage::getSingleton('customer/session')->getMessages(true));
        $this->renderLayout();
    }

    /**
     * @param $id
     * @return AW_Eventbooking_Model_Ticket
     * @throws Exception
     */
    protected function _loadTicket($id)
    {
        $session = Mage::getSingleton('customer/session');
        $collection = Mage::getModel('aw_eventbooking/ticket')->getCollection();
        $collection
            ->joinEventData()
            ->joinOrderData()
            ->addFieldToFilter('id', $id);

        $model = $collection->getFirstItem();

        if (!$model->getId()) {
            throw new Exception($this->__('Id not found'));
        }
        $customer = $session->getCustomer();
        if ($customer->getId() != $model->getData('customer_id')) {
            throw new Exception($this->__('Wrong customer ID'));
        }
        return $model;
    }

    public function viewAction()
    {
        $session = Mage::getSingleton('customer/session');
        try {
            if (!$id = Mage::app()->getRequest()->getParam('id')) {
                throw new Exception($this->__('No valid ID'));
            }
            $ticketModel = $this->_loadTicket($id);
        } catch (Exception $e) {
            $session->addError($e->getMessage());
            return $this->_redirect('aw_eventbooking/ticket',array('_secure' =>true));
        }
        $this->loadLayout();
        $layout = $this->getLayout();
        $this
            ->_title(Mage::helper('aw_eventbooking')->__('My Tickets'))
            ->_title($ticketModel->getEventName());
        $layout->getMessagesBlock()->setMessages($session->getMessages(true));
        $block = $layout->getBlock('ticket.view');

        $block->setData('ticket', $ticketModel);
        $block->setData('customer', $session->getCustomer());
        $block->setData('FullInfo', true);
        $this->renderLayout();
    }

    public function resendConfirmationAction()
    {
        $session = Mage::getSingleton('customer/session');
        try {
            if (!$id = Mage::app()->getRequest()->getParam('id')) {
                throw new Exception($this->__('No valid id'));
            }
            $model = $this->_loadTicket($id);
            if (!$orderItem = $model->getOrderItem()) {
                throw new Exception('Order item not found');
            }

            try {
                Mage::helper('aw_eventbooking/mailer')->sendConfirmationEmailForOrderItem($orderItem);
            } catch (Exception $e) {
                Mage::logException($e);
            }
            $session->addSuccess('Done');
            $this->_redirect('aw_eventbooking/ticket/view', array('_secure' =>true,'id' => $id));

        } catch (Exception $e) {
            $session->addError($e->getMessage());
            $this->_redirect('aw_eventbooking/ticket/index',array('_secure'=>true));
        }
    }
}
