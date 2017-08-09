<?php

class AW_Eventbooking_Block_Adminhtml_Products_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_ticketsStat = null;

    public function __construct()
    {
        parent::__construct();
        $this->setId('aw_evbook_products_grid');
        $this->setUseAjax(true);
    }

    /**
     * @return array
     */
    protected function _getProductIdsHavingEvent()
    {
        /** @var AW_Eventbooking_Model_Resource_Event_Collection $collection */
        $collection = Mage::getModel('aw_eventbooking/event')->getCollection();
        $productIds = $collection->getColumnValues('product_id');
        return $productIds ? $productIds : array(-1);
    }

    protected function _joinEventDataToProductCollection($collection)
    {
        /** @var Mage_Eav_Model_Entity_Collection_Abstract $collection */
        $collection
            ->joinTable(
                array('event' => 'aw_eventbooking/event'),
                'product_id=entity_id',
                array(
                    'event_id' => 'entity_id',
                    'event_enabled' => 'is_enabled',
                    'event_start_date' => 'event_start_date',
                    'event_end_date' => 'event_end_date'
                ),
                null,
                'left'
            );
        return $this;
    }

    protected function _prepareCollection()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addFieldToFilter('entity_id', array('in' => $this->_getProductIdsHavingEvent()));
        $this
            ->_joinEventDataToProductCollection($collection)
            ->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => $this->__('ID'),
            'width' => '100',
            'index' => 'entity_id',
        ));

        $this->addColumn('name', array(
            'header' => $this->__('Name'),
            'index' => 'name',
        ));

        $this->addColumn('sku', array(
            'header' => $this->__('SKU'),
            'width' => '80',
            'index' => 'sku',
        ));

        /** @var $sourceYesNo AW_Eventbooking_Model_Source_Yesno */
        $sourceYesNo = Mage::getSingleton('aw_eventbooking/source_yesno');

        $this->addColumn('event_enabled', array(
            'header' => $this->__('Event Booking Enabled'),
            'index' => 'event_enabled',
            'width' => '150',
            'type' => 'options',
            'options' => $sourceYesNo->toArray(),
        ));

        $this->addColumn('event_start_date', array(
            'header' => $this->__('Event Start Date'),
            'type' => 'datetime',
            'width' => '150',
            'index' => 'event_start_date',
        ));

        $this->addColumn('event_end_date', array(
            'header' => $this->__('Event End Date'),
            'type' => 'datetime',
            'width' => '150',
            'index' => 'event_end_date',
        ));

        $this->addColumn('tickets_qty', array(
            'header' => $this->__('Total Tickets'),
            'index' => 'tickets_qty',
            'width' => '150',
            'sortable' => false,
            'filter' => false,
        ));

        $this->addColumn('tickets_purchased_qty', array(
            'header' => $this->__('Purchased Tickets'),
            'index' => 'tickets_purchased_qty',
            'width' => '150',
            'sortable' => false,
            'filter' => false,
        ));

        $this->addColumn('tickets_available_qty', array(
            'header' => $this->__('Available Tickets'),
            'index' => 'tickets_available_qty',
            'width' => '150',
            'sortable' => false,
            'filter' => false,
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/catalog_product/edit', array(
            'store' => $this->getRequest()->getParam('store'),
            'id' => $row->getId(),
            'tab' => 'product_info_tabs_aw_eventbooking',
        ));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/ajaxgrid');
    }

    /**
     * Collects order history data for all events in current collection
     * @return array
     */
    protected function _getEventsStatData()
    {
        if ($this->_ticketsStat) {
            return $this->_ticketsStat;
        }

        $productsCollection = $this->getCollection();
        $ticketsCollection = Mage::getResourceModel('aw_eventbooking/event_ticket_collection')
            ->addFieldToFilter('main_table.event_id', array('in' => $productsCollection->getColumnValues('event_id')))
            ->addOrderHistoryTotals();
        $statSelect = new Zend_Db_Select($ticketsCollection->getConnection());
        $statSelect
            ->from(array('mt' => new Zend_Db_Expr('(' . $ticketsCollection->getSelect() . ')')))
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(array(
                'event_id',
                'tickets_qty' => new Zend_Db_Expr('SUM(qty)'),
                'tickets_purchased_qty' => new Zend_Db_Expr('ROUND(SUM(purchased_qty))'),
                'tickets_available_qty' => new Zend_Db_Expr('ROUND(SUM(available_qty))'),
                'tickets_current_revenue' => new Zend_Db_Expr('SUM(current_revenue)'),
            ))
            ->group('event_id');
        $this->_ticketsStat = $ticketsCollection->getConnection()->fetchAll($statSelect);

        return $this->_ticketsStat;
    }

    /**
     * Adds stat data to collection item
     * @param $item
     * @return $this
     */
    protected function _addEventStatData($item)
    {
        $statData = $this->_getEventsStatData();
        foreach ($statData as $statItem) {
            if ($statItem['event_id'] != $item->getData('event_id')) {
                continue;
            }
            $item->addData($statItem);
            break;
        }
        return $this;
    }

    public function _afterLoadCollection()
    {
        $collection = $this->getCollection();
        foreach ($collection as $item) {
            $this->_addEventStatData($item);
        }
        return parent::_afterLoadCollection();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        /** @var $statuses AW_Eventbooking_Model_Source_Yesno */
        $statuses = Mage::getSingleton('aw_eventbooking/source_status')->toOptionArray();
        array_unshift($statuses, array('label' => '', 'value' => ''));

        $this->getMassactionBlock()->addItem('status', array(
            'label' => $this->__('Change Event Booking Status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'status' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('catalog')->__('Status'),
                    'values' => $statuses
                ),
            ),
        ));
    }
}
