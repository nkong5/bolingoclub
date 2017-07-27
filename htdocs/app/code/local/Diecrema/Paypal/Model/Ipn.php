<?php

/**
 *
 *
 * @copyright triplesense
 * @author Edgar Bongkishiy <e.bongkishiy@triplesense.de>
 * @version 0.1
 * @package Vorwerk_Paypal
 *
 * last author: $Author: ebongkishiy $
 *
 */

/**
 * Custom PayPal Instant Payment Notification processor model extending
 */
class Diecrema_Paypal_Model_Ipn extends Mage_Paypal_Model_Ipn
{

    /**
     * Register recurring payment notification, create and process order
     */
    protected function _registerRecurringProfilePaymentCapture()
    {
        $price = $this->getRequestData('mc_gross') - $this->getRequestData('tax') -  $this->getRequestData('shipping');
        $productItemInfo = new Varien_Object;
        if ($this->getRequestData('period_type') == 'Trial') {
            $productItemInfo->setPaymentType(Mage_Sales_Model_Recurring_Profile::PAYMENT_TYPE_TRIAL);
        } elseif ($this->getRequestData('period_type') == 'Regular') {
            $productItemInfo->setPaymentType(Mage_Sales_Model_Recurring_Profile::PAYMENT_TYPE_REGULAR);
        }
        $productItemInfo->setTaxAmount($this->getRequestData('tax'));
        $productItemInfo->setShippingAmount($this->getRequestData('shipping'));
        $productItemInfo->setPrice($price);

        $order = $this->_recurringProfile->createOrder($productItemInfo);

        $payment = $order->getPayment();
        $payment->setTransactionId($this->getRequestData('txn_id'))
            ->setPreparedMessage($this->_createIpnComment(''))
            ->setIsTransactionClosed(0);
        $order->save();
        $this->_recurringProfile->addOrderRelation($order->getId());
        $payment->registerCaptureNotification($this->getRequestData('mc_gross'));
        $order->save();

        // Custom change

        // notify customer
//        if ($invoice = $payment->getCreatedInvoice()) {
//            $message = Mage::helper('paypal')->__('Notified customer about invoice #%s.', $invoice->getIncrementId());
//            $comment = $order->sendNewOrderEmail()->addStatusHistoryComment($message)
//                ->setIsCustomerNotified(true)
//                ->save();
//        }
    }

    /**
     * Process completed payment (either full or partial)
     */
    protected function _registerPaymentCapture()
    {
        if ($this->getRequestData('transaction_entity') == 'auth') {
            return;
        }
        $this->_importPaymentInformation();
        $payment = $this->_order->getPayment();
        $payment->setTransactionId($this->getRequestData('txn_id'))
            ->setPreparedMessage($this->_createIpnComment(''))
            ->setParentTransactionId($this->getRequestData('parent_txn_id'))
            ->setShouldCloseParentTransaction('Completed' === $this->getRequestData('auth_status'))
            ->setIsTransactionClosed(0)
            ->registerCaptureNotification($this->getRequestData('mc_gross'));
        $this->_order->save();

        // Custom change

        // notify customer
//        if ($invoice = $payment->getCreatedInvoice() && !$this->_order->getEmailSent()) {
//            $comment = $this->_order->sendNewOrderEmail()->addStatusHistoryComment(
//                    Mage::helper('paypal')->__('Notified customer about invoice #%s.', $invoice->getIncrementId())
//                )
//                ->setIsCustomerNotified(true)
//                ->save();
//        }
    }


    /**
     * Register authorized payment
     */
    protected function _registerPaymentAuthorization()
    {
        $this->_importPaymentInformation();

        $this->_order->getPayment()
            ->setPreparedMessage($this->_createIpnComment(''))
            ->setTransactionId($this->getRequestData('txn_id'))
            ->setParentTransactionId($this->getRequestData('parent_txn_id'))
            ->setIsTransactionClosed(0)
            ->registerAuthorizationNotification($this->getRequestData('mc_gross'));

          // Custom change: No email should be sent to customer

//        if (!$this->_order->getEmailSent()) {
//            $this->_order->sendNewOrderEmail();
//        }

        $this->_order->save();
    }

}
