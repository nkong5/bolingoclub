<?php

class AW_Eventbooking_Model_Observer_Agreement
{

    /**
     * Call for add event agreements to agreements list in Mage_Checkout_Block_Agreements
     * @param Varien_Object $observer
     * @return $this
     */
    public function addEventAgreementsToAgreementsBlock($observer)
    {
        $block = $observer->getBlock();
        if ($block instanceof Mage_Checkout_Block_Agreements) {
            $agreementCollection = $block->getAgreements();
            if (
                $agreementCollection instanceof Mage_Checkout_Model_Resource_Agreement_Collection ||
                $agreementCollection instanceof Mage_Checkout_Model_Mysql4_Agreement_Collection
            ) {
                $eventAgreementIds = $this->_getEventAgreementIds();
                $whereCondition = $agreementCollection->getConnection()
                    ->quoteInto("main_table.agreement_id in (?)", $eventAgreementIds)
                ;
                $agreementCollection->getSelect()
                    ->orWhere($whereCondition)
                ;
            }
        }
        return $this;
    }

    /**
     * Check magento terms & our terms before place order from onepage checkout
     * @param $observer
     * @return $this
     */
    public function checkoutOnepageSaveOrderPredispatch($observer)
    {
        $controllerAction = $observer->getControllerAction();

        $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
        $eventAgreements = $this->_getEventAgreementIds();
        $requiredAgreements = array_unique(
            array_merge($requiredAgreements, $eventAgreements)
        );
        $postedAgreements = array_keys($controllerAction->getRequest()->getPost('agreement', array()));
        if (array_diff($requiredAgreements, $postedAgreements)) {
            $result = array(
                'success'           => false,
                'error'             => true,
                'error_messages'    => Mage::helper('checkout')->__('Please agree to all the terms and conditions before placing the order.')
            );
            $controllerAction->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            $controllerAction->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
        return $this;
    }

    /**
     * Check magento terms & our terms before place order from multishipping checkout
     * @param $observer
     * @return $this
     */
    public function checkoutMultishippingOverviewPostPredispatch($observer)
    {
        $controllerAction = $observer->getControllerAction();

        $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
        $eventAgreements = $this->_getEventAgreementIds();
        $requiredAgreements = array_unique(
            array_merge($requiredAgreements, $eventAgreements)
        );
        $postedAgreements = array_keys($controllerAction->getRequest()->getPost('agreement', array()));
        if (array_diff($requiredAgreements, $postedAgreements)) {
            Mage::getSingleton('checkout/session')->addError(
                Mage::helper('checkout')->__('Please agree to all Terms and Conditions before placing the order.')
            );
            $controllerAction->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            $controllerAction->getResponse()->setRedirect(Mage::getUrl('*/*/billing'));
        }
        return $this;
    }

    /**
     * Check magento terms & our terms before place order from onestepcheckout from AW
     * @param $observer
     * @return $this
     */
    public function onestepcheckoutAjaxPlaceOrderPredispatch($observer)
    {
        $controllerAction = $observer->getControllerAction();

        $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
        $eventAgreements = $this->_getEventAgreementIds();
        $requiredAgreements = array_unique(
            array_merge($requiredAgreements, $eventAgreements)
        );
        $postedAgreements = array_keys($this->getRequest()->getPost('aw_osc_agreement', array()));
        if (array_diff($requiredAgreements, $postedAgreements)) {
            $result = array(
                'success'   => false,
                'messages'  => array(
                    Mage::helper('aw_onestepcheckout')->__('Please agree to all the terms and conditions before placing the order.')
                )
            );
            $controllerAction->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            $controllerAction->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
        return $this;
    }

    /**
     * @return array
     */
    protected function _getEventAgreementIds()
    {
        if (!Mage::getStoreConfigFlag('checkout/options/enable_agreements')) {
            return array();
        }
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $quoteEventItems = Mage::helper('aw_eventbooking/quote')
            ->getAllEventbookingItemsFromQuote($quote)
        ;
        $quoteEventProductIds = array();
        foreach ($quoteEventItems as $item) {
            $quoteEventProductIds[] = $item->getProduct()->getId();
        }
        $eventCollection = Mage::getModel('aw_eventbooking/event')->getCollection()
            ->addIsEnabledFilter()
            ->addProductIdsFilter($quoteEventProductIds)
            ->addEventAttributes(Mage::app()->getStore()->getId())
        ;
        $agreementIds = array();
        foreach($eventCollection as $event) {
            if (!$event->getData('is_terms_enabled')) {
                continue;
            }
            if (intval($event->getData('terms_id')) === 0) {
                continue;
            }
            $agreementIds[] = $event->getData('terms_id');
        }
        return array_unique($agreementIds);
    }

}