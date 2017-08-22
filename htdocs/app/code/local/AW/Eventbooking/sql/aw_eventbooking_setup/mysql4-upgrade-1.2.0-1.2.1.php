<?php
/**
 * @var $this Mage_Catalog_Model_Resource_Setup
 */

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('aw_eventbooking/tickets_pdf'))
	->addColumn('ticket_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Ticket Id')
    ->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
        ), 'Path');

$installer->getConnection()->createTable($table);

$installer->endSetup();