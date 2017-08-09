<?php
class AW_Eventbooking_ExtController extends Mage_Core_Controller_Front_Action
{
    public function viewAction()
    {
        if (Mage::app()->getRequest()->getParam('v')) {
            if ($ticket = Mage::helper('aw_eventbooking/ticket')->getTicketByExternalRequest()) {

                $session = Mage::getSingleton('customer/session');

                if (Mage::app()->getRequest()->getParam('p')) {
                    $session->addError($this->__('Sorry, you are not allowed to redeem tickets for this event.'));
                }

                if ($session->isLoggedIn() && $session->getCustomerId() == $ticket->getData('customer_id')) {
                    return $this->_redirect('aw_eventbooking/ticket/view', array('_secure' => true, 'id' => $ticket->getId()));
                }

                $customer = Mage::getSingleton('customer/customer')->load($ticket->getData('customer_id'));

                $this->loadLayout();
                $layout = $this->getLayout();
                $block = $layout->getBlock('ticket.view');
                $messages = $session->getMessages(true);
                $layout->getMessagesBlock()->setMessages($messages);

                $block->setData('ticket', $ticket);
                $block->setData('customer', $customer);
                $block->setData('FullInfo', false);
                $this->renderLayout();

            } else {
                /* Bad ticket info */
                $this->_redirect('');
            }
        } else {
            $this->_redirectBackend();
        }

    }

    protected function _redirectBackend($permissionsFail = false)
    {
        if ($ticket = $this->_getTicket()) {
            /* We have a valid ticket */
            $url = Mage::helper('adminhtml')->getUrl('adminhtml/aweventbooking_ext/view', array(
                '_secure' => true,
                'code' => $ticket->getCode(),
                'hash' => $ticket->getControlHash(),
                'p' => $permissionsFail
            ));
        } else {
            /* Bad ticket info, redirect to default store view */
            $url = Mage::app()->getDefaultStoreView()->getUrl('');
        }
        return $this->_redirectUrl($url);
    }

    protected function _getTicket()
    {
        /** @var AW_Eventbooking_Helper_Ticket $ticketHelper */
        $ticketHelper = Mage::helper('aw_eventbooking/ticket');
        return $ticketHelper->getTicketByExternalRequest();
    }
}
