<?php
class NRApps_Idealo_Adminhtml_Idealo_SettingsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Forward to config settings of idealo Connect
     */
    public function indexAction()
    {
        $this->_forward('edit', 'system_config', null, array('section' => 'nrapps_idealo'));
    }
    
    public function updateDeliveryTimeAction()
    {
        Mage::getSingleton('nrapps_idealo/deliveryTime')->update();
        
        Mage::getSingleton('adminhtml/session')->addSuccess(
            $this->__('Options have been updated.')
        );
        
        $this->_redirectReferer();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/nrapps_idealo/settings');
    }
}
