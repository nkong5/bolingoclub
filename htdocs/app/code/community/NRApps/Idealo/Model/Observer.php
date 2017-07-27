<?php

class NRApps_Idealo_Model_Observer
{
    /**
     * Check if necessary attributes are assigned
     *
     * @param Varien_Event_Observer $event
     * @event admin_session_user_login_success
     */
    public function onAdminUserLoginSuccess(Varien_Event_Observer $event)
    {
        Mage::helper('nrapps_idealo')->checkAttributesAssigned();
    }

    /**
     * Check if any attribute default values have changed. If so, mark feeds which use the attribute(s) as outdated.
     *
     * @param Varien_Event_Observer $observer
     * @event controller_action_predispatch_adminhtml_system_config_save
     */
    public function onConfigChange(Varien_Event_Observer $observer)
    {
        /** @var $action Mage_Adminhtml_Controller_Action */
        $action = $observer->getControllerAction();

        if ($action->getRequest()->getParam('section') != 'nrapps_idealo') {
            return;
        }

        $store = $action->getRequest()->getParam('store');
        if ($store) {
            $storeId = Mage::app()->getStore($store)->getId();
        } else {
            $storeId = null;
        }

        $groupValues = $action->getRequest()->getParam('groups');

        if (intval(Mage::app()->loadCache('nrapps_idealo_install_state')) === intval(NRApps_Idealo_Block_Adminhtml_Notifications::INSTALL_STATE_CONFIG)) {

            Mage::helper('nrapps_idealo')->setIsInstalled();

            /* @var $indexProcess Mage_Index_Model_Process */
            $indexProcess = Mage::getModel('index/process')->load('nrapps_idealo', 'indexer_code');
            $indexProcess->setStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX)->save();

            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('nrapps_idealo')->__(
                'The idealo Connect module has been installed and configured. Please update the <a href="%s">idealo Connect Index</a> now.',
                Mage::helper('adminhtml')->getUrl('adminhtml/process/list')
            ));

        } else {

            if ($this->_hasUpdatedConfigValues($groupValues, $storeId)) {

                /* @var $indexProcess Mage_Index_Model_Process */
                $indexProcess = Mage::getModel('index/process')->load('nrapps_idealo', 'indexer_code');
                $indexProcess->setStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX)->save();

                Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('nrapps_idealo')->__(
                    'Configuration data has been changed. Please update the <a href="%s">idealo Connect Index</a> now.',
                    Mage::helper('adminhtml')->getUrl('adminhtml/process/list')
                ));
            }
        }

        if (isset($groupValues['settings']['fields']['is_active']['value']) && Mage::getStoreConfig('nrapps_idealo/settings/is_active', $storeId) != $groupValues['settings']['fields']['is_active']['value']) {

            if ($groupValues['settings']['fields']['is_active']['value']) {
                $logMessage = 'idealo Module has been activated.';
            } else {
                $logMessage = 'idealo Module has been deactivated.';
            }
            if ($storeId) {
                $store = Mage::app()->getStore($storeId);
                $logMessage .= ' (Store ' . $store->getName() . ' [' . $store->getCode() . '])';
            }
            Mage::helper('nrapps_idealo')->log($logMessage, Zend_Log::INFO, 'idealo_activations.txt', true);
        }

        $this->_updateAttributes();
    }

    /**
     * Send keep alive request to idealo and output error if it fails
     *
     * @param Varien_Event_Observer $observer
     * @event controller_action_postdispatch_adminhtml_system_config_save
     */
    public function afterConfigChange(Varien_Event_Observer $observer)
    {
        /** @var $action Mage_Adminhtml_Controller_Action */
        $action = $observer->getControllerAction();

        if ($action->getRequest()->getParam('section') != 'nrapps_idealo') {
            return;
        }

        if (Mage::getStoreConfigFlag('nrapps_idealo/settings/test_mode')) {
            return;
        }

        try {
            Mage::getSingleton('nrapps_idealo/api')->sendKeepAlive();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nrapps_idealo')->__(
                'Connection to idealo API successful.'
            ));
        } catch (NRApps_Idealo_AccountDisabledException $e) {
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('nrapps_idealo')->__(
                'Please check the access data and the state of the registration of your shop at idealo.'
            ));
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('nrapps_idealo')->__(
                'Connection to idealo API has failed, please check the credentials.'
            ));
        }

        if (!Mage::getResourceModel('cron/schedule_collection')->getSize()) {
            Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('nrapps_idealo')->__(
                'It seems you have no cronjobs running. They are needed for transferring data to idealo. We strongly suggest you setup cronjobs. See <a href="http://www.magentocommerce.com/wiki/1_-_installation_and_configuration/how_to_setup_a_cron_job" target="_blank">here</a> for details.'
            ));
        }
    }

    /**
     * Translate labels of attributes
     */
    protected function _updateAttributes()
    {
        foreach(array_merge(array('nrapps_idealo_exclude' => 'nrapps_idealo_exclude'), Mage::getStoreConfig('nrapps_idealo/attributes')) as $identifierCode => $attributeCode) {
            if ($identifierCode == $attributeCode) {
                /** @var $attribute Mage_Eav_Model_Entity_Attribute */
                $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeCode);
                $attributeTitle = $attribute->getFrontendLabel();
                $newAttributeTitle = Mage::helper('nrapps_idealo')->__($attributeTitle);
                if ($newAttributeTitle != $attributeTitle) {
                    $this->_getSetup()->updateAttribute('catalog_product', $attributeCode, 'frontend_label', $newAttributeTitle);
                }
            }
        }

        if (is_array(Mage::getStoreConfig('nrapps_idealo/category_attributes'))) {
            foreach(array_keys(Mage::getStoreConfig('nrapps_idealo/category_attributes')) as $attributeCode) {
                /** @var $attribute Mage_Eav_Model_Entity_Attribute */
                $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_category', $attributeCode);
                $attributeTitle = $attribute->getFrontendLabel();
                $newAttributeTitle = Mage::helper('nrapps_idealo')->__($attributeTitle);
                if ($newAttributeTitle != $attributeTitle) {
                    $this->_getSetup()->updateAttribute('catalog_category', $attributeCode, 'frontend_label', $newAttributeTitle);
                }
            }
        }
    }

    public function onAttributesControllerActionPredispatch($observer)
    {
        if (!Mage::helper('nrapps_idealo')->isInstalled() && Mage::app()->loadCache('nrapps_idealo_install_state')
            == NRApps_Idealo_Block_Adminhtml_Notifications::INSTALL_STATE_START) {

            Mage::app()->saveCache(
                NRApps_Idealo_Block_Adminhtml_Notifications::INSTALL_STATE_ATTRIBUTES,
                'nrapps_idealo_install_state'
            );
        }
    }

    public function onConfigControllerActionPredispatch($observer)
    {
        if (!Mage::helper('nrapps_idealo')->isInstalled() && Mage::app()->loadCache('nrapps_idealo_install_state')
            == NRApps_Idealo_Block_Adminhtml_Notifications::INSTALL_STATE_ATTRIBUTES) {

            Mage::app()->saveCache(
                NRApps_Idealo_Block_Adminhtml_Notifications::INSTALL_STATE_CONFIG,
                'nrapps_idealo_install_state'
            );
        }
    }

    /**
     * @return Mage_Catalog_Model_Resource_Setup
     */
    protected function _getSetup()
    {
        return Mage::getResourceModel('catalog/setup', 'catalog_setup');
    }

    /**
     * Check if any relevant config value has been changed so index has to be rebuilt
     *
     * @param array $groupValues
     * @param int $storeId
     * @return bool
     */
    protected function _hasUpdatedConfigValues($groupValues, $storeId)
    {
        if (isset($groupValues['settings']['fields']['is_active']['value']) && Mage::getStoreConfig('nrapps_idealo/settings/is_active', $storeId) != $groupValues['settings']['fields']['is_active']['value']) {
            return true;
        }
        foreach(array('default_values', 'shipping', 'product_options', 'minimum_order_surcharge', 'minimum_order_weight_surcharge') as $groupCode) {
            if (!isset($groupValues[$groupCode]['fields'])) {
                continue;
            }
            foreach ($groupValues[$groupCode]['fields'] as $fieldName => $fieldData) {
                if (isset($fieldData['value'])) {
                    $fieldValue = $fieldData['value'];
                    if (is_array($fieldValue)) {
                        if (isset($fieldValue['__empty'])) {
                            unset($fieldValue['__empty']);
                        }
                        switch($fieldName) {
                            case 'shipping_comments':
                            case 'shipping_costs':
                            case 'shipping_costs_by_weight':
                            case 'payment_methods':
                            case 'add_attribute_contents_to_name':
                            case 'add_attributes_to_export':
                                $fieldValue = serialize($fieldValue);
                                break;
                            default:
                                $fieldValue = implode(',', $fieldValue);
                        }
                    }
                    $oldFieldValue = Mage::getStoreConfig('nrapps_idealo/' . $groupCode . '/' . $fieldName, $storeId);
                    if ($oldFieldValue != $fieldValue) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}