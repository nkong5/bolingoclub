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
class Iways_PaypalInstalments_Block_Sales_Fee extends Mage_Core_Block_Template
{
    /**
     * Get order store object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Get totals source object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Initialize fee totals
     *
     * @return Iways_PaypalInstalments_Block_Sales_Fee
     */
    public function initTotals()
    {
        if ((float)$this->getOrder()->getBaseInstalmentsFeeAmt()) {
            $source = $this->getSource();
            $value = $source->getInstalmentsFeeAmt();
            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code' => 'iways_paypalinstalments_fee',
                'strong' => false,
                'label' => Mage::helper('iways_paypalinstalments')->__('Fee'),
                'value' => $value,
                'area'  => 'footer',
                'class' => 'iways_paypalinstalments_fee'
            )), 'last');
            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code' => 'iways_paypalinstalments_fee_total',
                'strong' => true,
                'label' => Mage::helper('iways_paypalinstalments')->__('Grand Total (incl. Fee)'),
                'value' => $value + $source->getGrandTotal(),
                'area'  => 'footer',
                'class' => 'iways_paypalinstalments_fee_total'
            )), 'last');
        }
        return $this;
    }
}