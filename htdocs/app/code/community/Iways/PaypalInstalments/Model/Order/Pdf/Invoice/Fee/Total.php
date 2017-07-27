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
class Iways_PaypalInstalments_Model_Order_Pdf_Invoice_Fee_Total extends Mage_Sales_Model_Order_Pdf_Total_Default
{
    public function getAmount()
    {
        if($this->getSource()->getDataUsingMethod($this->getSourceField())) {
            $feeAmt = $this->getSource()->getDataUsingMethod($this->getSourceField());
            return $feeAmt + $this->getOrder()->getBaseGrandTotal();
        }
        return null;
    }
}