<?php
/**
 * Magento / Mediotype Module
 * 
 *
 * @desc        
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Model_Product_Source_Image
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Model_Product_Source_Image extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract{

    /**
     * @param int $productId
     * @return string
     */
    protected function _getUploadDir($productId){
        $media = Mage::getBaseDir('media');
        return $media . '/passbook/'.$productId.'/';
    }

    /**
     * @return array
     */
    protected function _getAllowedExtensions()
    {
        return array('jpg', 'jpeg', 'png');
    }

    /**
     * Perform actions after object save
     *
     * @param  Mage_Core_Model_Abstract $object
     * @return Mage_Catalog_Model_Resource_Attribute
     */
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getName());

        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
            return $this;
        }

        try {
            $uploader = new Mage_Core_Model_File_Uploader($this->getAttribute()->getName());
            $uploader->setAllowedExtensions($this->_getAllowedExtensions());
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
        } catch (Exception $e){
            return $this;
        }
        $uploader->save(Mage::getBaseDir('media') . '/passbook/' . $object->getId() );

        $fileName = $uploader->getUploadedFileName() ;
        if ($fileName) {
            $object->setData($this->getAttribute()->getName(), $fileName);
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
        }
        return $this;
    }

}