<?php
class NRApps_Idealo_Adminhtml_Idealo_AttributesController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('idealo Connect'))
            ->_title($this->__('Manage Attributes'));

        $this->loadLayout();

        $this->getLayout()
            ->getBlock('content')
            ->append($this->getLayout()->createBlock('nrapps_idealo/adminhtml_attributes'));

        $this->_setActiveMenu('system/nrapps_idealo/attributes');

        $this->_addBreadcrumb(
            Mage::helper('nrapps_idealo')->__('idealo Connect'),
            Mage::helper('nrapps_idealo')->__('idealo Connect')
        );
        $this->_addBreadcrumb(
            Mage::helper('nrapps_idealo')->__('Manage Attributes'),
            Mage::helper('nrapps_idealo')->__('Manage Attributes')
        );

        $this->renderLayout();
    }

    /**
     * Basic action: setup save action
     *
     * @return void
     */
    public function saveAction()
    {
        if ($this->getRequest()->isPost()) {

            // Set (translated) default value for delivery time
            if (!Mage::getStoreConfig('nrapps_idealo/default_values/delivery_time')) {
                $this->_getSetup()->setConfigData(
                    'nrapps_idealo/default_values/delivery_time',
                    $this->__('on stock')
                );
            }

            $attributes = $this->getRequest()->getParam('attribute');
            $existingAttributeCode = $this->getRequest()->getParam('existing_attribute');
            $newAttributeType = $this->getRequest()->getParam('new_attribute_type');

            $createdAttributes = 0;
            $updatedAttributeCodes = array();
            foreach($attributes as $attributeCode => $action) {
                if ($action == 'existing') {
                    if (Mage::getStoreConfig('nrapps_idealo/attributes/' . $attributeCode) != $existingAttributeCode[$attributeCode]) {
                        $this->_getSetup()->setConfigData('nrapps_idealo/attributes/' . $attributeCode, $existingAttributeCode[$attributeCode]);
                        $updatedAttributeCodes[] = $attributeCode;
                    }
                } else if ($action == 'create') {
                    Mage::getSingleton('nrapps_idealo/attribute')->createAttribute($attributeCode, $newAttributeType[$attributeCode]);
                    $this->_getSetup()->setConfigData('nrapps_idealo/attributes/' . $attributeCode, $attributeCode);
                    $updatedAttributeCodes[] = $attributeCode;
                    $createdAttributes++;
                } else if ($action == 'none') {
                    $this->_getSetup()->setConfigData('nrapps_idealo/attributes/' . $attributeCode, 'none');
                }

            }

            if (sizeof($updatedAttributeCodes)) {
                /** @todo */
                //Mage::helper('nrapps_idealo')->checkForOutdatedFeeds($updatedAttributeCodes);
                Mage::app()->getCacheInstance()->cleanType('config');
            }

            if ($createdAttributes) {
                $this->_getSession()->addSuccess($this->__('%s attributes have been created. You can fill them on the product edit page in the tab "idealo".', $createdAttributes));
            }

            if (!Mage::helper('nrapps_idealo')->isInstalled()) {
                $this->_getSession()->addSuccess($this->__('The attributes have been assigned. Please choose the default settings now.'));
                $this->_getSession()->addNotice($this->__('If you see an error here, please logout and login again.'));
                $this->_redirect('*/idealo_settings/');
            } else {
                $this->_getSession()->addSuccess($this->__('The attributes have been assigned.'));
                $this->_redirectReferer();
            }
        } else {
            $this->_redirectReferer();
        }
    }

    /**
     * @return Mage_Catalog_Model_Resource_Setup
     */
    protected function _getSetup()
    {
        return Mage::getResourceModel('catalog/setup', 'catalog_setup');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/nrapps_idealo/attributes');
    }
}
