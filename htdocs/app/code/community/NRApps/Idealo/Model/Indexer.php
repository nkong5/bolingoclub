<?php

class NRApps_Idealo_Model_Indexer extends Mage_Index_Model_Indexer_Abstract
{
    const PRODUCT_COLLECTION_PAGE_SIZE = 500;

    protected $_matchedEntities = array(
        Mage_Catalog_Model_Product::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
        ),
        Mage_Catalog_Model_Category::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
    );

    /** @var array[] */
    protected $_generatorsByStore = array();

    protected $_resourceName = 'nrapps_idealo/indexer';

    /**
     * Create Generators by store
     */
    protected function _initGenerators()
    {
        if (!sizeof($this->_generatorsByStore)) {
            foreach(Mage::app()->getStores() as $store) {
                $this->_generatorsByStore[$store->getId()][] = Mage::getModel('nrapps_idealo/generator', $store);
                $this->_generatorsByStore[$store->getId()][] = Mage::getModel('nrapps_idealo/csv_generator', $store);
            }
        }
    }

    public function getName()
    {
        return Mage::helper('nrapps_idealo')->__('idealo Connect');
    }

    public function getDescription()
    {
        return Mage::helper('nrapps_idealo')->__('Indexing of generated Product Data for idealo Connect');
    }

    /**
     * @param Mage_Index_Model_Event $event
     * @return $this
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        if ($event->getEntity() == Mage_Catalog_Model_Product::ENTITY) {

            $productIdsForUpdate = array();
            $productIdsForDeletion = array();

            /* @var $product Varien_Object */
            $product = $event->getDataObject();

            switch ($event->getType()) {
                case Mage_Index_Model_Event::TYPE_SAVE:
                    $productIdsForUpdate[] = $product->getId();
                    break;

                case Mage_Index_Model_Event::TYPE_DELETE:
                    $productIdsForDeletion[] = $product->getId();
                    break;

                case Mage_Index_Model_Event::TYPE_MASS_ACTION:
                    $productIdsForUpdate = $product->getProductIds();
                    break;
            }

            if (sizeof($productIdsForUpdate)) {
                $event->addNewData('nrapps_idealo_update_product_ids', $productIdsForUpdate);
            }

            if (sizeof($productIdsForDeletion)) {
                $event->addNewData('nrapps_idealo_delete_product_ids', $productIdsForDeletion);
            }

        }

        if ($event->getEntity() == Mage_Catalog_Model_Category::ENTITY) {

            /* @var $category Mage_Catalog_Model_Category */
            $category = $event->getDataObject();

            if ($this->_isCategoryAttributeChanged($category)) {

                $productIdsForUpdate = $this->_getCategoryChildren($category->getId());

                foreach($category->getAllChildren(true) as $categoryId) {

                    $productIdsForUpdate = array_merge($productIdsForUpdate, $this->_getCategoryChildren($categoryId));
                }

                $event->addNewData('nrapps_idealo_update_product_ids', array_unique($productIdsForUpdate));
            }
        }
        return $this;
    }

    /**
     * Check if any of the configured category attributes is changed
     *
     * @param Varien_Object $object
     * @return bool
     */
    protected function _isCategoryAttributeChanged(Varien_Object $object)
    {
        foreach (Mage::getStoreConfig('nrapps_idealo/category_attributes') as $attributeCode => $value) {

            if ($object->getData($attributeCode) != $object->getOrigData($attributeCode)) {

                return true;
            }
        }

        return false;
    }

    /**
     * Get Product Ids by category id
     *
     * @param $categoryId
     * @return array
     */
    protected function _getCategoryChildren($categoryId)
    {
        return $this->getResource()->getProductIds($categoryId);
    }

    /**
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();

        if (isset($data['nrapps_idealo_update_product_ids'])) {
            $productIds = $data['nrapps_idealo_update_product_ids'];
            if (is_array($productIds) && !empty($productIds)) {

                $this->_initGenerators();

                foreach($productIds as $productId) {
                    $this->_updateIndexForProduct($productId, null, true);
                }

                foreach($this->_generatorsByStore as $storeGenerators) {
                    foreach ($storeGenerators as $generator) {
                        /** @var $generator NRApps_Idealo_Model_Generator */
                        $generator->generateFile();
                    }
                }
            }
        }

        if (isset($data['nrapps_idealo_delete_product_ids'])) {
            $productIds = $data['nrapps_idealo_delete_product_ids'];
            if (is_array($productIds) && !empty($productIds)) {

                $this->_initGenerators();

                foreach($productIds as $productId) {
                    $this->_deleteIndexForProduct($productId);
                }

                foreach($this->_generatorsByStore as $storeGenerators) {
                    foreach ($storeGenerators as $generator) {
                        /** @var $generator NRApps_Idealo_Model_Generator */
                        $generator->generateFile();
                    }
                }
            }
        }

        if (isset($data['nrapps_idealo_recreate_files_needed'])) {

            $this->_initGenerators(null, true);

            if (Mage::getStoreConfigFlag('nrapps_idealo/settings/regenerate_files_on_product_save')) {
                foreach($this->_generatorsByStore as $storeGenerators) {
                    foreach($storeGenerators as $generator) { /** @var $generator NRApps_Idealo_Model_Generator */
                        $generator->generateFile();
                    }
                }
            }
        }
    }

    /**
     * Reindex all feeds
     */
    public function reindexAll()
    {
        $this->_initGenerators();

        $this->_updateIndexForProducts();

        foreach($this->_generatorsByStore as $storeGenerators) {
            foreach ($storeGenerators as $generator) {
                /** @var $generator NRApps_Idealo_Model_Generator */
                $generator->generateFile();
            }
        }

        /* @var $indexProcess Mage_Index_Model_Process */
        $indexProcess = Mage::getModel('index/process')->load('nrapps_idealo', 'indexer_code');
        $indexProcess->setStatus(Mage_Index_Model_Process::STATUS_PENDING)->save();
    }

    /**
     * Regenerate all feed indices for all products
     */
    protected function _updateIndexForProducts()
    {
        $foundProductId = array();
        foreach($this->_generatorsByStore as $storeId => $storeGenerators) {

            $pageNumber = 1;
            do {
                $productCollection = $this->_getProductCollection($storeId, $pageNumber++);

                $appEmulation = Mage::getSingleton('core/app_emulation');
                $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

                Mage::getSingleton('core/translate')
                    ->setLocale(Mage::getStoreConfig('general/locale/code', $storeId))
                    ->init('frontend');

                foreach ($productCollection as $product) {
                    /** @var $product Mage_Catalog_Model_Product */
                    $this->_updateIndexForProduct($product, $storeId);
                    $foundProductId[$product->getId()] = $product->getId();
                }

                $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

            } while ($pageNumber <= $productCollection->getLastPageNumber());
        }

        $outdatedIndexRows = $this->getResource()->getIndexRowsByProductIdsExclude($foundProductId);

        foreach($outdatedIndexRows as $indexRow) {
            $this->_deleteIndexForProduct($indexRow['product_id'], $indexRow['feed_type'], $indexRow['store_id']);
        }
    }

    /**
     * Get a collection for all products of a selected store, limited by pageNumber
     *
     * @param int $storeId
     * @param int $pageNumber
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _getProductCollection($storeId, $pageNumber = 1)
    {
        /** @var $productCollection Mage_Catalog_Model_Resource_Product_Collection */
        $productCollection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->setPageSize(self::PRODUCT_COLLECTION_PAGE_SIZE)
            ->setCurPage($pageNumber)
            ->setStore(Mage::app()->getStore($storeId))
            ->addWebsiteFilter(Mage::app()->getStore($storeId)->getWebsiteId())
            ->setOrder('sku', 'ASC');

        $attributeSetIds = $this->_getIncludedAttributeSetIds($storeId);
        if (sizeof($attributeSetIds)) {
            $productCollection->addFieldToFilter('attribute_set_id', array('in' => $attributeSetIds));
        }

        $productTypes = $this->_getIncludedProductTypes($storeId);
        if (sizeof($productTypes)) {
            $productCollection->addFieldToFilter('type_id', array('in' => $productTypes));
        }

        Mage::dispatchEvent('nrapps_idealo_load_product_collection_before', array('collection' => $productCollection, 'store_id' => $storeId));

        $this->_addMediaGalleryAttributeToCollection($productCollection, $storeId);

        Mage::dispatchEvent('nrapps_idealo_load_product_collection_after', array('collection' => $productCollection, 'store_id' => $storeId));

        return $productCollection;
    }

    /**
     * @param int $storeId
     * @return array
     */
    protected function _getIncludedAttributeSetIds($storeId)
    {
        $attributeSetIds = explode(',', Mage::getStoreConfig('nrapps_idealo/product_options/allowed_attribute_sets'));

        if (in_array('all', $attributeSetIds)) {
            return array();
        }

        return $attributeSetIds;
    }

    /**
     * @param int $storeId
     * @return array
     */
    protected function _getIncludedProductTypes($storeId)
    {
        $productTypes = explode(',', Mage::getStoreConfig('nrapps_idealo/product_options/allowed_product_types'));

        if (in_array('all', $productTypes)) {
            return array();
        }

        return $productTypes;
    }

    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $_productCollection
     * @param int $storeId
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _addMediaGalleryAttributeToCollection(Mage_Catalog_Model_Resource_Product_Collection $_productCollection, $storeId)
    {
        $productIds = $_productCollection->getColumnValues('entity_id');

        if (!sizeof($productIds)) {
            return $_productCollection->load();
        }

        $_mediaGalleryAttributeId = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'media_gallery')->getAttributeId();

        /** @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');
        /** @var $_read Varien_Db_Adapter_Pdo_Mysql */
        $_read = $resource->getConnection('catalog_read');

        $query = '
        SELECT
            main.entity_id, `main`.`value_id`, `main`.`value` AS `file`,
            `value`.`label`, `value`.`position`, `value`.`disabled`, `default_value`.`label` AS `label_default`,
            `default_value`.`position` AS `position_default`,
            `default_value`.`disabled` AS `disabled_default`
        FROM `' . $resource->getTableName('catalog/product_attribute_media_gallery') . '` AS `main`
            LEFT JOIN `' . $resource->getTableName('catalog/product_attribute_media_gallery_value') . '` AS `value`
                ON main.value_id=value.value_id AND value.store_id=' . $storeId . '
            LEFT JOIN `' . $resource->getTableName('catalog/product_attribute_media_gallery_value') . '` AS `default_value`
                ON main.value_id=default_value.value_id AND default_value.store_id=0
        WHERE (
            main.attribute_id = ' . $_read->quote($_mediaGalleryAttributeId) . ')
            AND (main.entity_id IN (' . $_read->quote($productIds) . '))
        ORDER BY IF(value.position IS NULL, default_value.position, value.position) ASC
        ';
        $_mediaGalleryData = $_read->fetchAll($query);

        $_mediaGalleryByProductId = array();
        foreach ($_mediaGalleryData as $_galleryImage) {
            $k = $_galleryImage['entity_id'];
            unset($_galleryImage['entity_id']);
            if (!isset($_mediaGalleryByProductId[$k])) {
                $_mediaGalleryByProductId[$k] = array();
            }
            $_mediaGalleryByProductId[$k][] = $_galleryImage;
        }
        unset($_mediaGalleryData);
        foreach ($_productCollection as &$_product) {
            $_productId = $_product->getData('entity_id');
            if (isset($_mediaGalleryByProductId[$_productId])) {
                $_product->setData('media_gallery', array('images' => $_mediaGalleryByProductId[$_productId]));
            }
        }
        unset($_mediaGalleryByProductId);

        return $_productCollection;
    }

    /**
     * @param int|Mage_Catalog_Model_Product $product
     * @param int|null $restrictedStoreId
     * @param boolean $forceStoreEmulation
     */
    protected function _updateIndexForProduct($product, $restrictedStoreId = null, $forceStoreEmulation = false)
    {
        foreach($this->_generatorsByStore as $storeId => $storeGenerators) {
            foreach ($storeGenerators as $generator) {

                if (!is_null($restrictedStoreId) && $storeId != $restrictedStoreId) {
                    continue;
                }

                if ($forceStoreEmulation) {
                    $appEmulation = Mage::getSingleton('core/app_emulation');
                    $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);
                }

                /** @var $product Mage_Catalog_Model_Product */
                if (!$product instanceof Mage_Catalog_Model_Product || $product->getStoreId() != $storeId) {
                    if ($product instanceof Mage_Catalog_Model_Product) {
                        $product = $product->getId();
                    }
                    $product = Mage::getSingleton('catalog/product')
                        ->reset()
                        ->setStoreId($storeId)
                        ->load($product);
                }

                if (!$product->getId()) {
                    $this->_deleteIndexForProduct($product->getId(), null, $storeId);
                    continue;
                }

                if (!$this->_canExportProduct($product, $storeId)) {
                    $this->_deleteIndexForProduct($product->getId(), null, $storeId);
                    continue;
                }

                $productData = $generator->getProductData($product);
                $indexData = array(
                    'store_id' => $storeId,
                    'product_id' => $product->getId(),
                    'data' => $productData,
                    'feed_type' => $generator->getFeedType(),
                );

                $this->getResource()->update($indexData);

                // index parent product(s)
                if (Mage::getStoreConfig('nrapps_idealo/product_options/configurable_export_type') == NRApps_Idealo_Model_Source_ConfigurableTypes::CONFIGURABLE_TYPE_EXPORT_PARENT
                    && $parentProductIds = $product->getParentProductIds()
                ) {
                    foreach ($parentProductIds as $parentProductId) {
                        $this->_updateIndexForProduct($parentProductId, $restrictedStoreId);
                    }
                }

                if ($forceStoreEmulation) {
                    $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
                }
            }
        }
    }

    /**
     * @param int $productId
     * @param string $feedType
     * @param int|null $storeId
     */
    protected function _deleteIndexForProduct($productId, $feedType = null, $storeId = null)
    {
        if (is_null($feedType)) {
            $this->_deleteIndexForProduct($productId, 'xml', $storeId);
            $this->_deleteIndexForProduct($productId, 'csv', $storeId);
            return;
        }

        if ($feedType == 'xml') {
            $productSku = Mage::getResourceSingleton('catalog/product')->getAttributeRawValue($productId, 'sku', Mage_Core_Model_App::ADMIN_STORE_ID);
            $data = '
    <offer>
        <command>Delete</command>
        <sku>' . $productSku . '</sku>
    </offer>';

            $this->getResource()->update(array(
                'store_id' => $storeId,
                'product_id' => $productId,
                'feed_type' => $feedType,
                'data' => $data,
            ), true);
        } else {
            $this->getResource()->deleteByData(array(
                'store_id' => $storeId,
                'product_id' => $productId,
                'feed_type' => $feedType,
            ), true);
        }
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param int $storeId
     * @return bool
     */
    protected function _canExportProduct($product, $storeId)
    {
        // allow disabling products for feeds: set $product->setData('nrapps_idealo_exclude', true) if you want to exclude a product
        Mage::dispatchEvent('nrapps_idealo_can_export_product', array('product' => $product, 'store_id' => $storeId));

        if ($product->getData('nrapps_idealo_exclude')) {
            return false;
        }

        $store = Mage::app()->getStore($storeId);

        if (!$store->getIsActive() || !Mage::getStoreConfigFlag('nrapps_idealo/settings/is_active', $storeId)) {
            return false;
        }

        if (!in_array($store->getWebsiteId(), $product->getWebsiteIds())) {
            return false;
        }

        if ($product->getStatus() != Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
            return false;
        }

        if (!$this->_isAttributeSetAllowed($product->getAttributeSetId(), $storeId)) {
            return false;
        }
        
        switch(Mage::getStoreConfig('nrapps_idealo/product_options/configurable_export_type', $storeId)) {

            case NRApps_Idealo_Model_Source_ConfigurableTypes::CONFIGURABLE_TYPE_NO_EXPORT:
                if ($product->getTypeId() == 'configurable') {
                    return false;
                }
                if (!$product->isVisibleInSiteVisibility()) {
                    return false;
                }
                break;

            case NRApps_Idealo_Model_Source_ConfigurableTypes::CONFIGURABLE_TYPE_EXPORT_CHILDREN:
                if ($product->getTypeId() == 'configurable') {
                    return false;
                }
                if ($this->_isProductTypeAllowed('configurable', $storeId) && sizeof($parentProductIds = $this->_getConfigurableParentIds($product))) {
                    $product->setParentProductIds($parentProductIds);
                } else {
                    if (!$product->isVisibleInSiteVisibility()) {
                        return false;
                    }
                }
                break;

            case NRApps_Idealo_Model_Source_ConfigurableTypes::CONFIGURABLE_TYPE_EXPORT_PARENT:
                if (!$product->isVisibleInSiteVisibility()) {
                    return false;
                }
                break;
        }


        if (!Mage::getStoreConfigFlag('cataloginventory/options/show_out_of_stock', $storeId) && !$product->isAvailable() && !$product->isConfigurable()) {
            return false;
        }

        if (!$this->_isProductTypeAllowed($product->getTypeId(), $storeId)) {
            return false;
        }

        if (!Mage::getStoreConfig('nrapps_idealo/product_options/export_flexible_price_bundles', $storeId)
            && $product->getTypeId() == 'bundle'
            && !$product->getPriceType()) {
            return false;
        }

        return true;
    }

    /**
     * @param int $attributeSetId
     * @param int $storeId
     * @return bool
     */
    protected function _isAttributeSetAllowed($attributeSetId, $storeId)
    {
        $allowedAttributeSetIds = explode(',', Mage::getStoreConfig('nrapps_idealo/product_options/allowed_attribute_sets', $storeId));

        return in_array(NRApps_Idealo_Model_Source_AttributeSets::ATTRIBUTE_SETS_ALL, $allowedAttributeSetIds)
            || in_array($attributeSetId, $allowedAttributeSetIds);
    }

    /**
     * @param string $productType
     * @param int $storeId
     * @return bool
     */
    protected function _isProductTypeAllowed($productType, $storeId)
    {
        $allowedProductTypes = explode(',', Mage::getStoreConfig('nrapps_idealo/product_options/allowed_product_types', $storeId));

        return (in_array('all', $allowedProductTypes) || in_array($productType, $allowedProductTypes));
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _getConfigurableParentIds($product)
    {
        return Mage::getModel('catalog/product_type_configurable')
            ->getParentIdsByChild($product->getId());
    }
}