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
class Iways_PaypalInstalments_Block_Review extends Mage_Paypal_Block_Express_Review
{
    /**
     * Paypal action prefix
     *
     * @var string
     */
    protected $_paypalActionPrefix = 'instalments';

    /**
     * Retrieve payment method and assign additional template values
     *
     * @return Mage_Paypal_Block_Express_Review
     */
    protected function _beforeToHtml()
    {
        $methodInstance = $this->_quote->getPayment()->getMethodInstance();
        $this->setPaymentMethodTitle($methodInstance->getTitle());

        $this->setEditUrl($this->getUrl("{$this->_paypalActionPrefix}/instalments/edit"))
            ->setPlaceOrderUrl($this->getUrl("{$this->_paypalActionPrefix}/instalments/placeOrder"));

        return Mage_Core_Block_Template::_beforeToHtml();
    }

    /**
     * Wrapper for getting instalments infos from payment additional information
     *
     * @return string
     */
    public function getInstalmentInfo($key)
    {
        return $this->_quote->getPayment()->getAdditionalInformation('instalments_' . $key);
    }
}
