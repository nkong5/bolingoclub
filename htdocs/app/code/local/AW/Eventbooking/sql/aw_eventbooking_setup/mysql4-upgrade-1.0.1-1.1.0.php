<?php
/**
 * @var $this Mage_Catalog_Model_Resource_Setup
 */

$this->startSetup();

$eventTable = $this->getTable('aw_eventbooking/event');
$eventTicketTable = $this->getTable('aw_eventbooking/event_ticket');
$ticketTable = $this->getTable('aw_eventbooking/ticket');

$this->run(<<<SQL
CREATE TABLE IF NOT EXISTS `{$ticketTable}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_ticket_id` INT(10) NOT NULL,
  `order_item_id` INT(10) UNSIGNED NOT NULL,
  `code` TINYTEXT NOT NULL,
  `redeemed` TINYINT NOT NULL DEFAULT 0,
  `payment_status` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_awebk_ticket_event_ticket`
    FOREIGN KEY (`event_ticket_id`)
    REFERENCES `{$eventTicketTable}` (`entity_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
);

/** @var Varien_Db_Adapter_Interface $dbConnection */
$dbConnection = $this->getConnection();

if ($dbConnection->tableColumnExists($eventTable, 'event_datetime')) {
    /* Rename event_datetime column to event_start_date */
    $dbConnection->changeColumn(
        $eventTable,
        'event_datetime',
        'event_start_date',
        'DATETIME NULL'
    );
}

if (!$dbConnection->tableColumnExists($eventTable, 'event_end_date')) {
    /* Add event_end_date column */
    $dbConnection->addColumn(
        $eventTable,
        'event_end_date',
        'DATETIME NULL AFTER `event_start_date`'
    );
}

if (!$dbConnection->tableColumnExists($eventTable, 'generate_pdf_tickets')) {
    /* Add event_end_date column */
    $dbConnection->addColumn(
        $eventTable,
        'generate_pdf_tickets',
        'TINYINT NOT NULL DEFAULT 1 AFTER `is_terms_enabled`'
    );
}

if (!$dbConnection->tableColumnExists($eventTable, 'redeem_roles')) {
    /* Add event_end_date column */
    $dbConnection->addColumn(
        $eventTable,
        'redeem_roles',
        'TEXT NULL AFTER `generate_pdf_tickets`'
    );
}

if (!$dbConnection->tableColumnExists($eventTable, 'location')) {
    /* Add location column */
    $dbConnection->addColumn(
        $eventTable,
        'location',
        'TEXT NULL'
    );
}

if (!$dbConnection->tableColumnExists($eventTicketTable, 'codeprefix')) {
    /* Add code prefix column */
    $dbConnection->addColumn(
        $eventTicketTable,
        'codeprefix',
        'TINYTEXT NULL'
    );
}

/** @var Mage_Admin_Model_Resource_Roles_Collection $adminRolesCollection */
$adminRolesCollection = Mage::getModel('admin/roles')->getCollection();
$adminRoleIds = $adminRolesCollection->getColumnValues('role_id');
$dbConnection->update(
    $eventTable,
    array('redeem_roles' => implode(',', $adminRoleIds)),
    'redeem_roles IS NULL'
);

$templatesData = require_once '_templates.php';
aweb_addTemplates($templatesData);

aweb_setTemplateConfig(AW_Eventbooking_Helper_Config::TEMPLATES_CONFIRMATION, '[aW Event Ticket] Ticket Confirmation [New]');
aweb_setTemplateConfig(AW_Eventbooking_Helper_Config::TEMPLATES_REMINDER, '[aW Event Ticket] Event Reminder [New]');

$this->endSetup();
