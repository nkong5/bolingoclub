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
class Diecrema_Paypal_Model_Standard extends Mage_Paypal_Model_Standard
{

    /**
     * Return form field array
     *
     * @return array
     */
    public function getStandardCheckoutFormFields()
    {
        $result = parent::getStandardCheckoutFormFields();
        echo '<pre>'.print_r($result, true).'</pre>'; exit;

        Mage::log($result, 0, 'paypal.log');
        return $result;
    }

}
