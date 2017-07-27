<?php

class NRApps_Idealo_Model_Source_PaymentMethods
{
    const PAYMENT_METHOD_INVOICE        = 'INVOICE';
    const PAYMENT_METHOD_CREDITCARD     = 'CREDITCARD';
    const PAYMENT_METHOD_PREPAID        = 'PREPAID';
    const PAYMENT_METHOD_PAYPAL         = 'PAYPAL';
    const PAYMENT_METHOD_DEBIT          = 'DIRECTDEBIT';
    const PAYMENT_METHOD_CASHONDELIVERY = 'COD';
    const PAYMENT_METHOD_SOFORTUEBERWEISUNG = 'SOFORTUEBERWEISUNG';
    const PAYMENT_METHOD_GIROPAY        = 'GIROPAY';
    const PAYMENT_METHOD_MONEYBOOKERS   = 'MONEYBOOKERS';
    const PAYMENT_METHOD_CLICKANDBUY    = 'CLICKANDBUY';
    const PAYMENT_METHOD_PAYSAFECARD    = 'PAYSAFECARD';
    const PAYMENT_METHOD_GOOGLECHECKOUT = 'GOOGLECHECKOUT';
    const PAYMENT_METHOD_POSTALORDER    = 'POSTALORDER';
    const PAYMENT_METHOD_EPS            = 'EPS';
    const PAYMENT_METHOD_ICLEAR         = 'ICLEAR';

    /**
     * @return array
     */
    public function getOptionArray()
    {
        return array(
            self::PAYMENT_METHOD_PREPAID => Mage::helper('nrapps_idealo')->__('Prepaid'),
            self::PAYMENT_METHOD_CASHONDELIVERY => Mage::helper('nrapps_idealo')->__('Cash on Delivery'),
            self::PAYMENT_METHOD_DEBIT => Mage::helper('nrapps_idealo')->__('Debit Payment'),
            self::PAYMENT_METHOD_PAYPAL => Mage::helper('nrapps_idealo')->__('PayPal'),
            self::PAYMENT_METHOD_INVOICE => Mage::helper('nrapps_idealo')->__('Invoice'),
            self::PAYMENT_METHOD_CREDITCARD => Mage::helper('nrapps_idealo')->__('Credit Card'),
            self::PAYMENT_METHOD_SOFORTUEBERWEISUNG => Mage::helper('nrapps_idealo')->__('Bank transfer via Sofort'),
            self::PAYMENT_METHOD_GIROPAY => Mage::helper('nrapps_idealo')->__('Giropay'),
            self::PAYMENT_METHOD_MONEYBOOKERS => Mage::helper('nrapps_idealo')->__('Moneybookers'),
            self::PAYMENT_METHOD_CLICKANDBUY => Mage::helper('nrapps_idealo')->__('Click & Buy'),
            self::PAYMENT_METHOD_PAYSAFECARD => Mage::helper('nrapps_idealo')->__('paysafecard'),
            self::PAYMENT_METHOD_GOOGLECHECKOUT => Mage::helper('nrapps_idealo')->__('Google Checkout'),
            self::PAYMENT_METHOD_POSTALORDER => Mage::helper('nrapps_idealo')->__('Payment by post'),
            self::PAYMENT_METHOD_EPS => Mage::helper('nrapps_idealo')->__('e-payment standard (Austria)'),
            self::PAYMENT_METHOD_ICLEAR => Mage::helper('nrapps_idealo')->__('iclear'),
        );
    }

    /**
     * @param string $option
     * @return string
     */
    public function getOptionLabel($option)
    {
        foreach($this->getOptionArray() as $optionCode => $optionLabel) {
            if ($option == $optionCode) {
                return $optionLabel;
            }
        }
        return '';
    }
}
