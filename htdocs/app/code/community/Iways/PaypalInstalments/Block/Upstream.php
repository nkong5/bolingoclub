<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com and you will be sent a copy immediately.
 *
 * Created on 02.03.2015
 * Author Robert Hillebrand - hillebrand@i-ways.de - i-ways sales solutions GmbH
 * Copyright i-ways sales solutions GmbH © 2015. All Rights Reserved.
 * License http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */
/**
 * Iways PaypalInstalments Review Block
 *
 * @category   Iways
 * @package    Iways_PaypalInstalments
 * @author robert
 */
class Iways_PaypalInstalments_Block_Upstream extends Mage_Payment_Block_Form
{
    /**
     * @var array
     */
    protected $financeInformation = array();
    protected $pageType;

    /**
     * __construct
     */
    protected function _construct()
    {
        parent::_construct();
        if (parent::getTemplate() === null) {
            $this->setPageType('payment');
            $this->setTemplate('iways_paypalinstalments/upstream/upstream.phtml');
        }
    }

    /**
     * Get finance information for the current price
     *
     * @return mixed
     */
    public function getFinanceInformation()
    {
        $amount = $this->getCurrentPrice();
        if(!$amount) return false;
        if (!isset($this->financeInformation[$amount])) {
            $this->financeInformation[$amount] = Mage::getModel('iways_paypalinstalments/api_rest')->getFinanceInfo($amount);
        }
        return $this->financeInformation[$amount];
    }

    /**
     * Get qualifying financing options
     *
     * @return mixed
     */
    public function getQualifyingFinancingOptions()
    {
        $financeInformation = $this->getFinanceInformation();
        if (isset($financeInformation->financing_options[0]->qualifying_financing_options)) {
            $qualifyingOptions = $financeInformation->financing_options[0]->qualifying_financing_options;
            usort($qualifyingOptions, function($A, $B) {
                if($A->monthly_payment->value == $B->monthly_payment->value) {
                    return 0;
                }
                return $A->monthly_payment->value > $B->monthly_payment->value ? -1 : 1;
            });
            return $qualifyingOptions;
        }
        return false;
    }

    /**
     * Get the current total price, dependent on the current page type
     *
     * @return float
     */
    public function getCurrentPrice()
    {
        if($this->pageType === 'product') {
            return Mage::registry('current_product')->getFinalPrice();
        } elseif($this->pageType === 'cart' || $this->pageType === 'payment') {
            return Mage::helper('checkout')->getQuote()->getGrandTotal();
        } else {
            return false;
        }
    }

    /**
     * Get the current page type
     *
     * @return mixed
     */
    public function getPageType()
    {
        return $this->pageType;
    }

    /**
     * Get the expression word for the current page type
     *
     * @return string
     */
    public function getPageTypeExpression()
    {
        if($this->pageType === 'product') {
            return "Artikel";
        } elseif($this->pageType === 'cart' || $this->pageType === 'payment') {
            return "Warenkorb";
        }
    }

    /**
     * Determine whether the current price is financeable
     *
     * @return bool
     */
    public function isFinanceable()
    {
        return $this->getCurrentPrice() >= 99;
    }

    public function isCalculatedValueVisible()
    {
        return Mage::getStoreConfig('payment/iways_paypalinstalments/value_on_first_page');
    }

    /**
     * Determine whether the current page may display the upstream info or not,
     * depending on the set page type
     *
     * @return bool
     */
    public function isUpstreamVisible()
    {
        if($this->pageType === 'payment') {
            return Mage::getStoreConfig('payment/iways_paypalinstalments/upstream_payment_method');
        } elseif ($this->pageType === 'cart') {
            return Mage::getStoreConfig('payment/iways_paypalinstalments/upstream_cart');
        } elseif ($this->pageType === 'product') {
            return Mage::getStoreConfig('payment/iways_paypalinstalments/upstream_product');
        }
        return false;
    }

    /**
     * Set the page type
     *
     * @param $type: 'product' or 'cart' or 'payment'
     */
    public function setPageType($type)
    {
        $this->pageType = $type;
    }
}