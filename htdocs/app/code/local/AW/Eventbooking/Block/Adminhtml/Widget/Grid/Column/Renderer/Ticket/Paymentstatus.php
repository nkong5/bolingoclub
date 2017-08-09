<?php

class AW_Eventbooking_Block_Adminhtml_Widget_Grid_Column_Renderer_Ticket_Paymentstatus
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
    /**
     * @return AW_Eventbooking_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('aw_eventbooking');
    }

    protected function _renderPaid($ticketId, $parentRenderedRow)
    {
        return sprintf(
            "%s [ <a href='#' onclick='AWEventbookingTicketsGrid.refundTicket(%d);return false;'>%s</a> ]",
            $parentRenderedRow,
            $ticketId,
            $this->_getHelper()->__('cancel')
        );
    }

    protected function _renderRefunded($ticketId, $parentRenderedRow)
    {
        return sprintf(
            "%s [ <a href='#' onclick='AWEventbookingTicketsGrid.undoRefundTicket(%d);return false;'>%s</a> ]",
            $parentRenderedRow,
            $ticketId,
            $this->_getHelper()->__('undo')
        );
    }

    public function render(Varien_Object $row)
    {
        $ticketId = $row->getData('id');
        $parentRenderedRow = parent::render($row);
        switch ($row->getData('payment_status')) {
            case AW_Eventbooking_Model_Ticket::PAYMENT_STATUS_PAID:
                return $this->_renderPaid($ticketId, $parentRenderedRow);
                break;
            case AW_Eventbooking_Model_Ticket::PAYMENT_STATUS_REFUNDED:
                return $this->_renderRefunded($ticketId, $parentRenderedRow);
                break;
        }
        return $parentRenderedRow;
    }
}
