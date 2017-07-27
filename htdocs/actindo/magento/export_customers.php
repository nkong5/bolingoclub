<?php
/**
 * export customers
 *
 * actindo Faktura/WWS connector
 *
 * @package actindo
 * @author  Patrick Prasse <pprasse@actindo.de>
 * @version $Revision: 148 $
 * @copyright Copyright (c) 2007, Patrick Prasse (Schneebeerenweg 26, D-85551 Kirchheim, GERMANY, pprasse@actindo.de)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/


function customers_count($request) {
	$response = new ShopCustomersCountResponse();

	$MageResult = Mage::getResourceModel('customer/customer_collection')->setOrder('entity_id', 'desc');
	$response->set_count((int)count($MageResult));

	foreach($MageResult as $row) {
		$response->set_max_deb_kred_id($row->entity_id);
		break;
	}

	return $response;
}


function customers_list($request) {

	$response = new ShopCustomersListResponse();
	$search_request = $request->search_request();

	$sal_map = actindo_get_salutation_map();
	$col_map = actindo_get_column_map();
	
	/*$p = $search_request->sortColName();
	if(empty($p))
		$search_request->set_sortColName('entity_id');

	$p = $search_request->sortOrder();
	if(empty($p))
		$search_request->set_sortOrder('desc');*/

	if(is_object($search_request)) {
		$requestData = $search_request->toArray();
	}
	if($requestData['limit']<=0)
		$requestData['limit']=50;
	if($requestData['start']<=0)
		$requestData['start']=1;
	if($requestData['sortOrder']=='')
		$requestData['sortOrder']='DESC';

	if(isset($col_map[$requestData['sortColName']]))
		$requestData['sortColName'] = $col_map[$requestData['sortColName']];
	else
		$requestData['sortColName'] = 'entity_id';

	$MageResult = Mage::getResourceModel('customer/customer_collection')
		->setOrder($requestData['sortColName'], $requestData['sortOrder']);
		//->setPageSize($requestData['limit']) TODO blättern noch nicht implementiert
		//->setCurPage($requestData['start'])

	foreach($MageResult as $res) {
		
		$customer = Mage::getModel('customer/customer')->load($res->entity_id);
		$address = $customer->getDefaultBillingAddress();
		
		if($request->just_list()) {
			$actindocustomer = array(
				//'deb_kred_id' => $customer->store_id.$customer->increment_id,
				'deb_kred_id' => 0,
				'anrede' => isset($sal_map[$customer->prefix]) ? $sal_map[$customer->prefix] : $customer->prefix,
				'kurzname' => $customer->lastname.' '.$customer->firstname,
				'firma' => $address->company,
				'name' => $customer->lastname,
				'vorname' => $customer->firstname,
				'adresse' => $address->billing_street,
				'plz' => $address->postcode,
				'ort' => $address->city,
				'land' => $address->country_id,
				'email' => $customer->email,
				'_customers_id' => $customer->entity_id,
			);
		} else {
			$actindocustomer = array(
				//'deb_kred_id' => $customer->store_id.$customer->increment_id,
				'deb_kred_id' => 0,
				'anrede' => isset($sal_map[$address->prefix]) ? $sal_map[$address->prefix] : $address->prefix,
				'kurzname' => !empty($address->company) ? $address->company : $address->firstname.' '.$address->lastname,
				'firma' => $address->company,
				'name' => $address->lastname,
				'vorname' => $address->firstname,
				'adresse' => $address->billing_street,
				'adresse2' => '',
				'plz' => $address->postcode,
				'ort' => $address->city,
				'land' => $address->country_id,
				'tel' => $address->telephone,
				'fax' => $address->fax,
				'tel2' => '',
				'mobiltel' => '',
				'ustid' => '',
				'email' => $customer->email,
				'url' => '',
				'print_brutto' => 1,
				'_customers_id' => $customer->entity_id,
				'currency' => 'EUR',
				'gebdat' => '',
			);

			$delivery_addresses = array();

			$addresses = $customer->getAddresses();
		
			foreach($addresses as $delivery) {
				$actindodelivery = array(
					'delivery_address_id' => (int)hexdec(substr($delivery->entity_id, 0, 3)),
					'kurzname' => !empty($delivery->company) ? $delivery->company : $delivery->firstname.' '.$delivery->lastname,
					'firma' => $delivery->company,
					'name' => $delivery->lastname,
					'vorname' => $delivery->firstname,
					'adresse' => $delivery->billing_street,
					'adresse2' => '',
					'plz' => $delivery->postcode,
					'ort' => $delivery->city,
					'land' => $delivery->country_id,
					'tel' => $delivery->telephone,
					'fax' => $delivery->fax,
					'email' => $customer->email,
				);
				$delivery_addresses[] = array_merge($actindocustomer, $actindodelivery);
			}

			$actindocustomer['delivery_addresses'] = $delivery_addresses;
		}

		$customers[] = $actindocustomer;
	}

	foreach($customers as $cust) {

		$customer = $response->add_customers();
		$customer->set_deb_kred_id($cust['deb_kred_id']);
		$customer->set__customers_id($cust['_customers_id']);
		if(!empty($cust['gebdat']) && $cust['gebdat'] != '0000-00-00')
			$customer->set_gebdat( $cust['gebdat'] );

		$address = new ShopCustomerAddress();
		$address->set_delivery_address_id(0);
		$address->fromArray($cust);
		$customer->set_address($address);

		$customer->set_delivery_as_customer(1);

		foreach($cust['delivery_addresses'] as $_addr) {
			$addr = $customer->add_other_delivery_addresses();
			$addr->fromArray($_addr);
		}
		
	}

	$response->set_count((int)count($MageResult));
	return $response;

}


/**
 * Mapping der Anreden auf Herr/Frau
 */
function actindo_get_column_map() {
	$columns = array(
		'_customers_id' => 'entity_id',
		'list_deb_kred_id' => 'increment_id',
		'kurzname' => 'lastname',
		'land' => 'country_id',
		'email' => 'email',
	);
	return $columns;
}
?>