<?php
/**
 * @var $this Mage_Catalog_Model_Resource_Setup
 */

$this->startSetup();

$ticketTable = $this->getTable('aw_eventbooking/ticket');
$historyTable = $this->getTable('aw_eventbooking/order_history');

/** @var Varien_Db_Adapter_Interface $dbConnection */
$dbConnection = $this->getConnection();

if (!$dbConnection->tableColumnExists($ticketTable, 'holder_name')) {
    /* Add holder_name column */
    $dbConnection->addColumn(
        $ticketTable,
        'holder_name',
        'varchar(255) NOT NULL AFTER `code`'
    );
}

if (!$dbConnection->tableColumnExists($ticketTable, 'holder_email')) {
    /* Add holder_email column */
    $dbConnection->addColumn(
        $ticketTable,
        'holder_email',
        'varchar(255) NOT NULL AFTER `holder_name`'
    );
}

if (!$dbConnection->tableColumnExists($historyTable, 'qty')) {
    /* Add qty column */
    $dbConnection->addColumn(
        $historyTable,
        'qty',
        'INT(10) NOT NULL DEFAULT 1 AFTER `event_ticket_id`'
    );
}

$this->endSetup();