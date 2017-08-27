 <?php

class AW_Eventbooking_Model_Observer
{
    /**
     * Call before "catalog/product/edit" action dispatching in adminhtml
     * @event controller_action_predispatch_adminhtml_catalog_product_edit
     * @see Mage_Adminhtml_Catalog_ProductController
     * @see Mage_Adminhtml_Controller_Action->preDispatch
     * @see Mage_Core_Controller_Varien_Action->preDispatch
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function catalogProductEditPredispatch($observer)
    {
        Mage::getModel('aw_eventbooking/observer_event')->catalogProductEditPredispatch($observer);
        return $this;
    }

    /**
     * Call before product save
     * @event catalog_product_save_before
     * @see Mage_Catalog_Model_Product->_beforeSave
     * @see Mage_Core_Model_Abstract->_beforeSave
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function catalogProductSaveBefore($observer)
    {
        Mage::getModel('aw_eventbooking/observer_event')->saveEvent($observer);
        return $this;
    }

    /**
     * Call after product save
     * @event catalog_product_save_after
     * @see Mage_Catalog_Model_Product->_afterSave
     * @see Mage_Core_Model_Abstract->_afterSave
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function catalogProductSaveAfter($observer)
    {
        Mage::getModel('aw_eventbooking/observer_event')->duplicateProduct($observer);
        return $this;
    }

    /**
     * Call after product load
     * @event catalog_product_load_after
     * @see Mage_Catalog_Model_Product->_afterLoad
     * @see Mage_Core_Model_Abstract->_afterLoad
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function catalogProductLoadAfter($observer)
    {
        Mage::getModel('aw_eventbooking/observer_options')->catalogProductLoadAfter($observer);
        return $this;
    }

    /**
     * Call after load product collection for quote items
     * @event sales_quote_item_collection_products_after_load
     * @see Mage_Sales_Model_Mysql4_Quote_Item_Collection->_assignProducts
     * @see Mage_Sales_Model_Resource_Quote_Item_Collection->_assignProducts
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function salesQuoteItemCollectionProductsAfterLoad($observer)
    {
        Mage::getModel('aw_eventbooking/observer_options')->salesQuoteItemCollectionProductsAfterLoad($observer);
        return $this;
    }

    /**
     * Call before add product to cart
     * @event catalog_product_type_prepare_cart_options
     * @see Mage_Catalog_Model_Product_Type_Abstract->_prepareOptionsForCart
     *
     * @event catalog_product_type_prepare_full_options
     * @see Mage_Catalog_Model_Product_Type_Abstract->_prepareOptions
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function catalogProductTypePrepareFullOptions($observer)
    {
        Mage::getModel('aw_eventbooking/observer_event')->validateOnAddToCart($observer);
        return $this;
    }

    /**
     * Call before update items in checkout/cart
     * @event checkout_cart_update_items_before
     * @see Mage_Checkout_Model_Cart->updateItems
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function checkoutCartUpdateItemsBefore($observer)
    {
        Mage::getModel('aw_eventbooking/observer_event')->validateOnUpdateItemsInCart($observer);
        return $this;
    }

    /**
     * Call before place order
     * @event sales_model_service_quote_submit_before
     * @see Mage_Sales_Model_Service_Quote->submitOrder
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function salesModelServiceQuoteSubmitBefore($observer)
    {
        Mage::getModel('aw_eventbooking/observer_event')->validateOnPlaceOrder($observer);
        return $this;
    }

    /**
     * Calls after multishipping checkout submits all created orders
     * @event checkout_submit_all_after
     * @see Mage_Checkout_Model_Type_Multishipping->createOrders
     *
     * @param $observer
     * @return $this
     */
    public function checkoutSubmitMultipleOrders($observer)
    {
        $orders = $observer->getOrders();
        if (is_array($orders) || ($orders instanceof Traversable)) {
            foreach ($orders as $order) {
                Mage::getModel('aw_eventbooking/observer_history')->addOrderToHistory($order);
            }
        }
        return $this;
    }

