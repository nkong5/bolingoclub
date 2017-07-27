<?php
/**
 * export orders
 *
 * actindo Faktura/WWS connector
 * 
 * @package actindo
 * @author  atlantis media GmbH <info@atlantismedia.de>
 * @version $Revision: 1.3 $
 * @copyright Copyright (c) 2009, atlantis media GmbH (Haferweg 26, 22769 Hamburg, Deutschland)
*/


function export_orders_count() {
	
	$response = new ShopOrdersCountResponse();

	$MageResult = Mage::getResourceModel('sales/order_collection');
	
	$response->set_count((int)count($MageResult));

	return $response;
	
}



function export_orders_list($request) {
	
	$response = new ShopOrdersListResponse();
	$search_request = $request->search_request();
	
	$sal_map = actindo_get_salutation_map();
	
	$orders = array();
	
	if(is_object($search_request)) {
		$request = $search_request->toArray();
	}
	if($request['limit']<=0)
		$request['limit']=50;
	if($request['start']<=0)
		$request['start']=1;
	if($request['sortOrder']=='')
		$request['sortOrder']='DESC';
		
	$MageResult = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('*')
            ->setOrder('entity_id', $request['sortOrder'])
            ->setPageSize($request['limit'])
            ->setCurPage($request['start']);

	$orderIncrementIdExclude = array('100003839','100003838','100003837','100003836','100003835','100003834','100003833','100003832','100003831','100003830','100003829','100003828','100003827','100003826','100003825','100003824','100003823','100003822','100003821','100003820','100003819','100003818','100003817','100003816','100003815','100003814','100003813','100003812','100003811','100003810','100003809','100003808','100003807','100003806','100003805','100003804','100003803','100003802','100003801','100003800','100003799','100003798','100003797','100003796','100003795','100003794','100003793','100003792');

	$order_count = 0;
	foreach($MageResult as $order) {
		$orderIncrementId = $order->getIncrementId();

		if (in_array($orderIncrementId, $orderIncrementIdExclude)) {
			continue;
		}

		$actindoorder = $response->add_orders();

		$actindoorder->set_order_id($order->increment_id);
		$actindoorder->set_external_order_id($order->entity_id);
		if($order->customer_id == 0) {
			$actindoorder->set__customers_id(1000000+$order->entity_id);
		} else {
			$actindoorder->set__customers_id($order->customer_id);
		}
		$actindoorder->set_deb_kred_id(0);
		
		$customer = new ShopCustomer();
		if($order->customer_id == 0) {
			$customer->set__customers_id(1000000+$order->entity_id);
		} else {
			$customer->set__customers_id($order->customer_id);
		}
		$customer->set_deb_kred_id(0);
		
		$billingAddress = $order->getBillingAddress();
		$shippingAddress = $order->getShippingAddress();
		
		//Rechnungsadresse
		$customer_address = new ShopCustomerAddress();
		$customer_address->set_anrede(isset($sal_map[$billingAddress->prefix]) ? $sal_map[$billingAddress->prefix] : $billingAddress->prefix);
		$customer_address->set_kurzname(!empty($billingAddress->company) ? $billingAddress->company : $billingAddress->firstname.' '.$billingAddress->lastname);
		$customer_address->set_name($billingAddress->lastname);
		$customer_address->set_vorname($billingAddress->firstname);
		$customer_address->set_firma($billingAddress->company);
		$customer_address->set_adresse($billingAddress->street);
		$customer_address->set_ort($billingAddress->city);
		$customer_address->set_plz($billingAddress->postcode);
		$customer_address->set_land($billingAddress->country_id);
		$customer_address->set_tel($billingAddress->telephone);
		$customer_address->set_email($order->customer_email);
		$customer->set_address($customer_address);

		//Lieferadresse
		$customer->set_delivery_as_customer(0);
		$delivery_address = new ShopCustomerAddress();
		$delivery_address->set_anrede(isset($sal_map[$shippingAddress->prefix]) ? $sal_map[$shippingAddress->prefix] : $shippingAddress->prefix);
		$delivery_address->set_kurzname(!empty($shippingAddress->company) ? $shippingAddress->company : $shippingAddress->firstname.' '.$shippingAddress->lastname);
		$delivery_address->set_name($shippingAddress->lastname);
		$delivery_address->set_vorname($shippingAddress->firstname);
		$delivery_address->set_firma($shippingAddress->company);
		$delivery_address->set_adresse($shippingAddress->street);
		$delivery_address->set_ort($shippingAddress->city);
		$delivery_address->set_plz($shippingAddress->postcode);
		$delivery_address->set_land($shippingAddress->country_id);
		$delivery_address->set_tel($shippingAddress->telephone);
		$delivery_address->set_email($order->customer_email);
		$customer->set_delivery_address($delivery_address);

		//Zahlart
		$verfmap = _act_get_paymentmethods();

        if($order instanceof Mage_Sales_Model_Order) {
			try{
				$paymentmethod = $verfmap[$order->getPayment()->getMethodInstance()->getCode()];
			} catch (Exception $e) {
				$orderCustomerName = $order->getCustomerName();
				throw new Exception("Order id $orderIncrementId with customer name $orderCustomerName is erroneous");
			}
        }

        if (!isset($paymentmethod)) {
            $paymentmethod = 'VK';
        }

		$customer->set_verf($paymentmethod);
		
		$actindoorder->set_customer($customer);
		
		$dateTimeArray = explode(" ",$order->created_at);
		$actindoorder->set_webshop_order_date($dateTimeArray[0]." ");
		$actindoorder->set_webshop_order_time($dateTimeArray[1]);

		$actindoorder->set_bill_date($dateTimeArray[0]." ");
		$actindoorder->set_val_date($dateTimeArray[0]." ");

		$payment = new OrderPayment();
		$actindoorder->set_payment($payment);

		$actindoorder->set_currency($order->order_currency_code);
		//$actindoorder->set_currency_value();

		$actindoorder->set_netto($order->base_subtotal);
		//$actindoorder->set_netto2();

		/*Rabattformat von Magento kann hier nicht an Actindo �bertragen werden => L�sung �ber Hilfspodukt
		 * if($order->base_discount_amount > 0) {
			$rabatt = new ShopOrderRabatt();
			$rabatt->set_rabatt_type('betrag');
			$rabatt->set_rabatt_betrag(round($order->base_discount_amount, 2));
			$actindoorder->set_rabatt($rabatt);
		}*/
		$actindoorder->set_saldo($order->base_grand_total);

  		$orderfolders = _act_get_orderfolders();
  		$folder_to_folders_id = array_flip( $orderfolders );
		$actindoorder->set_orders_status( $folder_to_folders_id[$order->status] );

		$order_count++;

	}
	$response->set_count($order_count);
 
	return $response;
  
}



