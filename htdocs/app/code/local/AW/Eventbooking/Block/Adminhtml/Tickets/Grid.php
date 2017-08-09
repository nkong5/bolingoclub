<?php

class AW_Eventbooking_Block_Adminhtml_Tickets_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('aw_evbook_tickets_grid');
        $this->setUseAjax(true);
    }

    protected function _joinAdditionalData(Mage_Core_Model_Mysql4_Collection_Abstract $collection)
    {
        $collection->join(
            array('order_billing_address' => 'sales/order_address'),
            "(order_billing_address.parent_id=order_item.order_id)
                AND (order_billing_address.address_type='billing')",
            array()
        );
        $collection->addExpressionFieldToSelect(
            'customer_billing_name',
            "CONCAT_WS(' ', {{firstname}}, {{lastname}})",
            array(
                'firstname' => 'order_billing_address.firstname',
                'lastname' => 'order_billing_address.lastname',
            )
        );
        return $this;
    }

    protected function _prepareCollection()
    {
        /** @var AW_Eventbooking_Model_Resource_Ticket_Collection $collection */
        $collection = Mage::getModel('aw_eventbooking/ticket')->getCollection();
        $collection
            ->joinEventData()
            ->joinOrderData()
            ->addAdminRoleFilter();
        $this
            ->_joinAdditionalData($collection)
            ->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => $this->__('ID'),
            'width' => '100',
            'index' => 'id',
        ));

        $this->addColumn('event_id', array(
            'header' => $this->__('Event ID'),
            'width' => '100',
            'index' => 'event_id',
            'filter_condition_callback' => array($this, '_filterEventId'),
        ));

        $this->addColumn('event_title', array(
            'header' => $this->__('Event Name'),
            'index' => 'event_name',
            'filter_condition_callback' => array($this, '_filterEventName'),
            'sortable' => false,
            'renderer' => 'aw_eventbooking/adminhtml_widget_grid_column_renderer_link',
            'link_action' => 'adminhtml/catalog_product/edit',
            'link_params' => array(
                'id' => 'product_id',
                'tab' => 'product_info_tabs_aw_eventbooking',
                'awBackUrl' => base64_encode('adminhtml/aweventbooking_tickets/list'),
            ),
        ));

        $this->addColumn('customer_billing_name', array(
            'header' => $this->__('Customer Name'),
            'index' => 'customer_billing_name',
            'filter_condition_callback' => array($this, '_filterBillingNameCondition'),
        ));

        $this->addColumn('order_increment_id', array(
            'header' => $this->__('Order'),
            'index' => 'increment_id',
            'renderer' => 'aw_eventbooking/adminhtml_widget_grid_column_renderer_link',
            'link_action' => 'adminhtml/sales_order/view',
            'link_params' => array(
                'order_id' => 'order_id'
            )
        ));

        $this->addColumn('ticket_title', array(
            'header' => $this->__('Title'),
            'index' => 'ticket_title',
            'filter_condition_callback' => array($this, '_filterTicketTitleCondition'),
        ));

        $this->addColumn('code', array(
            'header' => $this->__('Code'),
            'index' => 'code',
        ));

        /** @var AW_Eventbooking_Model_Source_Ticket_Redeem $redeemedSource */
        $redeemedSource = Mage::getModel('aw_eventbooking/source_ticket_redeem');

        $this->addColumn('redeemed', array(
            'header' => $this->__('Redeem Status'),
            'index' => 'redeemed',
            'width' => '150',
            'type' => 'options',
            'options' => $redeemedSource->toShortOptionArray(),
            'renderer' => 'aw_eventbooking/adminhtml_widget_grid_column_renderer_ticket_redeem',
        ));

        /** @var AW_Eventbooking_Model_Source_Ticket_PaymentStatus $paymentStatusSource */
        $paymentStatusSource = Mage::getModel('aw_eventbooking/source_ticket_paymentstatus');

        $this->addColumn('payment_status', array(
            'header' => $this->__('Payment Status'),
            'index' => 'payment_status',
            'width' => '150',
            'type' => 'options',
            'options' => $paymentStatusSource->toShortOptionArray(),
            'renderer' => 'aw_eventbooking/adminhtml_widget_grid_column_renderer_ticket_paymentstatus',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/ajaxgrid');
    }

    public function getRowUrl($row)
    {
        return null;
    }

    protected function _filterBillingNameCondition(Mage_Core_Model_Mysql4_Collection_Abstract $collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $collection->addFieldToFilter(
            new Zend_Db_Expr("CONCAT_WS(' ', order_billing_address.firstname, order_billing_address.lastname)"),
            array('like' => '%' . $value . '%')
        );
    }

    protected function _filterTicketTitleCondition(Mage_Core_Model_Mysql4_Collection_Abstract $collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $collection->addFieldToFilter('event_ticket_attr_title.value', array('like' => '%' . $value . '%'));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ticket');

        /** @var AW_Eventbooking_Model_Source_Ticket_Redeem $redeemStatuses */
        $redeemStatuses = Mage::getModel('aw_eventbooking/source_ticket_redeem');

        $this->getMassactionBlock()->addItem('redeem_status', array(
            'label' => $this->__('Change Redeem Status'),
            'url' => $this->getUrl('*/*/massredeem'),
            'additional' => array(
                'status' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => $this->__('Status'),
                    'values' => $redeemStatuses->toOptionArray(),
                ),
            ),
        ));

        /** @var AW_Eventbooking_Model_Source_Ticket_Paymentstatus $paymentStatuses */
        $paymentStatuses = Mage::getModel('aw_eventbooking/source_ticket_paymentstatus');
        $limitedPaymentStatuses = array(
            $paymentStatuses->getOption(AW_Eventbooking_Model_Ticket::PAYMENT_STATUS_REFUNDED),
            $paymentStatuses->getOption(AW_Eventbooking_Model_Ticket::PAYMENT_STATUS_PAID),
        );

        $this->getMassactionBlock()->addItem('refund', array(
            'label' => $this->__('Change Payment Status'),
            'url' => $this->getUrl('*/*/masspaymentstatus'),
            'additional' => array(
                'status' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => $this->__('Status'),
                    'values' => $limitedPaymentStatuses,
                ),
            ),
        ));
    }

    protected function _afterLoadCollection()
    {
        /** @var AW_Eventbooking_Model_Resource_Ticket_Collection $collection */
        $collection = $this->getCollection();
        foreach ($collection as $item) {
            /** @var AW_Eventbooking_Model_Ticket $item */
            $item->setData('event_name', $item->getEventName());
        }
        return parent::_afterLoadCollection();
    }

    protected function _filterEventName($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return null;
        }
        $productCollection = Mage::getModel('catalog/product')->getCollection();
        $productCollection->addAttributeToFilter('name', array('like' => '%' . $value . '%'));
        $productIds = $productCollection->getAllIds();
        $collection->addFieldToFilter('event.product_id', array('in' => $productIds));
        return $this;
    }

    protected function _filterEventId($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return null;
        }
        $collection->addFieldToFilter('event.entity_id', array('eq' => $value));
        return $this;
    }
}