    /**
     * Call after place order
     * @event sales_model_service_quote_submit_after
     * @see Mage_Sales_Model_Service_Quote->submitOrder
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function salesModelServiceQuoteSubmitAfter($observer)
    {
        Mage::getModel('aw_eventbooking/observer_history')->addOrderToHistory($observer->getEvent()->getOrder());
        return $this;
    }

    /**
     * Call before block render (toHtml)
     * @event core_block_abstract_to_html_before
     * @see Mage_Core_Block_Abstract->toHtml
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function coreBlockAbstractToHtmlBefore($observer)
    {
        Mage::getModel('aw_eventbooking/observer_agreement')->addEventAgreementsToAgreementsBlock($observer);
        return $this;
    }

    /**
     * Call before "checkout/onepage/saveOrder" action dispatching in frontend
     * @event controller_action_predispatch_checkout_onepage_saveOrder
     * @see Mage_Checkout_OnepageController
     * @see Mage_Core_Controller_Front_Action->preDispatch
     * @see Mage_Core_Controller_Varien_Action->preDispatch
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function checkoutOnepageSaveOrderPredispatch($observer)
    {
        Mage::getModel('aw_eventbooking/observer_agreement')->checkoutOnepageSaveOrderPredispatch($observer);
        return $this;
    }

    /**
     * Call before "checkout/multishipping/overviewPost" action dispatching in frontend
     * @event controller_action_predispatch_checkout_multishipping_overviewPost
     * @see Mage_Checkout_MultishippingController
     * @see Mage_Core_Controller_Front_Action->preDispatch
     * @see Mage_Core_Controller_Varien_Action->preDispatch
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function checkoutMultishippingOverviewPostPredispatch($observer)
    {
        Mage::getModel('aw_eventbooking/observer_agreement')->checkoutMultishippingOverviewPostPredispatch($observer);
        return $this;
    }

    /**
     * Call before "onestepcheckout/ajax/placeOrder" action dispatching in frontend
     * @event controller_action_predispatch_onestepcheckout_ajax_placeOrder
     * @see AW_Onestepcheckout_AjaxController
     * @see Mage_Core_Controller_Front_Action->preDispatch
     * @see Mage_Core_Controller_Varien_Action->preDispatch
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function onestepcheckoutAjaxPlaceOrderPredispatch($observer)
    {
        Mage::getModel('aw_eventbooking/observer_agreement')->onestepcheckoutAjaxPlaceOrderPredispatch($observer);
        return $this;
    }

    /**
     * Call after load product collection for wishlist items
     * @event wishlist_item_collection_products_after_load
     * @see Mage_Wishlist_Model_Resource_Item_Collection->_assignProducts
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function wishlistItemCollectionProductsAfterLoad($observer)
    {
        Mage::getModel('aw_eventbooking/observer_options')->wishlistItemCollectionProductsAfterLoad($observer);
        return $this;
    }

    /**
     * Call after invoice pay
     * @event sales_order_invoice_pay
     * @see Mage_Sales_Model_Order_Invoice->pay
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function salesOrderInvoicePay($observer)
    {
        $invoice = $observer->getInvoice();
        if ($invoice) {
            $order = $invoice->getOrder();
            if ($order) {
                Mage::getModel('aw_eventbooking/observer_history')->addOrderToHistory($order);
                Mage::getModel('aw_eventbooking/ticket')->markTicketsAsPaid($invoice);
                Mage::getModel('aw_eventbooking/observer_event')->sendConfirmationEmail($order);
                Mage::getModel('aw_eventbooking/observer_event')->sendReminderAfterInvoice($order);
            }
        }
        return $this;
    }
	
	
	 public function salesOrderPaymentPay($observer)
	{
		 /**
		 * @var $invoice Mage_Sales_Model_Order_Invoice
		 * @var $paymentMethod Mage_Payment_Model_Method_Abstract
		 */
		$invoice       = $observer->getEvent()->getInvoice();
		$paymentMethod = $observer->getEvent()->getPayment()->getMethodInstance();
		if ($paymentMethod->getCode() == Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS && !$invoice->getEmailSent()){
			$invoice->sendEmail(TRUE);
		}
	}
	
	public function checkoutSaveOrder(Varien_Event_Observer $observer)
	{
		$order = $observer->getEvent()->getOrder();
		if($order){
			Mage::getModel('aw_eventbooking/observer_history')->addOrderToHistory($order);
			Mage::getModel('aw_eventbooking/observer_event')->sendConfirmationEmail($order);
			Mage::getModel('aw_eventbooking/observer_event')->sendReminderAfterInvoice($order);
		}
		return $this;
	}

    /**
     * Replace back url in catalog\product\edit
     * @event controller_action_layout_render_before_adminhtml_catalog_product_edit
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function controllerActionLayoutRenderBeforeAdminhtmlCatalogProductEdit($observer)
    {

        $request = Mage::app()->getRequest();
        if (!$backUrl = $request->getParam('awBackUrl')) {
            return $this;
        }

        $layout = Mage::getSingleton('core/layout');
        if (!$block = $layout->getBlock('product_edit')) {
            return $this;
        }
        if ($backButton = $block->getChild('back_button')) {
            $backButton->setData('onclick', 'setLocation(\'' . $backButton->getUrl(base64_decode($backUrl)) . '\')');
        }

        return $this;
    }

    /**
     * Call after product collection is loaded
     * @see Mage_Catalog_Model_Resource_Product_Collection_afterLoad
     * @issue EBK-132
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function productCollectionLoadAfter($observer)
    {
        if ('sales_order_create' === Mage::app()->getRequest()->getControllerName()) {
            Mage::getModel('aw_eventbooking/observer_options')->productCollectionLoadAfter($observer);
        }
        return $this;
    }
}
