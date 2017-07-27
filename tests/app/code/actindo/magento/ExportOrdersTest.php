<?php

/**
 *
 *
 * @copyright triplesense
 * @author Joachim Schweisgut <j.schweisgut@triplesense.de>
 * @version 0.1
 * @package Vorwerk_Zanox
 *
 * last change: $Date: 2013-08-26 14:18:14 +0200 (Mon, 26 Aug 2013) $
 * last author: $Author: jschweisgut $
 * svn path: $URL: http://svn.dev/vorwerk/shop/trunk/tests/app/modules/local/Vorwerk/Zanox/Block/Checkout/SuccessTest.php $
 * revision: $Rev: 12184 $
 *
 * $Id: SuccessTest.php 12184 2013-08-26 12:18:14Z jschweisgut $
 */

/**
 * 
 * @group local
 * @group local_vorwerk
 * @group vorwerk_zanox
 * 
 */
class Actindo_Magento_ExportOrdersTest extends Diecrema_PHPUnit_TestCase
{
    
    /**
     * 
     * 
     * @test
     * 
     */
    public function testSuccessPagecontentDE ()
    {
        $paymentmethods = $this->getPaymentMethods();


        include_once '../../../../../htdocs/actindo/magento/export_orders.php';
        $request = array();
        $result = export_orders_list($request);
    }

    public function getPaymentMethods()
    {
       $payments =  Mage::getModel('payment/config')->getAllMethods();

        foreach ($payments as $paymentCode => $paymentModel) {
            $title = Mage::getStoreConfig('payment/' . $paymentCode . '/title');
            $methods[$paymentCode] = array(
                'title' => $title,
                'value' => $paymentCode,
            );
        }
        return $methods;
    }

    public function getOrderIncrementIds($request)
    {
        if ($request['limit'] <= 0)
            $request['limit'] = 50;
        if ($request['start'] <= 0)
            $request['start'] = 1;
        if ($request['sortOrder'] == '')
            $request['sortOrder'] = 'DESC';

        $MageResult = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('*')
            ->setOrder('entity_id', $request['sortOrder'])
            ->setPageSize($request['limit'])
            ->setCurPage($request['start']);

        foreach ($MageResult as $order) {
            $orderIncrementId = $order->getIncrementId();
            $incrementIds[] = $orderIncrementId;
        }
        return
    }
}