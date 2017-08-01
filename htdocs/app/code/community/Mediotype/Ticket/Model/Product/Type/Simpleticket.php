<?php
/**
 * Magento / Mediotype Module
 *
 *
 * @desc
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Model_Product_Type_Simpleticket
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Model_Product_Type_Simpleticket extends Mage_Catalog_Model_Product_Type_Virtual
{
    /**
     * @return string
     */
    public function getPrintUrl()
    {
        return Mage::getUrl('ticket/index/print', array("event" => $this->getSku()));
    }

    /**
     * @return string
     */
    public function getPassbookUrl()
    {
        return Mage::getUrl('ticket/index/passbook', array("event" => $this->getSku()));
    }

    /**
     * @return bool
     */
    public function hasOccurred()
    {
        $current = Mage::getModel('core/date')->timestamp(time());
        $eventDate = strtotime($this->getProduct()->getData("event_datetime"));

        if ($current > $eventDate) {
            return true;
        } else {
            return false;
        }
    }

    public function isSalable($product = null){
        if(parent::isSalable($product) && !is_null($product)){
            /** @var $product Mage_Catalog_Model_Product */
            $product = $product->load($product->getId());
            $current = Mage::getModel('core/date')->timestamp(time());
            $eventDate = strtotime($product->getData("event_datetime"));

            if ($current < $eventDate) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function getFormatDateTime($format = 'F jS, Y @ g:i A') {
        $product = $this->getProduct();
        return date($format, strtotime($product->getEventDatetime()));
    }
}