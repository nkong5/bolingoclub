<?php
class Diecrema_Teaser_Block_Widget extends Mage_Catalog_Block_Product_Abstract {
	protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    function getFeaturedProducts() {
      
		// get all products that are marked as featured
		$objCollection = Mage::getModel('catalog/product')->getCollection();
		$objCollection->addAttributeToSelect('home_featured_teaser_1');
		$objCollection->addFieldToFilter(array(
			array('attribute' => 'home_featured_teaser_1', 'eq' => true),
		));
		
		$arrProducts = array(); 
		foreach ($objCollection as $objTempProduct) {
		    $storeId = $this->helper('core')->getStoreId();
		    
			$objProduct = Mage::getModel('catalog/product')->
			                    setStoreId($storeId)->load($objTempProduct->getId());
            $arrProducts[] = $objProduct; 
		}
		return $arrProducts; 
    }
}