<?php
/**
 * Magento / Mediotype Module
 *
 *
 * @desc
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Model_Resource_Scanrecord
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Model_Resource_Scanrecord extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        $this->_init('mediotype_ticket/scanrecord', 'id');
    }
}