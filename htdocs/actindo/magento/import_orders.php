<?php
/**
 * import orders, specifically: set status, etc
 *
 * actindo Faktura/WWS connector
 * 
 * @package actindo
 * @author  atlantis media GmbH <info@atlantismedia.de>
 * @version $Revision: 1.2 $
 * @copyright Copyright (c) 2009, atlantis media GmbH (Haferweg 26, 22769 Hamburg, Deutschland)
*/


function import_orders_set_status($request) {

	$response = new ShopOrderSetStatusResponse();
	$response->set_order_id($request->order_id());
	
	$states = _act_get_orderfolders();
	
	try {
		$order = Mage::getModel('sales/order')
			->loadByIncrementId($request->order_id())
			->setStatus($states[$request->status_id()])
			->addStatusToHistory($states[$request->status_id()], 'via Actindo')
			/*->setState($states[$request->status_id()])*/
			->save();
	
		$response->set_status_set(1);
	
		$send_cmt = $request->send_comments();
		$send_cmt = (bool)$send_cmt;
		$cmt = $request->comment();
		if(!is_null($cmt) && !empty($cmt)) {
			$order->addStatusToHistory($states[$request->status_id()], $cmt, $send_cmt)->save();
		}
	} catch(Exception $e) {
		die($e->getMessage());
	}
	return $response;
	
}


function import_orders_set_trackingcode($request) {
	
	$response = new ShopOrderSetTrackingcodeResponse();
	$response->set_order_id($request->order_id());

	$send_date = $request->send_date();

	$carrier = $request->shipper();

	$order = Mage::getModel('sales/order')->loadByIncrementId($request->order_id());
	$id = create($order, array(), utf8_encode('Ihre Bestellung wurde versendet. In Ihrem Kundenkonto können Sie den aktuellen Lieferstatus der Bestellung verfolgen.'), true, true);
	$allowedCarriers = getCarriers($order);
	if(array_key_exists($carrier,$allowedCarriers)) {
		$newTrack = addTrack($id,$carrier,'Tracking Code',$request->trackingcode());
	}
	$response->set_shipper_set(1);
	$response->set_trackingcode_set(1);

	$collection = Mage::getModel('sales/order')
		->loadByIncrementId($request->order_id())
		->setStatus('complete')
		->addStatusToHistory('complete', 'via Actindo')
		/*->setState('complete')*/
		->save();
	$response->set_send_date_set(1);

	return $response;
  
}


/**
 * Create new shipment for order (copied from magento core)
 *
 * @param string $orderIncrementId
 * @param array $itemsQty
 * @param string $comment
 * @param booleam $email
 * @param boolean $includeComment
 * @return string
 */
function create($order, $itemsQty = array(), $comment = null, $email = false, $includeComment = false) {

	/**
	 * Check order existing
	 */
	if (!$order->getId()) {
		die("Die Bestellung existiert nicht");
	}

	/**
	 * Check shipment create availability
	 */
	if (!$order->canShip()) {
		die("Bestellung kann nicht versendet werden");
	}

	$convertor   = Mage::getModel('sales/convert_order');
	$shipment    = $convertor->toShipment($order);

	foreach ($order->getAllItems() as $orderItem) {
		if (!$orderItem->getQtyToShip()) {
			continue;
		}
		if ($orderItem->getIsVirtual()) {
			continue;
		}
		$item = $convertor->itemToShipmentItem($orderItem);
		if (isset($itemsQty[$orderItem->getId()])) {
			$qty = $itemsQty[$orderItem->getId()];
		} else {
			$qty = $orderItem->getQtyToShip();
		}
		$item->setQty($qty);
		$shipment->addItem($item);
	}
	$shipment->register();
	$shipment->addComment($comment, $email && $includeComment);

	if ($email) {
		$shipment->setEmailSent(true);
	}

	$shipment->getOrder()->setIsInProcess(true);

	try {
		$transactionSave = Mage::getModel('core/resource_transaction')
			->addObject($shipment)
			->addObject($shipment->getOrder())
			->save();

		$shipment->sendEmail($email, ($includeComment ? $comment : ''));
	} catch (Mage_Core_Exception $e) {
		$shipment->sendEmail($email, ($includeComment ? $comment : ''));
		$collection = Mage::getModel('sales/order')
			->loadByIncrementId($order->getId())
			->setStatus('complete')
			->save();
		die("Data invalid: ".$e->getMessage());
	}

	return $shipment->getIncrementId();
	
}
    
    
/**
 * Add tracking number to order (copied from magento core)
 *
 * @param string $shipmentIncrementId
 * @param string $carrier
 * @param string $title
 * @param string $trackNumber
 * @return int
 */
function addTrack($shipmentIncrementId, $carrier, $title, $trackNumber) {

	$shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);

	if (!$shipment->getId()) {
		die("Es existiert kein Shipment-Datensatz.");
	}

	$carriers = getCarriers($shipment);

	if (!isset($carriers[$carrier])) {
		die("Falsche Lieferantenangabe");
	}

	$track = Mage::getModel('sales/order_shipment_track')
		->setNumber($trackNumber)
		->setCarrierCode($carrier)
		->setTitle($title);

	$shipment->addTrack($track);

	try {
		$shipment->save();
	} catch (Mage_Core_Exception $e) {
		die("Data invalid: ".$e->getMessage());
	}

	return $track->getId();
	
}
    
    
/**
 * Retrieve shipping carriers for specified order (copied from magento core)
 *
 * @param Mage_Eav_Model_Entity_Abstract $object
 * @return array
 */
function getCarriers($order) {

	/**
	 * Check order existing
	 */
	if (!$order->getId()) {
		throw new Exception("Die Bestellung existiert nicht", EIO);
	} else {	
		$carriers = array();
		$carrierInstances = Mage::getSingleton('shipping/config')->getAllCarriers(
			$order->getStoreId()
		);
	
		$carriers['custom'] = Mage::helper('sales')->__('Custom Value');
		foreach ($carrierInstances as $code => $carrier) {
			if ($carrier->isTrackingAvailable()) {
				$carriers[$code] = $carrier->getConfigData('title');
			}
		}
	
		return $carriers;
	}
	
} 
?>