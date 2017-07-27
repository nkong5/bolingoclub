<?php

class NRApps_Idealo_Block_Adminhtml_Notifications extends Mage_Adminhtml_Block_Template
{
    const INSTALL_STATE_START       = 0;
    const INSTALL_STATE_ATTRIBUTES  = 1;
    const INSTALL_STATE_CONFIG      = 2;
    const INSTALL_STATE_FINISHED    = 3;

    public function getMessage()
    {
        if (Mage::helper('nrapps_idealo')->isInstalled()) {
            return '';
        }

        $message = '';
        switch($this->_getInstallState()) {

            case self::INSTALL_STATE_START:
                $message = Mage::helper('nrapps_idealo')->__(
                    'The idealo Connect is now installed. <a href="%s">Click here to start the configuration process.</a>',
                    Mage::helper('adminhtml')->getUrl('adminhtml/idealo_attributes/')
                );
                break;

            case self::INSTALL_STATE_ATTRIBUTES:
                $message = Mage::helper('nrapps_idealo')->__(
                    'idealo Connect Setup Step 1/2: Attributes Setup'
                );
                break;

            case self::INSTALL_STATE_CONFIG:
                $message = Mage::helper('nrapps_idealo')->__(
                    'idealo Connect Setup Step 2/2: Configuration and Default Values'
                );
                break;
        }

        return $message;
    }

    protected function _getInstallState()
    {
        return intval(Mage::app()->loadCache('nrapps_idealo_install_state'));
    }
}