<?php

class AW_Eventbooking_Block_Ext_View extends Mage_Adminhtml_Block_Template
{

    public function getOrderUrl($id)
    {
        return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $id));
    }

    public function getUndoUrl($code, $hash)
    {
        return $this->getUrl('*/*/undoredeem', array('code' => $code, 'hash' => $hash));
    }

    public function getActionUrl()
    {
        return $this->getUrl('*/*/view');
    }
}