function export_orders_positions($request) {

	$response = new ShopOrdersPositionsResponse();

	//Artikel
	$orderItemsCollection = Mage::getModel('sales/order_item')->getCollection();
	$orderItemsCollection->getSelect()
				->joinLeft(Array('orders' => Mage::getSingleton('core/resource')->getTableName('sales/order')), '`main_table`.`order_id`=`orders`.`entity_id`', '')
				->where('`orders`.`increment_id`="'.$request->order_id().'"');
	
	$order = Mage::getModel('sales/order')->loadByIncrementId($request->order_id());
				
	$price = false;
	$pos_count = 0;
	foreach($orderItemsCollection as $article) {
		
		//Skip Configurable Product, but save price for connected simple product
		if ($article->product_type == 'configurable'){
			unset($prodArray,$prodSKU,$p,$prodID,$product);
			$prodArray = unserialize($article->product_options);
			foreach($prodArray as $key=>$val) {
				if($key=='simple_sku')
					$prodSKU = $val;
			}
			$price[$prodSKU] = ($article->row_total + $article->tax_amount) / $article->qty_ordered;
		} elseif ($article->product_type == 'bundle'){
			//Skip Bundle Product
		} else {			 
			//Check for individual Options
			$options = unserialize($article->product_options);
			if(isset($options['options']) && is_array($options['options'])) {
				foreach($options['options'] as $option) {
					$optionString .= ' - '.$option['label'].': '.$option['print_value'];
				}
			}
			
			//Simple Product
			$pos_count++;	
			$actindoarticle = $response->add_positions();
			$actindoarticle->set_art_nr($article->sku);
			$actindoarticle->set_art_name($article->name.$optionString);			
			$actindoarticle->set_is_brutto(1);
			$actindoarticle->set_type('Lief');
			$actindoarticle->set_mwst((float)$article->tax_percent);
			$actindoarticle->set_menge((float)$article->qty_ordered);
			
			if(!isset($price[$article->sku])) {
				$price[$article->sku] = ($article->row_total + $article->tax_amount) / $article->qty_ordered;
			}			
			$actindoarticle->set_preis($price[$article->sku]);
			unset($price[$article->sku]);			
		}
	}	
	
	//Rabatt als Produkt
	if($order->base_discount_amount != 0) {
		$actindoarticle = $response->add_positions();
		$actindoarticle->set_art_nr("Rabatt");
		$actindoarticle->set_art_name($order->getDiscountDescription());
		$actindoarticle->set_preis((float)($order->base_discount_amount*-1));
		$actindoarticle->set_is_brutto(1);
		$actindoarticle->set_type('NLeist');
		$actindoarticle->set_mwst(0.00);
		$actindoarticle->set_menge(1.00);
	}
	
	//Versandkosten
	if($order->base_shipping_amount > 0) {
		$actindoarticle = $response->add_positions();
		$actindoarticle->set_art_nr("Versandkosten");
		
		//Zahlart
		$zahlartMap = _act_get_paymentmethods_names();

        if($order instanceof Mage_Sales_Model_Order) {
            $paymentmethod = $zahlartMap[$order->getPayment()->getMethodInstance()->getCode()];
        }

        if (!isset($paymentmethod)) {
            $paymentmethod = 'Sonstige';
        }
			
		$actindoarticle->set_art_name($paymentmethod.': '.$order->shipping_description);
		$actindoarticle->set_preis((float)$order->base_shipping_amount);
		$actindoarticle->set_is_brutto(1);
		$actindoarticle->set_type('NLeist');
		$actindoarticle->set_mwst(0.00);
		$actindoarticle->set_menge(1.00);
	}
	
	$response->set_n_pos($pos_count);

	return $response;
  
}


/**
 * Mapping der Anreden auf Herr/Frau
 */
function actindo_get_salutation_map() {
	$gender = array(
		'1' => 'Herr',
		'2' => 'Frau',
		'Mr' => 'Herr',
		'Mrs' => 'Frau',
		'Ms' => 'Frau',
	);
	return $gender;
}
?>