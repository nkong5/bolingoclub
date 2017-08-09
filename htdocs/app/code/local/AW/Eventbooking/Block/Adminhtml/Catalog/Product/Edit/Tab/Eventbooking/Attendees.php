<?php
class AW_Eventbooking_Block_Adminhtml_Catalog_Product_Edit_Tab_Eventbooking_Attendees
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('aw_eventbooking_attendees_grid');
        $this->setDefaultSort('item_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
    }

    protected function _prepareCollection()
    {
        $product = Mage::registry('current_product');
        /* @var $event AW_Eventbooking_Model_Event */
        $event = Mage::getModel('aw_eventbooking/event')
            ->loadByProductId($product->getId())
        ;
        $ticketCollection = Mage::getResourceModel('aw_eventbooking/event_ticket_collection')
            ->addFilterOnEventId($event->getId())
        ;
        $orderHistoryCollection = Mage::getModel('aw_eventbooking/order_history')->getCollection()
            ->addFilterOnEventTicketCollection($ticketCollection)
        ;
        /* @var AW_Eventbooking_Model_Resource_Ticket_Collection */
        $collection = $orderHistoryCollection->getTicketsWithRelatedOrderItemCollection();
        $collection = Mage::helper('aw_eventbooking/order')->addOrderInfoToOrderItemCollection($collection);
        $collection = Mage::helper('aw_eventbooking/order')->addEventTicketInfoToOrderItemCollection($collection);
        $collection->addExpressionFieldToSelect(
            'purchased_qty',
            '({{i}}-{{r}})',
            array(
                 'i' => 'order_item_table.qty_invoiced',
                 'r' => 'order_item_table.qty_refunded'
            )
        );
        //get only invoiced
        $collection->addFieldToFilter(
            'order_item_table.qty_invoiced',
            array('gt' => new Zend_Db_Expr("order_item_table.qty_refunded"))
        );
        $collection->addFieldToFilter(
            'payment_status',
            array('eq' => AW_Eventbooking_Model_Ticket::PAYMENT_STATUS_PAID)
        );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer_name', array(
             'index'                     => 'order_customer_name',
             'header'                    => $this->__('Customer'),
             'width'                     => '200',
             'filter_condition_callback' => array($this, '_filterCustomerName'),
             'renderer'                  => 'aw_eventbooking/adminhtml_catalog_product_edit_tab_eventbooking_attendees_column_renderer_customer'
        ));
        $this->addColumn('holder_name', array(
             'index'                     => 'holder_name',
             'header'                    => $this->__('Holder'),
             'width'                     => '200',
             'filter_condition_callback' => array($this, '_filterHolderName'),
             'renderer'                  => 'aw_eventbooking/adminhtml_catalog_product_edit_tab_eventbooking_attendees_column_renderer_holder'
        ));
        $this->addColumn('increment_id', array(
             'index'        => 'order_increment_id',
             'filter_index' => 'order_table.increment_id',
             'header'       => $this->__('Order #'),
             'width'        => '200',
             'renderer'     => 'aw_eventbooking/adminhtml_catalog_product_edit_tab_eventbooking_attendees_column_renderer_order'
        ));
        $this->addColumn('ticket_type', array(
            'index'         => 'event_ticket_title',
            'header'        => $this->__('Ticket Type'),
            'filter_index'  => 'event_ticket_attribute_title_ds.value',
            'width'         => '200',
        ));
        $this->addExportType('adminhtml/aweventbooking_attendees/exportCsv', $this->__('CSV'));
        $this->addExportType('adminhtml/aweventbooking_attendees/exportXml', $this->__('XML'));
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('item_id');
        $this->getMassactionBlock()->setFormFieldName('attendees');

        $this->getMassactionBlock()->addItem('send_message', array(
            'label'    => $this->__('Send Message'),
            'url'      => $this->getUrl('adminhtml/aweventbooking_attendees/massSendMessage'),
            'selected' => true,
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return null;
    }

    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/aweventbooking_attendees/grid', array('_current' => true));
    }

    protected function _filterCustomerName($collection, $column)
    {
        $condition = $column->getFilter()->getCondition();
        $condition = array_pop($condition);
        $collection->getSelect()
            ->where(
                "order_table.customer_firstname LIKE ? OR order_table.customer_lastname LIKE ?",
                $condition,
                $condition
            )
        ;
        return $this;
    }

    protected function _filterHolderName($collection, $column)
    {
        $condition = $column->getFilter()->getCondition();
        $condition = array_pop($condition);
        $collection->getSelect()
            ->where(
                'holder_name LIKE ? OR holder_email LIKE ?',
                $condition,
                $condition
            )
        ;
        return $this;
    }
}