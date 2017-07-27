<?php

/**
 * This file is part of the simplesurance GmbH checkout plugin for Magento.
 *
 * @link      https://simplesurance.de
 * @copyright (C) simplesurance GmbH
 */

/**
 * Helper class. Retrieves the required data from the shop system.
 * Will be called from templates. Also provides the event observer for
 * the category export.
 */
class Sisu_Checkout_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Javascript source
     *
     * @const string
     */
    const JAVASCRIPT_SOURCE = 'www.schutzklick.de/jsapi/sisu-checkout-2.x.min.js';

    /**
     * Returns the contents of the shopping cart as json
     *
     * @return string
     */
    public function getCartItems()
    {
        /** @var Mage_Sales_Model_Quote $quote */
        /** @var Mage_Sales_Model_Quote_Item $item */

        $products  = array();
        $quote     = Mage::getSingleton('checkout/session')->getQuote();
        $cartItems = $quote->getAllVisibleItems();
        $currency  = $this->getCurrency();
        $country   = strtolower($quote->getShippingAddress()->getCountryId());

        foreach ($cartItems as $item) {
            $mageProduct = Mage::getModel('catalog/product')->load($item->getProductId());

            $product = array(
                'id'       => $item->getProductId(),
                'name'     => $mageProduct->getName(),
                'price'    => $item->getOriginalPrice(),
                'currency' => $currency,
                'sku'      => $mageProduct->getSku(),
                'qty'      => $item->getQty(),
            );

            $product['categories'] = $this->getProductCategories($mageProduct);
            $products[]            = $product;
        }

        return array(
            count($products) > 0 ? json_encode($products) : json_encode(null),
            empty($country) ? Mage::getStoreConfig('general/country/default') : $country
        );
    }

    /**
     * Returns the last order id and the customer data as json
     *
     * @return string
     */
    public function getLastOrderDetails()
    {
        /** @var Mage_Sales_Model_Order_Address $address */
        $order    = $this->getOrder();
        $address  = $order->getBillingAddress();
        $country  = strtolower($address->getCountryId());

        $customerData = array(
            'firstname'     => $address->getFirstname(),
            'lastname'      => $address->getLastname(),
            'email'         => $this->getCustomerEmail($order),
            'street'        => $address->getStreetFull(),
            'street_number' => '',
            'zip'           => $address->getPostcode(),
            'city'          => $address->getCity(),
            'country'       => $country,
            'phone'         => $address->getTelephone(),
        );

        return array($this->getOrderId(), json_encode($customerData), $country);
    }

    /**
     * Returns partner_id value from shop config
     *
     * @return mixed
     */
    public function getPartnerId()
    {
        return Mage::getStoreConfig('sisu_checkout/settings/partner_id');
    }

    /**
     * Returns shop_id value from shop config
     *
     * @return mixed
     */
    public function getShopId()
    {
        return Mage::getStoreConfig('sisu_checkout/settings/shop_id');
    }

    /**
     * Returns the JavaScript source to include
     *
     * @return string
     */
    public function getJavascriptSource()
    {
        return self::JAVASCRIPT_SOURCE;
    }

    /**
     * Hooks into Magento's event system right before the response
     * will be sent. If GET['schutzklickCategoryExport'] is present and
     * the category export has been allowed in backend the page will
     * output JSON and die().
     *
     * @return boolean
     */
    public function controllerFrontSendResponseBefore()
    {
        $allow = Mage::getStoreConfig(
            'sisu_checkout/settings/allow_category_export'
        );
        if (isset($_GET['schutzklickCategoryExport']) && $allow) {
            $export = array();
            foreach ($this->getStoreCategories() as $category) {
                $export []= array(
                    'id' => $category->getId(),
                    'parent_id' => $category->getParentId(),
                    'name' => $category->getName()
                );
            }

            // send the json - and only the json
            Mage::app()->getResponse()
                ->setHeader('Content-Type', 'application/json')
                ->setBody(json_encode($export))
                ->sendResponse();
            die();
        }

        return true;
    }

    /**
     * Gets the category tree recursively. (Unlike the Mage helper function
     * with the same name.)
     *
     * @param integer $parentId defaults to null
     * @param boolean $isChild defaults to false
     *
     * @return array
     */
    protected function getStoreCategories($parentId = null, $isChild = false) {

        if (is_null($parentId)) {
            $parentId = Mage::app()->getStore()->getRootCategoryId();
        }

        $allCats = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('is_active','1')
                ->addAttributeToFilter('include_in_menu','1')
                ->addAttributeToFilter('parent_id',array('eq' => $parentId))
                ->addAttributeToSort('position', 'asc');

        $cats = array();
        foreach ($allCats as $category) {
            $subcats = $category->getChildren();
            $cats []= $category;
            if ($subcats) {
                $cats = array_merge($cats, $this->getStoreCategories($category->getId(), true));
            }
        }
        return $cats;
    }

    /**
     * @param Mage_Catalog_Model_Product $mageProduct
     * @return array
     */
    protected function getProductCategories($mageProduct)
    {
        /** @var Mage_Catalog_Model_Resource_Category_Collection $mageProductCategories */

        $categories            = array();
        $mageProductCategories = $mageProduct->getCategoryCollection();
        $mageProductCategories->addAttributeToSelect('name');

        foreach ($mageProductCategories as $c) {
            $categories[] = array($c->getId() => $c->getName());
        }

        return $categories;
    }

    /**
     * Returns completed order object
     *
     * @return Mage_Sales_Model_Order
     */
    protected function getOrder()
    {
        return Mage::getModel('sales/order')->loadByIncrementId($this->getOrderId());
    }

    /**
     * Returns completed order id
     *
     * @return string
     */
    protected function getOrderId()
    {
        return Mage::getSingleton('checkout/session')->getLastRealOrderId();
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return string Email address
     */
    protected function getCustomerEmail($order)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        $quote    = Mage::getModel('sales/quote')->load($order->getQuoteId());

        return $this->coalesce(
            $order->getCustomerEmail(),
            $quote->getCustomerEmail(),
            $customer->getEmail()
        );
    }

    /**
     * Returns shop's active currency
     *
     * @return string
     */
    protected function getCurrency()
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Takes multiple arguments and the returns the first of them which
     * evaluates to true or returns NULL if none of them evaluates to true.
     *
     * @param mixed $param1
     *        ...
     * @param mixed $paramN
     *
     * @return mixed
     */
    protected function coalesce(/* ... */)
    {
        $args = func_get_args();
        foreach ($args as $arg) {
            if ($arg) {
                return $arg;
            }
        }
        return null;
    }
}
