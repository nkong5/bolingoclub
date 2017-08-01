<?php
class Mediotype_Ticket_Model_System_Config_Backend_Certificate extends Mage_Adminhtml_Model_System_Config_Backend_File {
    protected function _getUploadDir(){
        return Mage::getModuleDir("data","Mediotype_Ticket");
    }

    /**
     * Getter for allowed extensions of uploaded files
     *
     * @return array
     */
    protected function _getAllowedExtensions()
    {
        return array("p12");
    }
}