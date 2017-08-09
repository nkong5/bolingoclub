<?php
class AW_Eventbooking_Block_Adminhtml_Catalog_Product_Edit_Tab_Eventbooking_Summary
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setUseAjax(false);
        $this->setCountTotals(true);
    }

    protected function _prepareCollection()
    {
        $product = Mage::registry('current_product');
        /* @var $event AW_Eventbooking_Model_Event */
        $event = Mage::getModel('aw_eventbooking/event')
            ->loadByProductId($product->getId())
        ;
        /* @var AW_Eventbooking_Model_Resource_Event_Ticket_Collection */
        $collection = $event->getTicketCollection();
        $collection->addOrderHistoryTotals($product->getPrice());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function getCountTotals()
    {
        $totalList = array(
            'qty'                  => 0,
            'purchased_qty'        => 0,
            'available_qty'        => 0,
            'current_revenue'      => (float)0,
            'max_possible_revenue' => (float)0,
        );
        foreach ($this->getCollection() as $item) {
            $itemData = $item->getData();
            foreach ($totalList as $key => $value) {
                if (!array_key_exists($key, $itemData)) {
                    continue;
                }
                $totalList[$key] += $itemData[$key];
            }
        }
        foreach ($totalList as $key => $value) {
            $totalList[$key] = sprintf("%f", $value);
        }
        $this->setTotals(new Varien_Object($totalList));
        return parent::getCountTotals();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'index'        => 'title',
            'header'       => $this->__('Ticket Type'),
            'width'        => '20px',
            'sortable'     => false,
            'totals_label' => $this->__('Total'),
        ));

        $this->addColumn('initial_qty', array(
            'type'         => 'number',
            'index'        => 'qty',
            'header'       => $this->__('Initial Qty'),
            'sortable'     => false,
            'width'        => '20px',
        ));
        $this->addColumn('purchased_qty', array(
            'type'         => 'number',
            'index'        => 'purchased_qty',
            'header'       => $this->__('Purchased Qty'),
            'sortable'     => false,
            'width'        => '20px',
        ));
        $this->addColumn('available_qty', array(
            'type'         => 'number',
            'index'        => 'available_qty',
            'header'       => $this->__('Available Qty'),
            'sortable'     => false,
            'width'        => '20px',
        ));
        $this->addColumn('current_revenue', array(
            'type'          => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'         => 'current_revenue',
            'header'        => $this->__('Current Revenue'),
            'sortable'      => false,
            'width'         => '20px',
        ));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return null;
    }
}