<?php
/**
 * include various files
 *
 * actindo Faktura/WWS connector
 * 
 * @package actindo
 * @author  atlantis media GmbH <info@atlantismedia.de>
 * @version $Revision: 1.2 $
 * @copyright Copyright (c) 2009, atlantis media GmbH (Haferweg 26, 22769 Hamburg, Deutschland)
*/


define('ACTINDO_SHOP_CHARSET', 'UTF-8');
define('MAGENTO_BASEPATH','/'); //If Magento is not installed in webservers document root, this path has to be changed

ini_set('memory_limit', '512M'); //Recommended Memory Limit (if ini_set is disabled, this should be set in php.ini / vhost configuration)

error_reporting(E_ALL & ~E_NOTICE);
set_error_handler('actindo_error_handler');

require_once('util.php');
require_once('import.php');
require_once('export.php');

//Magento
require_once($_SERVER['DOCUMENT_ROOT'].MAGENTO_BASEPATH.'app/Mage.php');
Mage::app();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); 

function actindo_authenticate($user, $pass) {

	//$user = Mage::getModel('admin/user'); //TODO Passwort kommt verschl�sselt aus Actindo, damit kann Magento nichts anfangen
	//$user->login($user, $pass);
	//if ($user->getId()) {
		return TRUE;
	//}

	//return "Benutzername / Passwort inkorrekt.";

}


function categories_get($params) {

	$response = new ShopCategoriesResponse();

	$categories = getStoreCategories(1); //1=ROOT

	foreach($categories as $category) {
		createCategoryTree($response, $category);
	}
	
	return $response;
}


function createCategoryTree(&$response, $category, $level = 0, $last = false) {
		
	if(!$category->getIsActive()) {
		return;
	}
		
	$children = $category->getChildren();
	$hasChildren = $children && $children->count();
		
	$actCategory = $response->add_categories();
	$actCategory->set_categories_id((string)$category->getId());
	$actCategory->set_categories_name($category->getName());

	if($hasChildren) {
		$j = 0;
		foreach ($children as $child) {
			if ($child->getIsActive ()) {
				createCategoryTreeRecursive($actCategory, $child, $level + 1, ++ $j >= 0, $category->getId());
			}
		}
	}
		
	return $menuArray;
}


function createCategoryTreeRecursive(&$actCategory, $category, $level = 0, $last = false, $parent = 0) {
		
	if(!$category->getIsActive()) {
		return;
	}
		
	$children = $category->getChildren();
	$hasChildren = $children && $children->count();

	$actCategorySub = $actCategory->add_children();
	$actCategorySub->set_categories_id((string)$category->getId());
	$actCategorySub->set_categories_name($category->getName());
	$actCategorySub->set_parent_id((string)$parent); //TODO
	
	if($hasChildren) {
		$j = 0;
		foreach ($children as $child) {
			if ($child->getIsActive ()) {
				createCategoryTreeRecursive($actCategorySub, $child, $level + 1, ++ $j >= 0, $category->getId());
			}
		}
	}
		
	return $menuArray;
}


function getStoreCategories($parent=null, $sorted=false, $asCollection=false, $toLoad=true) {
	if(!$parent)
		$parent = Mage::app()->getStore()->getRootCategoryId();

	$category = Mage::getModel('catalog/category');
	if(!$category->checkId($parent)) {
		if($asCollection) {
			return new Varien_Data_Collection();
		}
		return array();
	}

	$recursionLevel = max(0, (int) Mage::app()->getStore()->getConfig('catalog/navigation/max_depth'));

	$tree = $category->getTreeModel();

	$nodes = $tree->loadNode($parent)
		->loadChildren($recursionLevel)
		->getChildren();

	$tree->addCollectionData(null, $sorted, $parent, $toLoad, true);

	if ($asCollection) {
		return $tree->getCollection();
	} else {
		return $nodes;
	}
}


//TODO noch nicht implementiert f�r magento
function _get_debitnote_paymentdata( $oxuserID ) {
  require_once getShopBasePath() . 'core/oxuserpayment.php';
  $ouPayment = new oxUserPayment();
  $key = $ouPayment->getPaymentKey();
  
  $oUP = act_db_query( $q="select oxid, oxuserid, oxpaymentsid, DECODE( oxvalue, '".$key."' ) as oxvalue from oxuserpayments where oxuserid = " . oxDb::getDb()->quote( $oxuserID ) . " AND oxpaymentsid='oxiddebitnote'");
  while( $_res = act_db_fetch_assoc( $oUP ))  {
    $oPayment = $_res['oxvalue'];
  }
  
  $oPayment = explode( '@@', $oPayment );
  foreach( $oPayment as $key => $val )
  {
    if( strlen( $val ) ) {
      $tmp = explode( "__", $val );
      $ret[$tmp[0]] = $tmp[1];
    }
  }
  return $ret;
}



