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
 * Quote total for instalment fee
 *
 * @category   Iways
 * @package    Iways_PaypalInstalments
 * @author robert
 */
class Iways_PaypalInstalments_Model_Total_Quote_Fee_Total extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    const CODE = 'iways_paypalinstalments_fee_total';

    /**
     * Construct Quote Total
     */
    public function __construct()
    {
        $this->setCode(self::CODE);
    }

    /**
     * Fetch instalment fee for display
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $quote = $address->getQuote();
        $addressType = $address->getAddressType();
        if ($addressType == 'billing' && !$quote->isVirtual()) {
            return $this;
        }
        if($quote->getData('instalments_fee_amt')) {
            $feeTotalAmt = $quote->getData('instalments_fee_amt') + $quote->getData('grand_total');
            if($feeTotalAmt) {
                $address->addTotal(array(
                    'code'=> $this->getCode(),
                    'title'=> Mage::helper('iways_paypalinstalments')->__('Grand Total (incl. Fee)'),
                    'value'=> $feeTotalAmt,
                    'area'  => 'footer',
                    'strong' => true,
                    'class' => $this->getCode()
                ));
            }
        }
        return $this;
    }
}
