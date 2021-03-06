<?php

class AW_Eventbooking_Block_Catalog_Product_View_Options_Type_Text
    extends Mage_Catalog_Block_Product_View_Options_Type_Text
{

    public function setOption(Mage_Catalog_Model_Product_Option $option)
    {
        if (
            $option->getData('custom_field')
            && $option->getData('type') == Mage_Catalog_Model_Product_Option::OPTION_TYPE_FIELD
        ) {
            $this->setTemplate('aw_eventbooking/catalog/product/view/options/type/text.phtml');
        }
        parent::setOption($option);
        return $this;
    }

    public function getDefaultValue()
    {
        return $this->getProduct()->getPreconfiguredValues()->getData('options/' . $this->getOption()->getId());
    }
	
	
	public function formatOptionPrice($priceStr)
	{
		$productPrice = $this->getProduct()->getPrice();
		$cleanPrice = preg_replace("/[^0-9.,]/","",$priceStr);
		$fullPrice = $productPrice + $cleanPrice;
		$fullPrice = number_format($fullPrice, 2, ',', ' ');
		$priceStr = str_replace($cleanPrice, $fullPrice, $priceStr);
		$priceStr = str_replace('+', '', $priceStr);
		return $priceStr;
	}
	
	public function getFormatedPrice()
	{
		if ($option = $this->getOption()) {
			return $this->formatOptionPrice($this->_formatPrice(array(
				'is_percent'    => ($option->getPriceType() == 'percent'),
				'pricing_value' => $option->getPrice($option->getPriceType() == 'percent')
			)));
		}
		return '';
	}
	
    public function getDefaultFormattedPrice()
    {
		$store = $this->getProduct()->getStore();
		$priceStr = $this->helper('core')->currencyByStore(0, $store, true, true);
		$priceStr = '<span class="price-notice">+' . $priceStr . '</span>';
		$priceStr = $this->formatOptionPrice($priceStr);
		
        return $priceStr;
    }

    public function getTicketSectionTitle()
    {
        Mage::register('ticket_section_title_inserted', true, true);
        return Mage::registry('ticket_section_title');
    }

    public function isTicketSectionTitleInserted()
    {
        return Mage::registry('ticket_section_title_inserted');
    }
}