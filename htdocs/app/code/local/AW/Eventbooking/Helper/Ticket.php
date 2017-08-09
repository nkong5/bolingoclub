<?php

class AW_Eventbooking_Helper_Ticket
{
    /**
     * @param $id
     * @return AW_Eventbooking_Model_Ticket|null
     */
    protected function _getTicket($id)
    {
        /** @var AW_Eventbooking_Model_Ticket $model */
        $model = Mage::getModel('aw_eventbooking/ticket')->load($id);
        return $model->getId() ? $model : null;
    }

    /**
     * @param AW_Eventbooking_Model_Ticket $ticket
     * @return AW_Eventbooking_Block_Ticket_Html
     */
    protected function _getTicketBlock(AW_Eventbooking_Model_Ticket $ticket)
    {
        $block = Mage::app()->getLayout()->createBlock('aw_eventbooking/ticket_html');
        $block->setTicket($ticket);
        return $block;
    }

    public function renderTicketHtml($ticket)
    {
        if (!($ticket instanceof AW_Eventbooking_Model_Ticket)
            && (!$ticket = $this->_getTicket($ticket))
        ) {
            return;
        }
        $emulationInfo = Mage::helper('aw_eventbooking/environment')->startEmulation($ticket->getStoreId());
        $output = $this->_getTicketBlock($ticket)->toHtml();
        Mage::helper('aw_eventbooking/environment')->stopEmulation($emulationInfo);
        return $output;
    }

    protected function _getPdfRenderer()
    {
        $includeFile = realpath(BP . DS . 'lib' . DS . 'tcpdf' . DS . 'tcpdf.php');
        if (!$includeFile) {
            $ex = new Exception('TCPDF not found');
            Mage::logException($ex);
            throw $ex;
        }

        require_once $includeFile;

        $tcpdf = new TCPDF();
        $tcpdf->SetFont('dejavusans', '', 10);
        $tcpdf->setPrintHeader(false);
        $tcpdf->setPrintFooter(false);
        $tcpdf->SetDisplayMode('fullwidth');

        return $tcpdf;
    }

    public function renderTicketsPdf($ticketCollection)
    {
        $rendered = false;
        $pdfRenderer = $this->_getPdfRenderer();
        foreach ($ticketCollection as $ticket) {
            $ticketHtml = $this->renderTicketHtml($ticket);
            if (!$ticketHtml) {
                continue;
            }
            $pdfRenderer->AddPage('P');
            $pdfRenderer->writeHTML($ticketHtml);
            $rendered = true;

        }

        if (!$rendered) {
            return;
        }

        return $pdfRenderer->Output(AW_Eventbooking_Helper_Mailer::PDF_NAME, 'S');
    }


    protected function _getTicketByCode($ticketCode)
    {

        /** @var AW_Eventbooking_Model_Ticket $ticket */
        $collection = Mage::getModel('aw_eventbooking/ticket')->getCollection();
        $collection
            ->joinEventData()
            ->joinOrderData()
            ->addFieldToFilter('code', $ticketCode);

        $ticket = $collection->getFirstItem();
        if (!$ticket->getId()) {
            return null;
        }
        return $ticket;

    }

    /**
     * @return AW_Eventbooking_Model_Ticket|null
     */
    public function getTicketByExternalRequest()
    {
        $request = Mage::app()->getRequest();
        $ticketCode = $request->getParam('code');
        $ticketHash = $request->getParam('hash');
        if (!$ticketCode || !$ticketHash) {
            return null;
        }
        $ticket = $this->_getTicketByCode($ticketCode);
        if ($ticket) {
            return (strcmp($ticket->getControlHash(), $ticketHash) === 0)
                ? $ticket
                : null;
            }
        else {
            return null;
        }
    }

    public function getTicketByCodeRequest()
    {
        $request = Mage::app()->getRequest();
        $ticketCode = trim($request->getParam('code'));
        if (!$ticketCode) {
            return null;
        }
        return $this->_getTicketByCode($ticketCode);
    }

    protected function _getOrderItemOptionsArray(Mage_Sales_Model_Order_Item $orderItem)
    {
        /** @var Mage_Sales_Block_Order_Item_Renderer_Default $block */
        $block = Mage::app()->getLayout()->createBlock('sales/order_item_renderer_default');
        $block->setItem($orderItem);
        $resultArray = array();
        foreach ($block->getItemOptions() as $option) {
            if ($option['option_id'] == AW_Eventbooking_Model_Event::PRODUCT_OPTION_ID) {
                continue;
            }
            $resultArray[$block->escapeHtml($option['label'])] = $block->getFormatedOptionValue($option);
        }
        return $resultArray;
    }

    public function getOrderItemOptions(Mage_Sales_Model_Order_Item $orderItem)
    {
        return $this->_getOrderItemOptionsArray($orderItem);
    }
}
