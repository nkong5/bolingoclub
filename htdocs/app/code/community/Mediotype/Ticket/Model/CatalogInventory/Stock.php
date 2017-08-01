<?php
/**
 * Created by PhpStorm.
 * User: Dale
 * Date: 10/15/14
 * Time: 9:18 AM
 */ 
class Mediotype_Ticket_Model_CatalogInventory_Stock extends Mage_CatalogInventory_Model_Stock {

    /**
     * Subtract product qtys from stock.
     * Return array of items that require full save
     *
     * @param array $items
     * @return array
     */
    public function registerProductsSale($items)
    {
        $qtys = $this->_prepareProductQtys($items);
        $item = Mage::getModel('cataloginventory/stock_item');
        $this->_getResource()->beginTransaction();
        $stockInfo = $this->_getResource()->getProductsStock($this, array_keys($qtys), true);
        $fullSaveItems = array();
        $storeId = Mage::app()->getStore()->getStoreId();
        $configuredStockAvailibilty = Mage::getStoreConfig('mediotype_ticket/ticket_email/stock_update_on_available', $storeId);
        foreach ($stockInfo as $itemInfo) {
            $item->setData($itemInfo);
            if (!$item->checkQty($qtys[$item->getProductId()])) {
                $this->_getResource()->commit();
                Mage::throwException(Mage::helper('cataloginventory')->__('Not all products are available in the requested quantity'));
            }
            //check if it's a simple ticket and that stock update on available is on
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            if($product->getData('type_id') == 'simpleticket' && $configuredStockAvailibilty) {
                //don't subtract qty if this is true
                $qtys[$item->getProductId()] = 0;
            }
            else {
                $item->subtractQty($qtys[$item->getProductId()]);
                if (!$item->verifyStock() || $item->verifyNotification()) {
                    $fullSaveItems[] = clone $item;
                }
            }
        }
        $this->_getResource()->correctItemsQty($this, $qtys, '-');
        $this->_getResource()->commit();
        return $fullSaveItems;
    }

    /**
     * Get back to stock (when order is canceled or whatever else)
     *
     * @param int $productId
     * @param numeric $qty
     * @return Mage_CatalogInventory_Model_Stock
     */
    public function backItemQty($productId, $qty)
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        $configuredStockAvailibilty = Mage::getStoreConfig('mediotype_ticket/ticket_email/stock_update_on_available', $storeId);
        $product = Mage::getModel('catalog/product')->load($productId);
        //check if it's a simple ticket and that stock update on available is on
        if($product->getData('type_id') == 'simpleticket' && $configuredStockAvailibilty) {
            //in this case, don't update stock
            return $this;
        }
        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
        if ($stockItem->getId() && Mage::helper('catalogInventory')->isQty($stockItem->getTypeId())) {
            $stockItem->addQty($qty);
            if ($stockItem->getCanBackInStock() && $stockItem->getQty() > $stockItem->getMinQty()) {
                $stockItem->setIsInStock(true)
                    ->setStockStatusChangedAutomaticallyFlag(true);
            }
            $stockItem->save();
        }
        return $this;
    }
}