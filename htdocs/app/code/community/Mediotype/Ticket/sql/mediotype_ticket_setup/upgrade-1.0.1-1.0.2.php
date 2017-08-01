<?php
/**
 *
 * @author      Joel Hart
 */

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('mediotype_ticket'),
        'email_sent',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
            'nullable' => false,
            'default' => 0,
            'comment' => 'Email Sent'
        )
    );

$installer->endSetup();
