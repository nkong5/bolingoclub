<?php
class Diecrema_Startpage_Block_Adminhtml_Startpage_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('startpageGrid');
        // This is the primary key of the database
        $this->setDefaultSort('startpage_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('diecrema_startpage/startpage')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('startpage_id', array(
            'header'    => Mage::helper('diecrema_startpage')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'startpage_id',
        ));

        $this->addColumn('teaser_1_large_above_txt', array(
            'header'    => Mage::helper('diecrema_startpage')->__('First Image Title'),
            'align'     =>'left',
            'index'     => 'teaser_1_large_above_txt',
        ));

        $this->addColumn('teaser_2_large_above_txt', array(
            'header'    => Mage::helper('diecrema_startpage')->__('Second Image Title'),
            'align'     =>'left',
            'index'     => 'teaser_2_large_above_txt',
        ));

        $this->addColumn('teaser_3_large_above_txt', array(
            'header'    => Mage::helper('diecrema_startpage')->__('Third Image Title'),
            'align'     =>'left',
            'index'     => 'teaser_3_large_above_txt',
        ));

        $this->addColumn('store_code', array(
            'header'    => Mage::helper('diecrema_startpage')->__('Store Code'),
            'align'     =>'left',
            'index'     => 'store_code',
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('diecrema_startpage')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));

        $this->addColumn('update_time', array(
            'header'    => Mage::helper('diecrema_startpage')->__('Update Time'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'update_time',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }


}