//TODO noch nicht implementiert f�r magento
function category_action($params) {
	
}



/**
 * @done
 */
function settings_get($params) {
	
	$response = new ShopSettingsResponse();
	$response->set_timestamp(time());

	//lang
	$lang = $response->add_languages();
	$lang->set_language_code('de');
	$lang->set_language_name('deutsch');
	$lang->set_language_id(1);
	$lang->set_is_default(true);
	
	// manufacturers
	$product = Mage::getModel('catalog/product');    
	$attributes = Mage::getResourceModel('eav/entity_attribute_collection')
		->setEntityTypeFilter($product->getResource()->getTypeId())
		->addFieldToFilter('attribute_code', 'manufacturer') // This can be changed to any attribute code
		->load(false);

	$attribute = $attributes->getFirstItem()->setEntity($product->getResource());
	$manufacturers = $attribute->getSource()->getAllOptions(false);
	
	foreach($manufacturers as $vendor) {
		$man = $response->add_manufacturers();
		$man->set_manufacturers_id($vendor['value']);
		$man->set_manufacturers_name($vendor['label']);
	}

	// orders_status	
	$MageStates = _act_get_orderstatus();
	
	$i=0;
	foreach($MageStates as $key => $value) {
		$i++;
		$os = $response->add_orders_status( );
		$os->set_id($i);
		$os->set_name($value);
	}
	
	// TODO Cross-Selling, Zubeh�r & Artikel dazu	

	return $response;
  
}



function _act_get_orderfolders($reverse = FALSE) {
	
	$MageStates = _act_get_orderstatus();
	
	$orderfolders = array();
	foreach($MageStates as $key => $value) {
		$i++;
		if($reverse == TRUE)
			$orderfolders[$i] = $value;
		else
			$orderfolders[$i] = $key;
	}
	
	return $orderfolders;
  
}


function _act_get_orderstatus() {

	//Get Statuses
	$statuses = Mage::getSingleton('sales/order_config')->getStatuses();
	
	//Default German Translations
	if(is_file($_SERVER['DOCUMENT_ROOT'].MAGENTO_BASEPATH.'app/locale/de_DE/Mage_Sales.csv')) {
		$handle = fopen($_SERVER['DOCUMENT_ROOT'].MAGENTO_BASEPATH.'app/locale/de_DE/Mage_Sales.csv',"r");
		while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
			$arr[] = $data;
		}
		fclose($handle);
	}
	
	//Module German Translations
	//UOS Payment
	if(is_file($_SERVER['DOCUMENT_ROOT'].MAGENTO_BASEPATH.'app/locale/de_DE/Mage_Uos.csv')) {
		$handle = fopen($_SERVER['DOCUMENT_ROOT'].MAGENTO_BASEPATH.'app/locale/de_DE/Mage_Uos.csv',"r");
		while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
			$data[1] = html_entity_decode('UOS '.$data[1]);
			$arr[] = $data;
		}
		fclose($handle);	
	}
	
	//Translate Statuses
	$outStatus = $statuses;
	if(is_array($statuses)) {
		foreach($statuses as $key => $status) {
			if(is_array($arr)) {
				foreach($arr as $trans) {
					if($trans[0]==$status) {
						$outStatus[$key] = $trans[1];
					}
				}
			}
		}
	}
	
	return $outStatus;
	
}


