<?php

class Meigee_MeigeewidgetsUnique_Block_Brandslider
extends Mage_Core_Block_Html_Link
implements Mage_Widget_Block_Interface
{
    protected function _construct() {
        parent::_construct();
    }
	protected function _toHtml() {
        return parent::_toHtml();  
    }

    public function getBrandsOptions () {
        return $this->getData();
    }

    public function getBrands () {
        $widgetData = $this->getData();
        return $widgetData['brands'];

    }

    public function getWidgetId () {
        return $this->getData("widget_id");
    }

	public function getButtonsPosition() {
		return $this->getData('buttons_position');
	}

	public function getWidgetBackgroundColor() {
		return $this->getData('widget_bg_color');
	}

}
