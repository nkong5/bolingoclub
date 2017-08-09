<?php

class AW_Eventbooking_Model_Resource_Event extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('aw_eventbooking/event', 'entity_id');
    }

    /**
     * After Load event push attribute values for store to model
     *
     * @param Mage_Core_Model_Abstract $event
     * @return AW_Eventbooking_Model_Resource_Event
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $event)
    {
        $redeemRoles = $event->getData('redeem_roles');
        $event->setData('redeem_roles', (is_string($redeemRoles) && $redeemRoles)
            ? explode(',', $redeemRoles)
            : array());

        foreach ($event->getAttributeCollection() as $attribute) {
            $event->setData($attribute->getAttributeCode(), $attribute->getValue());
        }
        return parent::_afterLoad($event);
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $event)
    {
        $redeemRoles = $event->getData('redeem_roles');
        $event->setData('redeem_roles', (is_array($redeemRoles) && $redeemRoles)
            ? implode(',', $redeemRoles)
            : null);
        return parent::_beforeSave($event);
    }
}
