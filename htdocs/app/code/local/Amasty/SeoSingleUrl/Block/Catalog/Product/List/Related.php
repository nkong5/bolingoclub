<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */


class Amasty_SeoSingleUrl_Block_Catalog_Product_List_Related extends Amasty_SeoSingleUrl_Block_Catalog_Product_List_Related_Pure
{
	/**
	 * @return $this
	 */
	protected function _prepareData()
	{
		parent::_prepareData();
		$this->_itemCollection->addUrlRewrite();

		return $this;
	}
}
