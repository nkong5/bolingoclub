<?php

class NRApps_Idealo_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isInstalled()
    {
        return Mage::getStoreConfigFlag('nrapps_idealo/is_installed');
    }

    public function setIsInstalled()
    {
        Mage::getResourceModel('core/setup', 'core_setup')->setConfigData('nrapps_idealo/is_installed', 1);
        Mage::app()->getCacheInstance()->cleanType('config');
    }

    /**
     * Output notice to admin if not all necessary product attributes are assigned
     */
    public function checkAttributesAssigned()
    {
        if ($this->isInstalled() && !$this->_allAttributesAssigned()) {
            Mage::getSingleton('adminhtml/session')->addNotice(
                $this->__(
                    'idealo Connect requires you to set up attributes. Click <a href="%s">here</a> to go to the attribute management.',
                    Mage::helper('adminhtml')->getUrl('adminhtml/idealo_attributes/')
                )
            );
        }
    }

    /**
     * Check if all necessary product attributes are assigned via admin form
     *
     * @return bool
     */
    protected function _allAttributesAssigned()
    {
        foreach (Mage::getStoreConfig('nrapps_idealo/attributes') as $assignedAttributeCode) {
            if (!$assignedAttributeCode) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string[]|string $types
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function getProductAttributes($types = null)
    {
        Mage::log($types);
        /** @var $attributes Mage_Catalog_Model_Resource_Product_Attribute_Collection */
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addFieldToFilter('frontend_label', array('notnull' => true))
            ->addFieldToFilter('frontend_label', array('neq' => ''))
            ->setOrder('frontend_label', 'ASC');

        if (!is_null($types)) {
            if (!is_array($types)) {
                $types = array($types);
            }
            $frontendTypes = array();
            $backendTypes = array();
            $sourceModels = array();
            foreach($types as $type) {
                if ($type == 'decimal') {
                    $backendTypes[] = $type;
                } elseif ($type == 'yesno') {
                    $sourceModels[] = 'eav/entity_attribute_source_boolean';
                } else {
                    $frontendTypes[] = $type;
                }
            }
            if (sizeof($frontendTypes)) {
                $attributes->addFieldToFilter('frontend_input', array('in' => $frontendTypes));
            }
            if (sizeof($backendTypes)) {
                $attributes->addFieldToFilter('backend_type', array('in' => $backendTypes));
            }
        }

        return $attributes;
    }

    /**
     * log facility (??)
     *
     * @param string $message
     * @param integer $level
     * @param string $file
     */
    public function log($message, $level = null, $file = '')
    {
        static $loggers = array();

        $level  = is_null($level) ? Zend_Log::DEBUG : $level;
        $file = empty($file) ? 'system.log' : $file;

        try {
            if (!isset($loggers[$file])) {
                $logDir  = Mage::getBaseDir('media') . DS . 'nrapps_idealo' . DS . 'log';
                $logFile = $logDir . DS . $file;

                if (!is_dir($logDir)) {
                    mkdir($logDir, 0777, true);
                }

                if (!file_exists($logFile)) {
                    file_put_contents($logFile, '');
                    chmod($logFile, 0777);
                }

                $format = '%timestamp% %priorityName% (%priority%): %message%' . PHP_EOL;
                $formatter = new Zend_Log_Formatter_Simple($format);
                $writerModel = (string)Mage::getConfig()->getNode('global/log/core/writer_model');
                if (!$writerModel) {
                    $writer = new Zend_Log_Writer_Stream($logFile);
                }
                else {
                    $writer = new $writerModel($logFile);
                }
                $writer->setFormatter($formatter);
                $loggers[$file] = new Zend_Log($writer);
            }

            if (is_array($message) || is_object($message)) {
                $message = print_r($message, true);
            }

            $loggers[$file]->log($message, $level);
        }
        catch (Exception $e) {
        }
    }
}