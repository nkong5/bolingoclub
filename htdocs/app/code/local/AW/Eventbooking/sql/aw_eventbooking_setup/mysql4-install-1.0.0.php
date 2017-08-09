<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->run("
CREATE TABLE IF NOT EXISTS `{$installer->getTable('aw_eventbooking/event')}` (
    `entity_id` INT(10) NOT NULL AUTO_INCREMENT,
    `product_id` INT(10) NULL,
    `is_enabled` TINYINT NULL,
    `event_datetime` DATETIME NULL,
    `day_count_before_send_reminder_letter` INT(10) NULL,
    `is_reminder_send` TINYINT NOT NULL DEFAULT 0,
    `is_terms_enabled` TINYINT NULL,
    PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$installer->getTable('aw_eventbooking/event_attribute')}` (
    `entity_id` INT NOT NULL AUTO_INCREMENT,
    `event_id` INT(10) NOT NULL,
    `store_id` INT(5) NULL,
    `attribute_code` VARCHAR(255) NULL,
    `value` VARCHAR(255) NULL,
    PRIMARY KEY (`entity_id`),
    INDEX `fk_aw_eventbooking_event_attribute_event_idx` (`event_id` ASC),
    CONSTRAINT `fk_aw_eventbooking_event_attribute_event`
        FOREIGN KEY (`event_id`)
        REFERENCES `{$installer->getTable('aw_eventbooking/event')}` (`entity_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$installer->getTable('aw_eventbooking/event_ticket')}` (
    `entity_id` INT(10) NOT NULL AUTO_INCREMENT,
    `event_id` INT(10) NOT NULL,
    `price` DECIMAL(12,4) NULL,
    `price_type` VARCHAR(45) NULL,
    `sku` VARCHAR(255) NULL,
    `qty` INT(10) NULL,
    PRIMARY KEY (`entity_id`),
    INDEX `fk_aw_eventbooking_ticket_settings_event_idx` (`event_id` ASC),
    CONSTRAINT `fk_aw_eventbooking_ticket_settings_event`
        FOREIGN KEY (`event_id`)
        REFERENCES `{$installer->getTable('aw_eventbooking/event')}` (`entity_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE  TABLE IF NOT EXISTS `{$installer->getTable('aw_eventbooking/event_ticket_attribute')}` (
    `entity_id` INT NOT NULL AUTO_INCREMENT,
    `ticket_id` INT(10) NOT NULL,
    `store_id` INT(10) NULL,
    `attribute_code` VARCHAR(255) NULL,
    `value` VARCHAR(255) NULL,
    PRIMARY KEY (`entity_id`),
    INDEX `fk_aw_eventbooking_event_ticket_attribute_event_ticket_idx` (`ticket_id` ASC) ,
    CONSTRAINT `fk_aw_eventbooking_event_ticket_data_event_ticket`
        FOREIGN KEY (`ticket_id`)
        REFERENCES `{$installer->getTable('aw_eventbooking/event_ticket')}` (`entity_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$installer->getTable('aw_eventbooking/order_history')}` (
    `entity_id` INT(10) NOT NULL AUTO_INCREMENT,
    `event_ticket_id` INT(10) NOT NULL,
    `order_item_id` INT(10) NOT NULL,
    PRIMARY KEY (`entity_id`),
    INDEX `fk_aw_eventbooking_order_history_event_tick_idx` (`event_ticket_id` ASC),
    CONSTRAINT `fk_aw_eventbooking_order_history_event_ticket`
        FOREIGN KEY (`event_ticket_id`)
        REFERENCES `{$installer->getTable('aw_eventbooking/event_ticket')}` (`entity_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();

/**
 * Adding new templates
 */
$templatesData = require_once '_templates.php';
aweb_addTemplates($templatesData);
