<?php

/** @var Diecrema_Startpage_Model_Mys   ql4_Setup $installer */

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('startpage')}
    ADD COLUMN `store_code` VARCHAR (10) NOT NULL AFTER `teaser_3_link`;
");

$installer->endSetup();