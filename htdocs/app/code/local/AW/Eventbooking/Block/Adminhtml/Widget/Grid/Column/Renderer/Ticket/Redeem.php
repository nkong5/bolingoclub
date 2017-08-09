<?php

class AW_Eventbooking_Block_Adminhtml_Widget_Grid_Column_Renderer_Ticket_Redeem
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
    /**
     * @return AW_Eventbooking_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('aw_eventbooking');
    }

    protected function _renderNotRedeemed($ticketId)
    {
        return sprintf(
            "<a href='#' onclick='AWEventbookingTicketsGrid.redeemTicket(%d);return false;'>%s</a>",
            $ticketId,
            $this->_getHelper()->__('Redeem')
        );
    }

    protected function _renderRedeemed($ticketId, $parentRenderedRow)
    {
        return sprintf(
            "%s [ <a href='#' onclick='AWEventbookingTicketsGrid.undoRedeemTicket(%d);return false;'>%s</a> ]",
            $parentRenderedRow,
            $ticketId,
            $this->_getHelper()->__('undo')
        );
    }

    public function render(Varien_Object $row)
    {
        $ticketId = $row->getData('id');
        $parentRenderedRow = parent::render($row);
        switch ($row->getData('redeemed')) {
            case AW_Eventbooking_Model_Ticket::NOT_REDEEMED:
                return $this->_renderNotRedeemed($ticketId);
                break;
            case AW_Eventbooking_Model_Ticket::REDEEMED:
                return $this->_renderRedeemed($ticketId, $parentRenderedRow);
                break;
        }
        return $parentRenderedRow;
    }
}
