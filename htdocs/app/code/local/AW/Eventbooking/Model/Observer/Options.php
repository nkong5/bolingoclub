<?php

class AW_Eventbooking_Model_Observer_Options
{
    /**
     * Observer for add our options to product entity after load
     * @param Varien_Object $observer
     *
     * @return $this
     */
    public function catalogProductLoadAfter($observer)
    {
        /* @var Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();
        /**
         * check of load product in admin/catalog/product/edit
         * @see Mage_Adminhtml_Catalog_ProductController->_initProduct
         */
        if ($product->getData('_edit_mode')) {
            return $this;
        }
        $this->_addOptionsToProduct($product);
        return $this;
    }

    /**
     * Observer for add our options to product items after load collection
     * in Mage_Sales_Model_Resource_Quote_Item_Collection->_assignProducts
     * @param $observer
     *
     * @return $this
     */
    public function salesQuoteItemCollectionProductsAfterLoad($observer)
    {
        $productCollection = $observer->getEvent()->getProductCollection();
        foreach ($productCollection as $item) {
            $this->_addOptionsToProduct($item);
        }
        return $this;
    }

    /**
     * Observer for add our options to product items after load collection
     * in Mage_Wishlist_Model_Resource_Item_Collection->_assignProducts
     * @param $observer
     *
     * @return $this
     */
    public function wishlistItemCollectionProductsAfterLoad($observer)
    {
        $productCollection = $observer->getEvent()->getProductCollection();
        foreach ($productCollection as $item) {
            $this->_addOptionsToProduct($item);
        }
        return $this;
    }

    /**
     * Add event options to products when creating order in backend
     * @issue EBK-132
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function productCollectionLoadAfter($observer)
    {
        $productCollection = $observer->getEvent()->getCollection();
        foreach ($productCollection as $item) {
            $this->_addOptionsToProduct($item);
        }
        return $this;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     */
    protected function _addOptionsToProduct($product)
    {
        /**@var AW_Eventbooking_Model_Event $event */
        $event = Mage::getModel('aw_eventbooking/event')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->loadByProductId($product->getId())
        ;
        if (
            is_null($event->getId())
            || !$event->getData('is_enabled')
            || Mage::app()->getStore()->isAdmin()
        ) {
            return;
        }
        $optionType = Mage_Catalog_Model_Product_Option::OPTION_TYPE_FIELD;
        $productHelper = Mage::helper('aw_eventbooking/product');

        $ticketCollection = $event->getTicketCollection();
        $ticketCollection->setOrder('CAST(sort_order AS SIGNED)', Varien_Data_Collection::SORT_ORDER_ASC);//sort as INT

        $preconfiguredValues = $product->getPreconfiguredValues();
        if (!$options = $preconfiguredValues->getData('options')) {
            $options = array();
        }
        foreach ($ticketCollection as $ticket) {
            $productHelper->addProductFieldOptions(
                $product,
                $optionType,
                AW_Eventbooking_Model_Event::PRODUCT_OPTION_ID . '_' . $ticket->getId(),
                $ticket
            );
            $options[AW_Eventbooking_Model_Event::PRODUCT_OPTION_ID . '_' . $ticket->getId()] = 0;
        }

        $preconfiguredValues->setData('options', $options);
        $product->setPreconfiguredValues($preconfiguredValues);
        $product->setHasOptions(true);
        $product->setRequiredOptions(true);
    }
}