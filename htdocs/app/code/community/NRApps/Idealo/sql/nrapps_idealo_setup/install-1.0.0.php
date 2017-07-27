<?php

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table nrapps_idealo_index
 */
$indexTableName = $installer->getTable('nrapps_idealo/index');

if ($installer->getConnection()->isTableExists($indexTableName)) {
    $installer->getConnection()->dropTable($indexTableName);
}

$indexTable = $installer->getConnection()->newTable($indexTableName)
    ->addColumn('index_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        'identity' => true,
    ), 'ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Store ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Product ID')
    ->addColumn('data', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
    ), 'Product Feed Data')
    ->addColumn('is_processed', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable' => false,
        'default' => 0,
    ), 'Is transferred to idealo')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Status from API')
    ->addColumn('feed_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Feed Type (xml or csv)')
    ->addColumn('status_message', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Message for Status from API')
    ->addColumn('transfer_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    ), 'Transfer Date')

    ->addIndex($installer->getIdxName('nrapps_idealo/index', array('store_id', 'product_id', 'feed_type'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('store_id', 'product_id', 'feed_type'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('nrapps_idealo/index', array('store_id', 'is_processed', 'feed_type'), Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX),
        array('store_id', 'is_processed', 'feed_type'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX))

    ->addForeignKey($installer->getFkName('nrapps_idealo/index', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)

    ->setComment('idealo Connect Index');

$installer->getConnection()->createTable($indexTable);


$installer->addAttribute('catalog_product', 'nrapps_idealo_exclude', array(
    'type'              => 'varchar',
    'input'             => 'select',
    'source'            => 'eav/entity_attribute_source_boolean',
    'label'             => Mage::helper('nrapps_idealo')->__('Exclude from idealo Connect'),
    'required'          => 0,
    'user_defined'      => 0,
    'group'             => 'idealo',
    'global'            => 0,
    'visible'           => 1,
    'searchable'        => 0,
    'filterable'        => 0,
    'filterable_in_search' => 0,
    'visible_on_front'  => 0,
    'is_configurable'   => 0,
));

$installer->addAttribute('catalog_category', 'nrapps_idealo_exclude', array(
    'type'              => 'int',
    'input'             => 'select',
    'source'            => 'eav/entity_attribute_source_boolean',
    'label'             => Mage::helper('nrapps_idealo')->__('Exclude from idealo Connect'),
    'note'              => Mage::helper('nrapps_idealo')->__('Exclude only Categories, not included Products'),
    'required'          => 0,
    'user_defined'      => 0,
    'group'             => 'idealo',
    'global'            => 0,
    'visible'           => 1,
));

$installer->endSetup();