function _act_get_paymentmethods() {
	
	$MagePaymentAllowed = array();
	/* diese funktion macht auf einigen magento systemen probleme, daher erstmal deaktiviert
	$methods = Mage::getSingleton('payment/config')->getActiveMethods();
	foreach($methods as $paymentCode => $paymentModel) {
		$MagePaymentAllowed[] = $paymentCode;
	}*/

	$ShowAllPaymentMethods = true;

	//Standard Zahlarten:
	if(in_array('googlecheckout',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['googlecheckout'] = 'VK'; //GoogleCheckout, prepaid
	if(in_array('checkmo',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['checkmo'] = 'VK'; //Vorkasse (transfer prepaid)
	if(in_array('bankpayment',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['bankpayment'] = 'VK'; //Vorkasse (transfer prepaid)
	if(in_array('ccsave',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['ccsave'] = 'KK'; //Kredikarte
	if(in_array('purchaseorder',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['purchaseorder'] = 'U'; //Rechnung
	if(in_array('free',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['free'] = ''; //Gratis
	if(in_array('purchaseorder',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['purchaseorder'] = 'L'; //Lastschrift
	if(in_array('authorizenet',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['authorizenet'] = 'KK'; //KK �ber Authorize.net
	if(in_array('verisign',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['verisign'] = 'KK'; //KK �ber Payflow Pro
	if(in_array('paypal_standard',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['paypal_standard'] = 'PP'; //Diverse PayPal Zahlarten
	if(in_array('paypal_express',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['paypal_express'] = 'PP';
	if(in_array('paypal_direct',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['paypal_direct'] = 'PP';
	if(in_array('paypaluk_direct',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['paypaluk_direct'] = 'PP';
	if(in_array('paypaluk_express',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['paypaluk_express'] = 'PP';
	if(in_array('amazonpayments_cba',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['amazonpayments_cba'] = 'AZ'; //Amazon Payments
	if(in_array('amazonpayments_asp',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['amazonpayments_asp'] = 'AZ'; //Amazon Payments

	//Bekannte Zahlmodule:
	if(in_array('cashondelivery',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['cashondelivery'] = 'NN'; //Nachnahme
	if(in_array('sofortueberweisung',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['sofortueberweisung'] = 'SU'; //Sofortüberweisung
	if(in_array('paycode',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['paycode'] = 'VK'; // Vorkasse mit Sofortüberweisung Paycode
	if(in_array('uosccard',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['uosccard'] = 'KK'; //UOS Kreditkarte
	if(in_array('uosdebit',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['uosdebit'] = 'L'; //UOS Lastschrift
	if(in_array('uosgiropay',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['uosgiropay'] = 'GP'; //UOS GiroPay
	if(in_array('uosdirectebanking',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['uosdirectebanking'] = 'SU'; //UOS Sofortüberweisung
	if(in_array('uosprepaid',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['uosprepaid'] = 'VK'; //UOS Vorkasse
	if(in_array('uosinvoice',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['uosinvoice'] = 'U'; //UOS Rechnung
	if(in_array('utstandard',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['utstandard'] = 'UT'; //United Transfer Direkt
	if(in_array('utccard',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['utccard'] = 'KK'; //United Transfer Kreditkarte
	if(in_array('utdebit',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['utdebit'] = 'L'; //United Transfer Lastschrift
	if(in_array('utgiropay',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['utgiropay'] = 'GP'; //United Transfer GiroPay
	if(in_array('utdirectebanking',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['utdirectebanking'] = 'SU'; //United Transfer Sofortüberweisung
	if(in_array('utprepaid',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['utprepaid'] = 'VK'; //United Transfer Vorkasse	
	if(in_array('payone_cc',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['payone_cc'] = 'KK'; //Payone Kreditkarte
	if(in_array('payone_elv',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['payone_elv'] = 'L'; //Payone Lastschrift
	if(in_array('payone_vor',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['payone_vor'] = 'VK'; //Payone Vorkasse
	if(in_array('payone_rec',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['payone_rec'] = 'U'; //Payone Rechnung
	if(in_array('payone_sb',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['payone_sb'] = 'SU'; //Payone Sofortüberweisung
	if(in_array('payone_wlt',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['payone_wlt'] = 'PP'; //Payone PayPal	
	if(in_array('invoice',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['invoice'] = 'U'; //Mxperts Invoice
	if(in_array('cash',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['cash'] = 'B'; //Bezahlung bei Abholung
	if(in_array('clickandbuy',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['clickandbuy'] = 'CB'; //Click & Buy
	if(in_array('ipayment_cc',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['ipayment_cc'] = 'KK'; //iPayment Kreditkarte
	if(in_array('ipayment_elv',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['ipayment_elv'] = 'L'; //iPayment elektronische Lastschrift

    // heidelpay payment methods
	if(in_array('hcdpal',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['hcdpal'] = 'PP'; //heidelpay pay pal
	if(in_array('hcdcc',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['hcdcc'] = 'KK'; //heidelpay credit card
	if(in_array('hcdsu',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['hcdsu'] = 'SU'; //heidelpay Direct E-Banking - Sofortueberweisung
	if(in_array('hcdgp',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['hcdgp'] = 'GP'; //heidelpay giropay

	return $MagePayment;
	
}



function _act_get_paymentmethods_names() {

	$MagePaymentAllowed = array();
	// actiondo statement: diese funktion macht lu auf einigen magento systemen probleme, daher erstmal deaktiviert
	$methods = Mage::getSingleton('payment/config')->getActiveMethods();
	foreach($methods as $paymentCode => $paymentModel) {
		$MagePaymentAllowed[] = $paymentCode;
	}
	$ShowAllPaymentMethods = true;

	//Standard Zahlarten:
	if(in_array('googlecheckout',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['googlecheckout'] = 'Vorkasse'; //GoogleCheckout, prepaid
	if(in_array('checkmo',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['checkmo'] = 'Vorkasse'; //Vorkasse (transfer prepaid)
	if(in_array('bankpayment',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['bankpayment'] = 'Vorkasse'; //Vorkasse (transfer prepaid)
	if(in_array('ccsave',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['ccsave'] = 'Kreditkarte'; //Kredikarte
	if(in_array('purchaseorder',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['purchaseorder'] = 'Rechnung'; //Rechnung
	if(in_array('free',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['free'] = ''; //Gratis
	if(in_array('purchaseorder',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['purchaseorder'] = 'Lastschrift'; //Lastschrift
	if(in_array('authorizenet',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['authorizenet'] = 'Kreditkarte'; //KK �ber Authorize.net
	if(in_array('verisign',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['verisign'] = 'Kreditkarte'; //KK �ber Payflow Pro
	if(in_array('paypal_standard',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['paypal_standard'] = 'PayPal'; //Diverse PayPal Zahlarten
	if(in_array('paypal_express',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['paypal_express'] = 'PayPal';
	if(in_array('paypal_direct',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['paypal_direct'] = 'PayPal';
	if(in_array('paypaluk_direct',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['paypaluk_direct'] = 'PayPal';
	if(in_array('paypaluk_express',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['paypaluk_express'] = 'PayPal';
	if(in_array('amazonpayments_cba',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['amazonpayments_cba'] = 'AmazonPayments'; //Amazon Payments
	if(in_array('amazonpayments_asp',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['amazonpayments_asp'] = 'AmazonPayments'; //Amazon Payments

	//Bekannte Zahlmodule:
	if(in_array('cashondelivery',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['cashondelivery'] = 'Nachnahme'; //Nachnahme
	if(in_array('sofortueberweisung',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['sofortueberweisung'] = 'Sofortüberweisung'; //Sofortüberweisung
	if(in_array('paycode',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['paycode'] = 'Vorkasse'; // Vorkasse mit Sofortüberweisung Paycode
	if(in_array('uosccard',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['uosccard'] = 'Kreditkarte'; //UOS Kreditkarte
	if(in_array('uosdebit',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['uosdebit'] = 'Lastschrift'; //UOS Lastschrift
	if(in_array('uosgiropay',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['uosgiropay'] = 'GiroPay'; //UOS GiroPay
	if(in_array('uosdirectebanking',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['uosdirectebanking'] = 'Sofortuberweisung'; //UOS Sofortüberweisung
	if(in_array('uosprepaid',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['uosprepaid'] = 'Vorkasse'; //UOS Vorkasse
	if(in_array('uosinvoice',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['uosinvoice'] = 'Rechnung'; //UOS Rechnung
	if(in_array('utstandard',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['utstandard'] = 'UnitedTransfer'; //United Transfer Direkt
	if(in_array('utccard',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['utccard'] = 'Kreditkarte'; //United Transfer Kreditkarte
	if(in_array('utdebit',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['utdebit'] = 'Lastschrift'; //United Transfer Lastschrift
	if(in_array('utgiropay',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['utgiropay'] = 'GiroPay'; //United Transfer GiroPay
	if(in_array('utdirectebanking',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['utdirectebanking'] = 'Sofortüberweisung'; //United Transfer Sofortüberweisung
	if(in_array('utprepaid',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['utprepaid'] = 'Vorkasse'; //United Transfer Vorkasse	
	if(in_array('payone_cc',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['payone_cc'] = 'Kreditkarte'; //Payone Kreditkarte
	if(in_array('payone_elv',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['payone_elv'] = 'Lastschrift'; //Payone Lastschrift
	if(in_array('payone_vor',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['payone_vor'] = 'Vorkasse'; //Payone Vorkasse
	if(in_array('payone_rec',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['payone_rec'] = 'Rechnung'; //Payone Rechnung
	if(in_array('payone_sb',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['payone_sb'] = 'Sofortüberweisung'; //Payone Sofortüberweisung
	if(in_array('payone_wlt',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['payone_wlt'] = 'PayPal'; //Payone PayPal	
	if(in_array('invoice',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['invoice'] = 'Rechnung'; //Mxperts Invoice
	if(in_array('cash',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['cash'] = 'Barverkauf'; //Bezahlung bei Abholung
	if(in_array('clickandbuy',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['clickandbuy'] = 'ClickandBuy'; //Click & Buy
	if(in_array('ipayment_cc',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['ipayment_cc'] = 'Kreditkarte'; //iPayment Kreditkarte
	if(in_array('ipayment_elv',$MagePaymentAllowed) || $ShowAllPaymentMethods)
		$MagePayment['ipayment_elv'] = 'Lastschrift'; //iPayment elektronische Lastschrift


    if(in_array('hcdpal',$MagePaymentAllowed) || $ShowAllPaymentMethods)
        $MagePayment['hcdpal'] = 'PayPal'; //heidelpay pay pal
    if(in_array('hcdcc',$MagePaymentAllowed) || $ShowAllPaymentMethods)
        $MagePayment['hcdcc'] = 'Kreditkarte'; //heidelpay credit card
    if(in_array('hcdsu',$MagePaymentAllowed) || $ShowAllPaymentMethods)
        $MagePayment['hcdsu'] = 'Sofortüberweisung'; //heidelpay Direct E-Banking - Sofortueberweisung
    if(in_array('hcdgp',$MagePaymentAllowed) || $ShowAllPaymentMethods)
        $MagePayment['hcdgp'] = 'GiroPay'; //heidelpay giropay
	return $MagePayment;
	
}


function _act_get_langid_to_langcode() {
	$langcodes = array();
    $langcodes[1] = 'de';
	return $langcodes;
}


function actindo_set_token($params) {
	return resp( array('ok'=>TRUE) );
}


function actindo_get_time($params) {
	
	$response = new ShopTimeResponse( );
  
	$time_database['datetime'] = date('Y-m-d H:i:s',time()+7200);
	$utctime_database = array('datetime'=> '');

	$response->set_time_server(date('Y-m-d H:i:s',time()+7200));
	$response->set_gmtime_server(gmdate('Y-m-d H:i:s'));
	$response->set_time_database($time_database['datetime']);
	$response->set_gmtime_database($utctime_database['datetime']);

	if(!empty($utctime_database['datetime'])) {
		$diff = strtotime($time_database['datetime']) - strtotime($utctime_database['datetime']);
	} else {
		$diff = strtotime(date('Y-m-d H:i:s',time()+7200)) - strtotime(gmdate('Y-m-d H:i:s'));
	}
	$response->set_diff_seconds($diff);
	$diff_neg = $diff < 0;
	$diff = abs($diff);
	$response->set_diff(($diff_neg ? '-':'').sprintf("%02d:%02d:%02d", floor($diff / 3600), floor( ($diff % 3600) / 60 ), $diff % 60 ));

	return $response;
  
}


function shop_get_connector_version(&$response) {
	
	$revision = '$Revision: 240 $';
	$response->set_revision($revision);
	$response->set_protocol_version('2.'.substr($revision, 11, -2));
	//$response->set_shop_type(act_get_shop_type()); //TODO Wenn auf "magento" gesetzt, gibt es einen Fehler, daher auskommentiert
	$response->set_shop_version(Mage::getVersion());
	$response->set_default_charset('UTF-8');
	
}


function act_shop_get_capabilities() {
	
	return array(
		'artikel_vpe' => 1,
		'artikel_shippingtime' =>1,
		'artikel_properties' => 1,
		'artikel_contents' => 1,
		'artikel_attributsartikel' => 1,
		'wg_sync' => 1,
		'multi_livelager' => 1,
	);
  
}


function actindo_get_cryptmode() {

	$str = "cryptmode=MD5";
	return $str;
  
}
?>