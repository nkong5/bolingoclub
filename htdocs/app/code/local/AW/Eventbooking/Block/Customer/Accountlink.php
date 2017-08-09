<?php

class AW_Eventbooking_Block_Customer_Accountlink extends Mage_Core_Block_Abstract
{
    public function addLink()
    {
        if (Mage::helper('aw_eventbooking')->isEnabled()) {
            $parentBlock = $this->getParentBlock();
            if ($parentBlock instanceof Mage_Customer_Block_Account_Navigation) {
                $parentBlock->addLink(
                    'aweventbooking',
                    'aw_eventbooking/ticket/index',
                    $this->__('My Tickets'),
                    array('_secure' => true)
                );
            }
        }
    }
}
