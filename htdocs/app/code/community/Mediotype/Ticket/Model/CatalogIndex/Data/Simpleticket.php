<?php
/**
 * Magento / Mediotype Module
 *
 *
 * @desc
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Model_CatalogIndex_Data_Simpleticket
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Model_CatalogIndex_Data_Simpleticket extends Mage_CatalogIndex_Model_Data_Abstract
{
    /**
     * @return string
     */
    public function getTypeCode()
    {
        return Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET;
    }
}
