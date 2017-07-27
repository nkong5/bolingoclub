<?php

class NRApps_Idealo_Model_Resource_Indexer extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('nrapps_idealo/index', 'index_id');
    }

    /**
     * Update single row - only if data has changed
     * 
     * @param array $indexData
     * @param boolean $updateExistingRowsOnly
     */
    public function update($indexData, $updateExistingRowsOnly = false)
    {
        $existingData = $this->getDataByStoreIdProductIdAndFeedType($indexData['store_id'], $indexData['product_id'], $indexData['feed_type']);
        if ($updateExistingRowsOnly && !$existingData) {
            return;
        }
        if ($indexData['data'] === $existingData) {
            return;
        }
        $indexData['is_processed'] = 0;
        $indexData['status'] = null;
        $indexData['status_message'] = null;
        $this->_getWriteAdapter()->insertOnDuplicate($this->getTable('nrapps_idealo/index'), $indexData);
    }

    /**
     * Mark row as processed, including status
     * 
     * @param int $storeId
     * @param int $productId
     * @param string $status
     * @param string $statusMessage
     */
    public function markProcessed($storeId, $productId, $status = 'OK', $statusMessage = '')
    {
        $condition = $this->_getReadAdapter()->quoteInto('store_id = ?', $storeId);
        $condition .= ' AND ' . $this->_getReadAdapter()->quoteInto('product_id = ?', $productId);
        $condition .= ' AND ' . $this->_getReadAdapter()->quoteInto('feed_type = ?', 'xml');

        $this->_getWriteAdapter()->update(
            $this->getTable('nrapps_idealo/index'),
            array(
                'is_processed' => 1,
                'status' => $status,
                'status_message' => $statusMessage,
                'transfer_date' => new Zend_Db_Expr('NOW()'),
            ),
            $condition
        );
    }

    /**
     * Mark row as processed, including status
     *
     * @param string $sku
     * @param string $status
     * @param string $statusMessage
     */
    public function markProcessedBySku($sku, $status = 'OK', $statusMessage = '')
    {
        $productId = Mage::getSingleton('catalog/product')->getIdBySku($sku);
        $condition = $this->_getReadAdapter()->quoteInto('product_id = ?', $productId);
        $condition .= ' AND ' . $this->_getReadAdapter()->quoteInto('feed_type = ?', 'xml');

        $this->_getWriteAdapter()->update(
            $this->getTable('nrapps_idealo/index'),
            array(
                'is_processed' => 1,
                'status' => $status,
                'status_message' => $statusMessage,
                'transfer_date' => new Zend_Db_Expr('NOW()'),
            ),
            $condition
        );
    }

    /**
     * Mark all rows as uprocessed so they will get transferred again
     */
    public function markAllUnprocessed()
    {
        $condition = $this->_getReadAdapter()->quoteInto('feed_type = ?', 'xml');

        $this->_getWriteAdapter()->update(
            $this->getTable('nrapps_idealo/index'),
            array(
                'is_processed' => 0,
                'status' => null,
                'status_message' => null,
            ),
            $condition
        );
    }

    /**
     * @param array $indexData
     */
    public function deleteByData($indexData)
    {
        $deleteWhere = array();
        foreach($indexData as $key => $value) {
            $deleteWhere[$key . ' = ?'] = $value;
        }
        $this->_getWriteAdapter()->delete($this->getTable('nrapps_idealo/index'), $deleteWhere);
    }

    /**
     * @param int $storeId
     */
    public function deleteFeedData($storeId)
    {
        $this->deleteByData(array('store_id' => $storeId));
    }

    /**
     * @param int $storeId
     * @param int $productId
     * @param string $feedType
     * @return string
     */
    public function getDataByStoreIdProductIdAndFeedType($storeId, $productId, $feedType)
    {
        $condition = $this->_getReadAdapter()->quoteInto('store_id = ?', $storeId)
            . ' AND ' . $this->_getReadAdapter()->quoteInto('product_id = ?', $productId)
            . ' AND ' . $this->_getReadAdapter()->quoteInto('feed_type = ?', $feedType)
        ;
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('nrapps_idealo/index'), array('data'))
            ->where($condition);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getDataByStoreId($storeId)
    {
        $condition = $this->_getReadAdapter()->quoteInto('store_id = ?', $storeId);
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('nrapps_idealo/index'), array('data'))
            ->where($condition);

        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * @param int $storeId
     * @param int $limit
     * @return array
     */
    public function getDataToProcess($storeId = null, $limit = null)
    {
        $condition = $this->_getReadAdapter()->quoteInto('is_processed != ?', 1);
        if (!is_null($storeId)) {
            $condition .= ' AND ' . $this->_getReadAdapter()->quoteInto('store_id = ?', $storeId);
        }
        $condition .= ' AND ' . $this->_getReadAdapter()->quoteInto('feed_type = ?', 'xml');

        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('nrapps_idealo/index'), array('*'))
            ->where($condition);

        if ($limit) {
            $select->limit($limit);
        }
        
        return $this->_getReadAdapter()->fetchAll($select);
    }

    public function getIndexRowsByProductIdsExclude($productIds)
    {
        $condition = $this->_getReadAdapter()->quoteInto('product_id NOT IN (?)', $productIds);

        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('nrapps_idealo/index'), array('product_id', 'store_id'))
            ->where($condition);

        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * Retrieve product category identifiers
     *
     * @param int $categoryId
     * @return array
     */
    public function getProductIds($categoryId)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->getTable('catalog/category_product'), 'product_id')
            ->where('category_id = ?', (int)$categoryId);

        return $adapter->fetchCol($select);
    }
}
