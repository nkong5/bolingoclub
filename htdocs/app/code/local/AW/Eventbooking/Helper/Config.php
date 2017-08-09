<?php

class AW_Eventbooking_Helper_Config
{
    const EXTENSION_KEY = 'aw_eventbooking';

    const GENERAL_ALLOW_BUY_IF_STARTED = 'general/allow_buy';
    const QR_WIDTH = 'qr/width';
    const TEMPLATES_CONFIRMATION = 'templates/confirmation';
    const TEMPLATES_REMINDER = 'templates/reminder';

    public function getConfig($key, $store = null)
    {
        return Mage::getStoreConfig(self::EXTENSION_KEY . '/' . $key, $store);
    }

    public function getQRWidth($store = null)
    {
        return $this->getConfig(self::QR_WIDTH, $store);
    }

    public function getTemplatesConfirmation($store = null)
    {
        return $this->getConfig(self::TEMPLATES_CONFIRMATION, $store);
    }

    public function getTemplatesReminder($store = null)
    {
        return $this->getConfig(self::TEMPLATES_REMINDER, $store);
    }

    public function getAllowBuyTicketsIfEventStarted($store = null)
    {
        return $this->getConfig(self::GENERAL_ALLOW_BUY_IF_STARTED, $store);
    }

    public function setConfig($key, $value)
    {
        /** @var Mage_Core_Model_Config $configModel */
        $configModel = Mage::getModel('core/config');
        $configModel->saveConfig(self::EXTENSION_KEY . '/' . $key, $value);
        return $this;
    }
}
