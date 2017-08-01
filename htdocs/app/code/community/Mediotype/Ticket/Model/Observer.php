<?php
/**
 * Magento / Mediotype Module
 *
 *
 * @desc
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Model_Observer
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Model_Observer
{

    public function checkoutAllowGuest($observer){

        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $observer->getQuote();
        $result = $observer->getResult();

        foreach($quote->getAllItems() as $item){
            /** @var $item Mage_Sales_Model_Quote_Item */
            $product = $item->getProduct();
            $product->load($product->getId());
            if ($product->getTypeId() == Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET) {
                $result->setIsAllowed(false);
            }
        }

    }

    public function processTicketsByOrderStatus(Varien_Event_Observer $observer)
    {

        $order = $observer->getDataObject();

        if (!$order instanceof Mage_Sales_Model_Order) {
            return;
        }

        $orderStatus = $order->getStatus();

        $configuredAvailableStatus = Mage::getStoreConfig('mediotype_ticket/ticket_email/ticket_is_available_on');
        $configuredEmailStatus = Mage::getStoreConfig('mediotype_ticket/ticket_email/send_on_checkout');
        $configuredCanceledStatus = Mage::getStoreConfig('mediotype_ticket/ticket_email/canceled_status');
        $configuredStockAvailibilty = Mage::getStoreConfig('mediotype_ticket/ticket_email/stock_update_on_available');

        $ticketOrderModels = Mage::getModel('mediotype_ticket/order')->getCollection()->addFieldToFilter(
            'order_id',
            $order->getId()
        );

        $emailsToSend = array();

        if ($ticketOrderModels->count() > 0) {

            foreach ($ticketOrderModels->getItems() as $ticket) {

                if (strtolower($orderStatus) == strtolower($configuredAvailableStatus)) {
                    /** @var $ticket = Mediotype_Ticket_Model_Order */
                    if ((int)$ticket->getTicketAvailable() == 0) {
                        $ticket->setTicketAvailable(1);
                        $ticket->save();
                    }
                    //check if stock needs to change
                    if($configuredStockAvailibilty) {
                        $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($ticket->getProduct());
                        $stock->subtractQty(1);
                        $stock->save();
                    }
                }

                if (strtolower($orderStatus) == strtolower($configuredCanceledStatus)) {
                    /** @var $ticket = Mediotype_Ticket_Model_Order */
                    if ((int)$ticket->getTicketAvailable() == 1) {
                        $ticket->setTicketAvailable(0);
                        $ticket->save();
                    }
                }

                // SEND EMAILS IF ORDER STATUS IS SAME AS CONFIGURED
                if (strtolower($orderStatus) == strtolower($configuredEmailStatus)
                    && (int)$ticket->getData('email_sent') == 0) {
                    $emailsToSend[$ticket->getData('sku')] = true;
                }

            }

            foreach ($emailsToSend as $sku => $value) {
                $customerId = $order->getCustomerId();
                $customer = Mage::getModel('customer/customer')->load($customerId);
                /** @var $emailModel  Mediotype_Ticket_Model_Email */
                $emailModel = Mage::getModel('mediotype_ticket/email');
                $emailModel->sendEmail($sku, $customer);
            }

        }

    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function generateTickets(Varien_Event_Observer $observer)
    {

        $orders = $observer->getOrders();
        if (!is_array($orders)) {
            $orders = array();
            $orders[] = $observer->getOrder();
        }

        foreach ($orders as $index => $order) {
            $orderStatus = $order->getStatus();

            /** @var $order Mage_Sales_Model_Order */
            foreach ($order->getAllItems() as $_item) {
                /** @var $_item Mage_Sales_Model_Order_Item */

                if ($_item->getProductType() == Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET) {

                    // CREATE ONE TICKET ORDER MODEL FOR EACH TICKET PURCHASED
                    for ($newTicketCount = 0; $newTicketCount < $_item->getQtyOrdered(); $newTicketCount++) {
                        /** @var $ticketOrderModel Mediotype_Ticket_Model_Order */
                        $ticketOrderModel = Mage::getModel('mediotype_ticket/order');
                        $data = array();
                        $data['sku'] = $_item->getProduct()->getData('sku');
                        $data['customer_id'] = $order->getCustomerId();
                        $data['order_id'] = $order->getId();
                        $data['date_created'] =$order->getCreatedAt();

                        $data['ticket_available'] = (strtolower($orderStatus) == strtolower(
                                Mage::getStoreConfig('mediotype_ticket/ticket_email/ticket_is_available_on')
                            ));

                        $ticketOrderModel->setData($data)->save();
                    }
                }
            }
        }

    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function validateBuyer(Varien_Event_Observer $observer)
    {

        /** @var $_cart Mage_Checkout_Model_Cart */
        $_cart = $observer->getCart();
        foreach ($_cart->getItems() as $_id => $_item) {
            /** @var $_item Mage_Sales_Model_Quote_Item */
            /** @var $product Mage_Catalog_Model_Product */
            $product = $_item->getProduct();
            $product->load($product->getId());

            /** @var $response Zend_Controller_Response_Http */
            $response = $observer->getEvent()->getResponse();

            if ($product->getTypeId() == Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET) {

                $allowGuestCartAdd = (bool)Mage::getStoreConfig(
                    'mediotype_ticket/ticket_email/allow_guest_add_to_cart'
                );

                if (!$allowGuestCartAdd) {
                    if (!$this->_getCustomerSession()->isLoggedIn()) {
                        $_cart->removeItem($_id);
                        $this->_getCheckoutSession()->getMessages(true);
                        $this->_getCheckoutSession()->addError("You must be logged in to purchase a ticket.");
                        $_cart->getQuote()->setHasError(true);
                    }
                }

                if ($product->getTypeInstance()->hasOccurred()) {
                    $_cart->removeItem($_id);
                    $this->_getCheckoutSession()->getMessages(true);
                    $this->_getCheckoutSession()->addError(
                        "You can not purchase a ticket for an event that has already occurred."
                    );
                    $_cart->getQuote()->setHasError(true);
                }
            }
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function addInputRenderToAdminForm(Varien_Event_Observer $observer)
    {
        $addedInputTypes = $observer->getResponse();
        $addNewTypes = array();
        foreach ($addedInputTypes->getTypes() as $typeName => $typeClass) {
            $addNewTypes[$typeName] = $typeClass;
        }
        $addNewTypes['passbook'] = Mage::getConfig()->getBlockClassName(
            'mediotype_ticket/adminhtml_catalog_product_helper_form_passbook'
        );
        $addedInputTypes->setTypes($addNewTypes);
    }

    /**
     * Removes products with the type of Simple Ticket from the cart
     */
    protected function _removeTickets()
    {
        /** @var $cart Mage_Checkout_Model_Cart */
        $cart = Mage::getSingleton('checkout/cart');
        foreach ($cart->getItems() as $id => $item) {
            /** @var $item Mage_Sales_Model_Quote_Item */
            if ($item->getProductType() == Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET) {
                $cart->removeItem($id);
            }
        }
        $cart->getQuote()->setHasError(true);
        $cart->save();
    }

    /**
     * Get customer session model instance
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Identify referer url via all accepted methods (HTTP_REFERER, regular or base64-encoded request param)
     *
     * @param Zend_Controller_Request_Http $request
     *
     * @return string
     */
    protected function _getRefererUrl($request)
    {
        $refererUrl = $request->getServer('HTTP_REFERER');
        if ($url = $request->getParam(self::PARAM_NAME_REFERER_URL)) {
            $refererUrl = $url;
        }
        if ($url = $request->getParam(self::PARAM_NAME_BASE64_URL)) {
            $refererUrl = Mage::helper('core')->urlDecode($url);
        }
        if ($url = $request->getParam(self::PARAM_NAME_URL_ENCODED)) {
            $refererUrl = Mage::helper('core')->urlDecode($url);
        }

        if (!$this->_isUrlInternal($refererUrl)) {
            $refererUrl = Mage::app()->getStore()->getBaseUrl();
        }
        return $refererUrl;
    }

    /**
     * Check url to be used as internal
     *
     * @param   string $url
     *
     * @return  bool
     */
    protected function _isUrlInternal($url)
    {
        if (strpos($url, 'http') !== false) {
            /**
             * Url must start from base secure or base unsecure url
             */
            if ((strpos($url, Mage::app()->getStore()->getBaseUrl()) === 0)
                || (strpos($url, Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true)) === 0)
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Reference for scanning tickets from a credentialed admin user
     *
     * @param $observer
     */
    public function setMediotypeSessionId($observer)
    {

        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton('admin/session');
        $sessionId = $session->getSessionId();

        $cookies = Mage::getModel('core/cookie');
        $cookies->set('mediotype_ticket_reference', $sessionId, time() + 3600); //set value for 1 hour

    }

}
