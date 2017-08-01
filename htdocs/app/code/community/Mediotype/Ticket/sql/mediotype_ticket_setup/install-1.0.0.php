<?php
/**
 * Magento / Mediotype Module
 *
 *
 * @desc
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;

$installer->startSetup();
$fieldList = array(
    'price',
    'group_price',
    'special_price',
    'special_from_date',
    'special_to_date',
    'tier_price',
    'msrp_enabled',
    'msrp_display_actual_price_type',
    'msrp',
    'cost',
    'tax_class_id',
);

foreach ($fieldList as $field) {
    $applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));
    if (!in_array(Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET, $applyTo)) {
        $applyTo[] = Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET;
        $installer->updateAttribute('catalog_product', $field, 'apply_to', join(',', $applyTo));
    }
}


$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'event_datetime', array(
    'group' => 'Ticket Information',
    'sort_order' => 100,
    'type' => 'datetime',
    'backend' => 'mediotype_core/entity_attribute_backend_datetime',
    'frontend' => '',
    'label' => 'Event Date/Time',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => true,
    'user_defined' => true,
    'default' => '',
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    'used_for_promo_rules' => true
));

$installer->updateAttribute('catalog_product', 'event_datetime', 'frontend_input_renderer', 'mediotype_core/entity_attribute_input_renderer_datetime');


$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'event_title', array(
    'group' => 'Ticket Information',
    'sort_order' => 102,
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'Event Title',
    'input' => 'text',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => true,
    'user_defined' => true,
    'default' => '',
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    'used_for_promo_rules' => true
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'event_subtitle', array(
    'group' => 'Ticket Information',
    'sort_order' => 103,
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'Event Sub-Title',
    'input' => 'text',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    'used_for_promo_rules' => true
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'event_description', array(
    'group' => 'Ticket Information',
    'sort_order' => 104,
    'type' => 'text',
    'backend' => '',
    'frontend' => '',
    'label' => 'Event Description',
    'input' => 'textarea',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    'used_for_promo_rules' => true
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'venue_name', array(
    'group' => 'Ticket Information',
    'sort_order' => 105,
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'Venue Name',
    'input' => 'text',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    'used_for_promo_rules' => true
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'venue_phone', array(
    'group' => 'Ticket Information',
    'sort_order' => 106,
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'Venue Phone Number',
    'input' => 'text',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    'used_for_promo_rules' => true
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'venue_location', array(
    'group' => 'Ticket Information',
    'sort_order' => 107,
    'type' => 'text',
    'backend' => '',
    'frontend' => '',
    'label' => 'Venue Location',
    'input' => 'textarea',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    'used_for_promo_rules' => true
));

$newFieldList = array(
    "event_datetime",
    "event_title",
    "event_subtitle",
    "event_description",
    "venue_name",
    "venue_phone",
    "venue_location"
);

foreach ($newFieldList as $field) {
    $installer->updateAttribute('catalog_product', $field, 'apply_to', Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET);
}

$installer->updateAttribute('catalog_product', 'event_description', 'is_wysiwyg_enabled', true);
$installer->updateAttribute('catalog_product', 'venue_location', 'is_wysiwyg_enabled', true);

if($installer->getConnection()->isTableExists('mediotype_ticket')){
    $installer->getConnection()->dropTable('mediotype_ticket');
}
$ticketTable = $installer->getConnection()
    ->newTable($installer->getTable('mediotype_ticket'))
    ->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'ID')
    ->addColumn('sku',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(
        'nullable' => true,
        'default' => '',
    ),'Sku')
    ->addColumn('customer_id',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
        'nullable' => true,
        'default' => null,
    ),'Customer ID')
    ->addColumn('order_id',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
        'nullable' => true,
        'default' => null,
    ),'Order ID')
    ->addColumn('date_created',Varien_Db_Ddl_Table::TYPE_DATETIME,null,array(
        'nullable' => true,
        'default' => null,
    ),'Date Created')
    ->addColumn('date_redeemed',Varien_Db_Ddl_Table::TYPE_DATETIME,null,array(
        'nullable' => true,
        'default' => null,
    ),'Date Redeemed')
    ->addColumn('redeemed_by',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(
        'nullable' => true,
        'default' => '',
    ),'Redeemed By')
    ->addColumn('admin_scancount',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
        'nullable' => false,
        'default' => 0,
    ),'Admin Scan Count')
    ->addColumn('user_scancount',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
        'nullable' => false,
        'default' => 0,
    ),'User Scan Count')
    ->addColumn('revoked_by',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(
        'nullable' => true,
        'default' => '',
    ),'Revoked By')
    ->addColumn('date_revoked',Varien_Db_Ddl_Table::TYPE_DATETIME,null,array(
        'nullable' => true,
        'default' => null,
    ),'Date Revoked')
    ->addColumn('comments',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(
        'default' => '',
    ),'Comments')
    ->addForeignKey(
    $installer->getFkName(
        'mediotype_ticket',
        'order_id',
        'sales/order',
        'entity_id'
    ),
    'order_id',
    $installer->getTable('sales/order'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_RESTRICT,
    Varien_Db_Ddl_Table::ACTION_RESTRICT)
    ->addForeignKey(
        $installer->getFkName(
            'mediotype_ticket',
            'customer_id',
            'customer/entity',
            'entity_id'
        ),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_RESTRICT,
        Varien_Db_Ddl_Table::ACTION_RESTRICT);
$installer->getConnection()->createTable($ticketTable);

if($installer->getConnection()->isTableExists('mediotype_ticket_scanlog')){
    $installer->getConnection()->dropTable('mediotype_ticket_scanlog');
}
$ticketTable = $installer->getConnection()
    ->newTable($installer->getTable('mediotype_ticket_scanlog'))
    ->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'ID')
    ->addColumn('date_scanned',Varien_Db_Ddl_Table::TYPE_DATETIME,null,array(
        'nullable' => true,
        'default' => null,
    ),'Date Scanned')
    ->addColumn('scanned_by',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(
        'nullable' => false,
        'default' => '',
    ),'Scanned By')
    ->addColumn('user_type',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(
        'nullable' => true,
        'default' => '',
    ),'User Type')
    ->addColumn('ticket_id',Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Ticket ID')
    ->addForeignKey(
        $installer->getFkName(
            'mediotype_ticket_scanlog',
            'ticket_id',
            'mediotype_ticket',
            'id'
        ),
        'ticket_id',
        $installer->getTable('mediotype_ticket'),
        'id',
        Varien_Db_Ddl_Table::ACTION_RESTRICT,
        Varien_Db_Ddl_Table::ACTION_RESTRICT);
$installer->getConnection()->createTable($ticketTable);

// PASSBOOK INSTALL SCRIPT

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'passbook_enabled', array(
    'group' => 'Passbook Information',
    'sort_order' => 100,
    'type' => 'int',
    'backend' => '',
    'frontend' => '',
    'label' => 'Passbook Enabled',
    'input' => 'select',
    'class' => '',
    'source' => 'eav/entity_attribute_source_boolean',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => false,
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    "apply_to" => array(Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET)
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'pass_book_back_title', array(
    'group' => 'Passbook Information',
    'sort_order' => 103,
    'type' => 'text',
    'backend' => '',
    'frontend' => '',
    'label' => 'Passbook Back Title',
    'input' => 'text',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    'used_for_promo_rules' => true,
    "apply_to" => array(Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET)
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'pass_book_back', array(
    'group' => 'Passbook Information',
    'sort_order' => 104,
    'type' => 'text',
    'backend' => '',
    'frontend' => '',
    'label' => 'Passbook Back',
    'input' => 'textarea',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    'used_for_promo_rules' => true,
    "apply_to" => array(Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET)
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'passbook_icon', array(
    'group' => 'Passbook Information',
    'sort_order' => 105,
    'type' => 'text',
    'backend' => 'mediotype_ticket/product_source_image',
    'frontend' => 'mediotype_ticket/product_source_passbook',
    'label' => 'Passbook Icon',
    'input' => 'passbook',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    "apply_to" => array(Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET)
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'passbook_icon_2_x', array(
    'group' => 'Passbook Information',
    'sort_order' => 110,
    'type' => 'text',
    'backend' => 'mediotype_ticket/product_source_image',
    'frontend' => 'mediotype_ticket/product_source_passbook',
    'label' => 'Passbook Icon @ 2X',
    'input' => 'passbook',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    "apply_to" => array(Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET)
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'passbook_logo', array(
    'group' => 'Passbook Information',
    'sort_order' => 115,
    'type' => 'text',
    'backend' => 'mediotype_ticket/product_source_image',
    'frontend' => 'mediotype_ticket/product_source_passbook',
    'label' => 'Passbook Logo',
    'input' => 'passbook',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    "apply_to" => array(Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET)
));

//like pass.com.localhost.magento
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'pass_type_identifier', array(
    'group' => 'Passbook Information',
    'sort_order' => 101,
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'Passtype Identifier (Configured in Apple Developer Portal)',
    'input' => 'text',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    "apply_to" => array(Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET)
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'passbook_thumbnail', array(
    'group' => 'Passbook Information',
    'sort_order' => 105,
    'type' => 'text',
    'backend' => 'mediotype_ticket/product_source_image',
    'frontend' => 'mediotype_ticket/product_source_passbook',
    'label' => 'Passbook Thumbnail',
    'input' => 'passbook',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    "apply_to" => array(Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET)
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'passbook_thumbnail_2_x', array(
    'group' => 'Passbook Information',
    'sort_order' => 110,
    'type' => 'text',
    'backend' => 'mediotype_ticket/product_source_image',
    'frontend' => 'mediotype_ticket/product_source_passbook',
    'label' => 'Passbook Thumbnail @ 2X',
    'input' => 'passbook',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    "apply_to" => array(Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET)
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'passbook_logo_2_x', array(
    'group' => 'Passbook Information',
    'sort_order' => 115,
    'type' => 'text',
    'backend' => 'mediotype_ticket/product_source_image',
    'frontend' => 'mediotype_ticket/product_source_passbook',
    'label' => 'Passbook Logo @ 2X',
    'input' => 'passbook',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    "apply_to" => array(Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET)
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'passbook_background', array(
    'group' => 'Passbook Information',
    'sort_order' => 105,
    'type' => 'text',
    'backend' => 'mediotype_ticket/product_source_image',
    'frontend' => 'mediotype_ticket/product_source_passbook',
    'label' => 'Passbook Background',
    'input' => 'passbook',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    "apply_to" => array(Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET)
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'passbook_background_2_x', array(
    'group' => 'Passbook Information',
    'sort_order' => 110,
    'type' => 'text',
    'backend' => 'mediotype_ticket/product_source_image',
    'frontend' => 'mediotype_ticket/product_source_passbook',
    'label' => 'Passbook Background @ 2X',
    'input' => 'passbook',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'visible_on_front' => false,
    'unique' => false,
    'is_configurable' => false,
    "apply_to" => array(Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET)
));

$newFieldList = array(
    "passbook_enabled",
    "passbook_icon",
    "passbook_icon_2_x",
    "passbook_logo",
    "pass_type_identifier",
    "pass_book_back",
    "pass_book_back_title",
    "passbook_thumbnail",
    "passbook_thumbnail_2_x",
    "passbook_logo_2_x",
    "passbook_background",
    "passbook_background_2_x"
);

foreach ($newFieldList as $field) {
    $installer->updateAttribute('catalog_product', $field, 'apply_to', Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET);
}

$installer->endSetup();
