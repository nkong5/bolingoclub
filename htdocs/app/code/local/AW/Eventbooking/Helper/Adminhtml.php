<?php

class AW_Eventbooking_Helper_Adminhtml
{
    public function getSendMessageUrl()
    {
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/aweventbooking_attendees/massSendMessage');
        return Zend_Json::encode($url);
    }
}
