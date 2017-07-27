<?php
class Diecrema_CatalogSearch_Block_Result extends Mage_CatalogSearch_Block_Result
{
	/**
     * Set Search Result collection
     *
     * @return Mage_CatalogSearch_Block_Result
     */
    public function setListCollection()
    {
       //MBI 07.05.2011
       $this->getListBlock()
           ->setCollection($this->_getProductCollection());
       return $this;
    }
    
	/**
     * Retrieve loaded category collection
     *
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection
     */
    protected function _getProductCollection()
    {
        
        echo '<pre>'.print_r( 'asdasd', 1).'</pre>';
        if (is_null($this->_productCollection)) {
            $this->_productCollection = Mage::getSingleton('catalogsearch/layer')->getProductCollection(); 
        }
        return $this->_productCollection;
    }
}