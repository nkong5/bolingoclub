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
 * Iways PaypalInstalments Adminhtml Sales Invoice Totals
 *
 * @category   Iways
 * @package    Iways_PaypalInstalments
 * @author robert
 */
class Iways_PaypalInstalments_Block_Adminhtml_Sales_Invoice_Totals
    extends Mage_Adminhtml_Block_Sales_Order_Invoice_Totals
{
    /**
     * Add instalment fee amt to invoice totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        if ($this->getSource()->getOrder()->getInstalmentsFeeAmt()) {
            $this->addTotal(new Varien_Object(array(
                'code'      => 'iways_paypalinstalments_fee',
                'value'     => $this->getSource()->getInstalmentsFeeAmt(),
                'base_value'=> $this->getSource()->getBaseInstalmentsFeeAmt(),
                'label'     => $this->__('Fee'),
                'area'      => 'footer',
                'strong'    => false,
            )), 'grand_total');
            $this->addTotal(new Varien_Object(array(
                'code'      => 'iways_paypalinstalments_fee_total',
                'value'     => $this->getSource()->getInstalmentsFeeAmt() + $this->getSource()->getGrandTotal(),
                'base_value'=> $this->getSource()->getBaseInstalmentsFeeAmt() + $this->getSource()->getBaseGrandTotal(),
                'label'     => $this->__('Grand Total (incl. Fee)'),
                'strong'    => true,
                'area'      => 'footer'
            )), 'iways_paypalinstalments_fee');
        }
    }
}