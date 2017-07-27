<?php
$installer = $this;
$installer->startSetup();
$installer->addAttribute('catalog_category', 'meigee_cat_menutype', array(
    'group'             => 'Meigee/Enhanced Categories',
    'label'             => 'Top Level Dropdown Menu Type',
    'note'              => "",
    'type'              => 'varchar',
    'input'             => 'select',
    'source'            => 'Meigee_CategoriesEnhanced/category_attribute_source_block_menutype',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
	'default' => 0,
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => false,
    'is_html_allowed_on_front'  => false,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'sort_order' => 0,
));
$installer->endSetup();