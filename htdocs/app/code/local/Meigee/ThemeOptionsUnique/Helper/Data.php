<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsUnique_Helper_Data extends Mage_Core_Helper_Abstract
{
 public function getThemeOptionsUnique ($themeOption) {
 	switch ($themeOption) {
		case 'meigee_unique_general':
		    return Mage::getStoreConfig('meigee_unique_general');
		break;
		case 'meigee_unique_design':
		    return Mage::getStoreConfig('meigee_unique_design');
		break;
		case 'meigee_unique_design_skin1':
		    return Mage::getStoreConfig('meigee_unique_design_skin1');
		break;
		case 'meigee_unique_design_skin2':
		    return Mage::getStoreConfig('meigee_unique_design_skin2');
		break;
		case 'meigee_unique_design_skin3':
		    return Mage::getStoreConfig('meigee_unique_design_skin3');
		break;
		case 'meigee_unique_design_skin4':
		    return Mage::getStoreConfig('meigee_unique_design_skin4');
		break;
		case 'meigee_unique_productpage':
		    return Mage::getStoreConfig('meigee_unique_productpage');
		break;
		case 'meigee_unique_sidebar':
		    return Mage::getStoreConfig('meigee_unique_sidebar');
		break;
		case 'meigee_unique_headerslider':
		    return Mage::getStoreConfig('meigee_unique_headerslider');
		break;
		case 'meigee_unique_bgslider':
		    return Mage::getStoreConfig('meigee_unique_bgslider');
		break;
		case 'mediaurl':
		    return Mage::getBaseUrl('media') . 'images/';
		break;
		case 'ajax_general':
		    return Mage::getStoreConfig('ajax/general/enabled');
		break;
		case 'ajax_toolbar':
		    return Mage::getStoreConfig('ajax/ajaxtoolbar/enabled');
		break;
		case 'ajax_wishlistcompare':
		    return Mage::getStoreConfig('ajax/wishlistcompare/enabled');
		break;
 	}
 }

public function getProductLabels ($_product, $type) {
 	switch ($type) {
		case 'new':
		 	if (Mage::getStoreConfig('meigee_unique_general/productlabels/labelnew')):
				$from = $_product->getNewsFromDate();
				$to = new Zend_Date($_product->getNewsToDate());
				$now = new Zend_Date(Mage::getModel('core/date')->timestamp(time()));
				if (isset($from) && $to->isLater($now)): 
					return '<span class="label-new">'.$this->__('New').'</span>';
				else:
					return false;
				endif;
			else:
				return false;
			endif;
		break;
		case 'sale':
		    if(Mage::getStoreConfig('meigee_unique_general/productlabels/labelonsale')):
				$_finalPrice = MAGE::helper('tax')->getPrice($_product, $_product->getFinalPrice());
				$_regularPrice = MAGE::helper('tax')->getPrice($_product, $_product->getPrice());
				if ($_regularPrice != $_finalPrice):
					if (Mage::getStoreConfig('meigee_unique_general/productlabels/salepercentage')):
						$getpercentage = number_format($_finalPrice / $_regularPrice * 100, 2);
						$finalpercentage = 100 - $getpercentage;
						return '<div class="label-sale percentage">- '.number_format($finalpercentage, 0).'%</div>';
					else:
						return '<div class="label-sale">'.$this->__('Sale').'</div>';
					endif;
				else:
					return false;
				endif;
			else:
				return false;
			endif;
		break;
	}
 	
 }

public function getProductOnlyXleft ($_product){
	if(Mage::getStoreConfig('meigee_unique_general/productlabels/labelonlyxleft')){
		$stockThreshold = Mage::getStoreConfig('cataloginventory/options/stock_threshold_qty');
		$productQty = round(Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getQty());
		if($productQty != 0 and $productQty < $stockThreshold){
			return '<div class="availability-only">< '.($productQty+1).' <p>'.$this->__('Left').'</p></div>';
		}else{
			return false;
		}
	}else{
		return false;
	}
}
 
 public function isNew($product)
	{
		return $this->_nowIsBetween($product->getData('news_from_date'), $product->getData('news_to_date'));
	}
public function isOnSale($product)
{
	$specialPrice = number_format($product->getFinalPrice(), 2);
	$regularPrice = number_format($product->getPrice(), 2);
	
	if ($specialPrice != $regularPrice)
		return true;
	else
		return false;
}

 public function prevnext ($product) {
 	if ($product->getUniquePrprevnext() < 2 ):
		$prevnext = $this->getThemeOptionsUnique('meigee_unique_productpage');
		if ($product->getUniquePrprevnext() == 1 or $prevnext['general']['prevnext'] == 'prevnext'):
		
		 	$_helper = Mage::helper('catalog/output');
			$_product = $product->getProduct();
			$prev_url = $next_url = $url = $product->getProductUrl();
			 
			if (Mage::helper('catalog/data')->getCategory()) {
				$category = Mage::helper('catalog/data')->getCategory();
			} else {
				$_ccats = Mage::helper('catalog/data')->getProduct()->getCategoryIds();
				if(isset($_ccats[0])){
					$category = Mage::getModel('catalog/category')->load($_ccats[0]);
				}else{
					return false;
				}
			}
			 
			$children = $category->getProductCollection();
			$_count = is_array($children) ? count($children) : $children->count();
			if ($_count) {
			foreach ($children as $product) {
			$plist[] = $product->getId();
			}
			 
			/**
			* Determine the previous/next link and link to current category
			*/
			$current_pid  = Mage::helper('catalog/data')->getProduct()->getId();
			$curpos   = array_search($current_pid, $plist);
			// get link for prev product
			$previd   = isset($plist[$curpos+1])? $plist[$curpos+1] : $current_pid;
			$product  = Mage::getModel('catalog/product')->load($previd);
			$prevpos  = $curpos;
			while (!$product->isVisibleInCatalog()) {
			$prevpos += 1;
			$nextid   = isset($plist[$prevpos])? $plist[$prevpos] : $current_pid;
			$product  = Mage::getModel('catalog/product')->load($nextid);
			}
			$prev_url = $product->getProductUrl();
			// get link for next product
			$nextid   = isset($plist[$curpos-1])? $plist[$curpos-1] : $current_pid;
			$product  = Mage::getModel('catalog/product')->load($nextid);
			$nextpos  = $curpos;
			while (!$product->isVisibleInCatalog()) {
			$nextpos -= 1;
			$nextid   = isset($plist[$nextpos])? $plist[$nextpos] : $current_pid;
			$product  = Mage::getModel('catalog/product')->load($nextid);
			}
			$next_url = $product->getProductUrl();
			}
			
			$html ='';
		    if ($url <> $next_url): 
				$html = '<a class="product-prev" title="' . $this->__('Previous Product') . '" href="' . $next_url . '"><i class="fa fa-angle-left"></i></a>';
		    endif;
			if ($url <> $prev_url):
		        $html .= '<a class="product-next" title="' . $this->__('Next Product') . '" href="' . $prev_url . '"><i class="fa fa-angle-right"></i></a>';
		    endif;

		    return $html;
		else: 
			return false;
		endif;
	else: 
		return false;
	endif;
 }

	public function isActive($attribute, $value){
	    $col = Mage::getModel('cms/block')->getCollection();
	    $col->addFieldToFilter($attribute, $value);
	    $item = $col->getFirstItem();
	    $id = $item->getData('is_active');
		if($id == 1){
			return true;
		}else{
			return false;
		}
	}

	public function switchHeader() {
		$headertype = $this->getThemeOptionsUnique('meigee_unique_general');
		if ($headertype['header_and_menu']['headertype'] == 1){
			return 'page/html/header_2.phtml';
		} elseif ($headertype['header_and_menu']['headertype'] == 2){
			return 'page/html/header_3.phtml';
		}
		return 'page/html/header.phtml';
	}
	
	public function switchTopLinks() {
		$topLinks = $this->getThemeOptionsUnique('meigee_unique_general');
		if ($topLinks['header_and_menu']['toplinks'] == 1){
			return 'page/template/links_2.phtml';
		}
		return 'page/template/links.phtml';
	}
	
	public function switchCart() {
		$cartType = $this->getThemeOptionsUnique('meigee_unique_general');
		$cart_qty = (int) Mage::getModel('checkout/cart')->getQuote()->getItemsQty();
		if($cart_qty){
			if($cartType['layout']['cartpage'] == 'cart_new_default') {
				return 'checkout/cart_2.phtml';
			} else {
				return 'checkout/cart.phtml';
			}
		} else {
			return 'checkout/cart/noItems.phtml';
		}
	}
	
	public function switchGrid() {
		$switchGrid = $this->getThemeOptionsUnique('meigee_unique_general');
		if ((int)$switchGrid['layout']['responsiveness'] !== 1):
			return 'css/grid_' . $switchGrid['layout']['responsiveness'] . '.css';

		endif;
		return 'css/grid_responsive.css';
	}

	public function switchGridClass() {
		$switchGrid = $this->getThemeOptionsUnique('meigee_unique_general');
		if ((int)$switchGrid['layout']['responsiveness'] == 1008):
			return 'no-responsive';
		endif;
	}
	
	public function fancySwitcher(){
		$fancy = $this->getThemeOptionsUnique('meigee_unique_general');
		if (($fancy['fancybox']['fancybox_status'] == 1) or ($this->getThemeOptionsUnique('ajax_general') == 1) or ($this->getThemeOptionsUnique('ajax_toolbar') == 1) or ($this->getThemeOptionsUnique('ajax_wishlistcompare') == 1)):
			return 'css/fancybox.css';
		endif;
	}

	public function getPaternClass (){
		$skin_design = Mage::getStoreConfig('meigee_unique_general/design/skin_design');
		$patern = $this->getThemeOptionsUnique('meigee_unique_design'.$skin_design.'');
		return $patern['appearance']['patern'];
	}

	public function getSidebarPos (){
		$sidePos = $this->getThemeOptionsUnique('meigee_unique_general');
		return $sidePos['productlisting']['sidebar'];
	}

	public function fancySwitcherJs(){
		$fancy = $this->getThemeOptionsUnique('meigee_unique_general');
		if (($fancy['fancybox']['fancybox_status'] == 1) or ($this->getThemeOptionsUnique('ajax_general') == 1) or ($this->getThemeOptionsUnique('ajax_toolbar') == 1) or ($this->getThemeOptionsUnique('ajax_wishlistcompare') == 1)):
			return 'js/jquery.fancybox.pack.js';
		endif;
	}

	public function HexToRGB($hex) {
	    //$hex = srt_replace("#", "", $hex);
	    $color = '';
	    if(strlen($hex) == 3) {
		    $color .= hexdec(substr($hex, 0, 1) . $r) . ',';
		    $color .= hexdec(substr($hex, 1, 1) . $g) . ',';
		    $color .= hexdec(substr($hex, 2, 1) . $b);
	    }
	    else if(strlen($hex) == 6) {
		    $color .= hexdec(substr($hex, 0, 2)) . ',';
		    $color .= hexdec(substr($hex, 2, 2)) . ',';
		    $color .= hexdec(substr($hex, 4, 2));
	    }
	    
	    return $color;
    }

	 public function getIcon ($type) {
		$skin_design = Mage::getStoreConfig('meigee_unique_general/design/skin_design');
		return '<i class="fa '. Mage::getStoreConfig('meigee_unique_design'.$skin_design.'/icons/'. $type) .'"></i>';
    }
}
?>