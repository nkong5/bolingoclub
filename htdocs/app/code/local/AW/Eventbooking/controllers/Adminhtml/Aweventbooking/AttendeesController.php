<?php

class AW_Eventbooking_Adminhtml_Aweventbooking_AttendeesController extends Mage_Adminhtml_Controller_Action
{
    public function gridAction()
    {
        $this->_initProduct();

        $this->loadLayout();
        $grid = $this->getLayout()
            ->createBlock('aw_eventbooking/adminhtml_catalog_product_edit_tab_eventbooking_attendees')
        ;
        $this->getResponse()->setBody($grid->toHtml());
    }

    /**
     * Export customer grid to CSV format
     */
    public function exportCsvAction()
    {
        $this->_initProduct();

        $fileName   = 'attendees.csv';
        $grid = $this->getLayout()
            ->createBlock('aw_eventbooking/adminhtml_catalog_product_edit_tab_eventbooking_attendees')
        ;
        $content = $grid->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export customer grid to XML format
     */
    public function exportXmlAction()
    {
        $this->_initProduct();

        $fileName   = 'attendees.xml';
        $grid = $this->getLayout()
            ->createBlock('aw_eventbooking/adminhtml_catalog_product_edit_tab_eventbooking_attendees')
        ;
        $content = $grid->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function massSendMessageAction()
    {
        $result = array(
            'error'     => false,
            'message'   => $this->__('Message has been sent')
        );
        $attendeesIds = $this->getRequest()->getParam('ids', "");
        $attendeesIds = explode(",", $attendeesIds);
        $subject = $this->getRequest()->getParam('subject', null);
        $body = $this->getRequest()->getParam('body', null);
        if (count($attendeesIds) < 1 || is_null($subject) || is_null($body)) {
            $result['error'] = true;
            $result['message'] = $this->__('Bad request');
            return $this->getResponse()->setBody(
                Zend_Json::encode($result)
            );
        }
        try {
            Mage::helper('aw_eventbooking/mailer')->sendMessageToAttendees($attendeesIds, $subject, $body);
        } catch (Exception $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
            return $this->getResponse()->setBody(
                Zend_Json::encode($result)
            );
        }
        return $this->getResponse()->setBody(
            Zend_Json::encode($result)
        );
    }

    protected function _initProduct()
    {
        $productId = Mage::app()->getRequest()->getParam('id', 0);
        $product = Mage::getModel('catalog/product')->load($productId);
        Mage::register('current_product', $product);
        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/aw_eventbooking/products');
    }
}