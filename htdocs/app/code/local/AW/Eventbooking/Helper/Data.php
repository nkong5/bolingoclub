<?php

class AW_Eventbooking_Helper_Data extends Mage_Core_Helper_Data
{
    public function isCustomSMTPInstalled()
    {
        return $this->isModuleOutputEnabled('AW_Customsmtp') && @class_exists('AW_Customsmtp_Model_Email_Template');
    }

    public function isEnabled()
    {
        return $this->isModuleOutputEnabled();
    }

    /**
     * @return array
     */
    public function getCurrentAdminRoleIds()
    {
        /** @var Mage_Admin_Model_Session $adminSession */
        $adminSession = Mage::getSingleton('admin/session');
        return $adminSession->isLoggedIn()
            ? $adminSession->getUser()->getRoles()
            : array();
    }

    /**
     * @return Mage_Core_Model_Cache|null
     */
    public function getFPCInstance()
    {
        if ($this->isModuleOutputEnabled('Enterprise_PageCache')) {
            return Enterprise_PageCache_Model_Cache::getCacheInstance();
        }
    }
}
