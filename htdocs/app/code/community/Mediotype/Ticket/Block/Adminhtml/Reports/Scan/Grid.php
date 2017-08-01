<?php
/**
 * Magento / Mediotype Module
 *
 *
 * @desc
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Block_Adminhtml_Reports_Scan_Grid
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Block_Adminhtml_Reports_Scan_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('scan_grid');
        $this->setTemplate('mediotype/ticket/grid.phtml');
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection  Mediotype_Ticket_Model_Resource_Scanrecord_Collection*/
        $collection = Mage::getModel('mediotype_ticket/scanrecord')
            ->getCollection();

        $this->setCollection($collection);

        $this->addExportType('*/*/scanExport', 'CSV');

        return parent::_prepareCollection();
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {

        $this->addColumn('id', array(
            'header' => $this->__('Id'),
            'align' => 'left',
            'index' => 'id',
            'type' => 'number'
        ));

        $this->addColumn('ticket_id', array(
            'header' => $this->__('Ticket #'),
            'align' => 'left',
            'index' => 'ticket_id',
            'type' => 'number'
        ));

        $this->addColumn('date_scanned', array(
            'header' => $this->__('Timestamp'),
            'align' => 'left',
            'width' => "150px",
            'index' => 'date_scanned',
            'type' => 'datetime'
        ));

        $this->addColumn('user_type', array(
            'header' => $this->__('User Type'),
            'align' => 'left',
            'index' => 'user_type',
            'width' => '75px',
            'type' => 'text'
        ));

        $this->addColumn('scanned_by', array(
            'header' => $this->__('Scanned By'),
            'align' => 'left',
            'index' => 'scanned_by',
            'type' => 'text'
        ));


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
        return $this->getUrl('*/adminhtml_index/index', array('id' => $row->getTicketId()));
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