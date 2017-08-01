<?php
/**
 * Magento / Mediotype Module
 *
 *
 * @desc
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Adminhtml_ReportsController
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Adminhtml_ReportsController extends Mage_Adminhtml_Controller_Action
{

    /**
     *
     */
    public function indexAction()
    {
        $this->_redirect('mediotype_ticket/adminhtml_reports/sales');
    }

    /**
     *
     */
    public function salesAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     *
     */
    public function printAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     *
     */
    public function scanAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     *
     */
    public function statusAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * @return Mediotype_Ticket_Adminhtml_ReportsController
     */
    public function salesExportAction()
    {
        $fileName = 'ticket_sales_report.csv';
        $content = $this->getLayout()->createBlock('mediotype_ticket/adminhtml_reports_sales_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content, "text/csv");
        return $this;
    }

    /**
     * @return Mediotype_Ticket_Adminhtml_ReportsController
     */
    public function ticketExportAction()
    {
        $fileName = 'ticket_order_report.csv';
        $content = $this->getLayout()->createBlock('mediotype_ticket/adminhtml_reports_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content, "text/csv");
        return $this;
    }


    public function printTicketsAction()
    {
        $this->loadLayout();
        $ticketIds = $this->getRequest()->getParam('ticket_ids');
        $purchasedTickets = Mage::getModel('mediotype_ticket/order')->getCollection();
        $purchasedTickets->addFieldToFilter('id', $ticketIds);
        $purchasedTickets->setOrder('order_id');
        $purchasedTickets->load();


        $currentOrderId = null;
        foreach ($purchasedTickets as $tIndex => $ticketModel) {
            if ($currentOrderId != $ticketModel->getData('order_id')) {
                $lastOrderId = $currentOrderId;
                $currentOrderId = $ticketModel->getData('order_id');
                if (!is_null($lastOrderId)) {
                    $break = $this->getLayout()
                        ->createBlock('core/text', 'break-' . $currentOrderId)
                        ->setText('<div class="page-break"></div>');
                    $this->getLayout()->getBlock('content')->append($break);


                }
                $orderModel = Mage::getModel('sales/order')->load($currentOrderId);
                // Render Order Info
                $orderBlock = $this->getLayout()
                    ->createBlock('mediotype_ticket/adminhtml_ticket_print_info', 'order-' . $currentOrderId)
                    ->setTemplate('mediotype/ticket/print/info.phtml')
                    ->setData('order', $orderModel);
                $this->getLayout()->getBlock('content')->append($orderBlock);
            }

            $ticketBlock = $this->getLayout()
                ->createBlock('mediotype_ticket/ticket', "order-$currentOrderId-ticket-{$ticketModel->getData('id')}", array("ticketOrderModel" => $ticketModel));
            $this->getLayout()->getBlock('content')->append($ticketBlock);
        }


        $this->renderLayout();
    }

    /**
     * @return Mediotype_Ticket_Adminhtml_ReportsController
     */
    public function scanExportAction()
    {
        $fileName = 'ticket_scan_report.csv';
        $content = $this->getLayout()->createBlock('mediotype_ticket/adminhtml_reports_scan_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content, "text/csv");
        return $this;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        /** @var $helper Mediotype_Core_Helper_Data */
        $helper = Mage::helper("mediotype_core");

        $moduleName = $this->getRequest()->getModuleName();
        $controllerName = $this->getRequest()->getControllerName();
        $actionName = $helper->explodeCamelCase($this->getRequest()->getActionName());

        $aclPath = "$moduleName/$controllerName/" . implode("/", $actionName);
        Mage::log($aclPath, null, "acl.log");

        return Mage::getSingleton('admin/session')
            ->isAllowed($aclPath);
    }
}