<?php

class AW_Eventbooking_Adminhtml_Aweventbooking_AbstractController extends Mage_Adminhtml_Controller_Action
{
    protected function _setTitle($title)
    {
        foreach ($title as $titlePart) {
            $this->_title($titlePart);
        }
        return $this;
    }

    protected function _initAction($title = null)
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/aw_eventbooking');
        if ($title) {
            $this->_setTitle(is_array($title) ? $title : array($title));
        }

        return $this;
    }
}
