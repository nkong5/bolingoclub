<?php
/**
 * Magento / Mediotype Module
 *
 *
 * @desc
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Helper_Data
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getTickets($order)
    {
        $results = array();
        foreach ($order->getAllItems() as $_item) {
            if ($_item->getProductType() == Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET) {
                $results[] = $_item;
            }
        }
        return $results;
    }

    /**
     * @param $sku
     * @return Mage_Core_Model_Abstract
     * Temporary function to help with bad loads
     */
    public function loadTicketBySku($sku)
    {
        //load by sku, then get id, then load by id
        $ticketProduct = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
        return Mage::getModel('catalog/product')->load($ticketProduct->getId());
    }
}