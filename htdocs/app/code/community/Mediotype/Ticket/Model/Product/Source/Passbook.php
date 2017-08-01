<?php
/**
 * Magento / Mediotype Module
 * 
 *
 * @desc        
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Model_Product_Source_Passbook
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Model_Product_Source_Passbook extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract{

    /**
     * @param Varien_Object $object
     * @param null $size
     * @return string
     */
    public function getUrl($object, $size=null)
    {
        $image = $object->getData($this->getAttribute()->getAttributeCode());
        $url = Mage::app()->getStore($object->getStore())->getBaseUrl('media').'/passbook/' . $object->getId() . $image;
        return $url;
    }

}