<?php
/**
 * Magento / Mediotype Module
 *
 *
 * @desc
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Block_Adminhtml_Reports_Sales_Grid
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Block_Adminhtml_Reports_Sales_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_grid');
        $this->setTemplate('mediotype/ticket/grid.phtml');
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection  Mediotype_Ticket_Model_Resource_Order_Collection */
        $collection = Mage::getModel('mediotype_ticket/order')
            ->getCollection();

        $collection = Mage::helper('mediotype_core')->joinEavTablesIntoCollection(
            $collection,
            'customer_id',
            'customer',
            0
        );

        $collection->getSelect()->group(array('order_id', 'sku'));
        $collection->getSelect()->columns('COUNT(`main_table`.`id`) AS qty_purchased');

        $this->setCollection($collection);

        $this->addExportType('*/*/salesExport', 'CSV');

        return parent::_prepareCollection();
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
            'order_id',
            array(
                'header' => $this->__('Order Id'),
                'align' => 'left',
                'width' => "100px",
                'index' => 'order_id',
                'type' => 'number'
            )
        );


        $this->addColumn(
            'date_purchased',
            array(
                'header' => $this->__('Date Purchased'),
                'align' => 'left',
                'width' => "150px",
                'index' => 'date_created',
                'type' => 'datetime',
            )
        );

        $this->addColumn(
            'qty_purchased',
            array(
                'header' => $this->__('Qty Purchased'),
                'align' => 'center',
                'width' => "100px",
                'index' => 'qty_purchased',
                'type' => 'number',
                'filter_condition_callback' => "Mediotype_Core_Helper_Data::filterHaving"
            )
        );

        $this->addColumn(
            'sku',
            array(
                'header' => $this->__('Sku'),
                'align' => 'left',
                'index' => 'sku',
                'type' => 'text'
            )
        );

        $this->addColumn(
            'customer_id',
            array(
                'header' => $this->__('Customer Id'),
                'align' => 'left',
                'width' => "90px",
                'index' => 'customer_id',
                'type' => 'number'
            )
        );

        $this->addColumn(
            'firstname',
            array(
                'header' => $this->__('First Name'),
                'align' => 'left',
                'index' => 'firstname',
                'type' => 'text',
                'filter_condition_callback' => "Mediotype_Core_Helper_Data::filterHaving"
            )
        );

        $this->addColumn(
            'lastname',
            array(
                'header' => $this->__('Last Name'),
                'align' => 'left',
                'index' => 'lastname',
                'type' => 'text',
                'filter_condition_callback' => "Mediotype_Core_Helper_Data::filterHaving"
            )
        );


        $this->addColumn(
            'email',
            array(
                'header' => $this->__('E-Mail'),
                'align' => 'left',
                'width' => '275px',
                'index' => 'email',
                'type' => 'text',
                'filter_condition_callback' => "Mediotype_Core_Helper_Data::filterHaving"
            )
        );


        return parent::_prepareColumns();
    }

    /**
     * Return row url for js event handlers
     *
     * @param Mage_Catalog_Model_Product|Varien_Object
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl("adminhtml/sales_order/view", array('order_id' => $row->getOrderId()));
    }

    /**
     * @return int
     */
    public function getTableWidth()
    {
        $total = 0;
        foreach ($this->getColumns() as $colId => $column) {
            $total += (INTEGER)rtrim(trim($column->getData('width')), "px");
        }
        return $total;
    }


}