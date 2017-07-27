<?php
/**
 * Created by PhpStorm.
 * User: nkongme
 * Date: 01.03.14
 * Time: 22:27
 */

class Kibithek_Customer_Model_GuestOrderEmailTest extends Kibithek_PHPUnit_TestCase
{

    /**
     * @test
     */
    public function sendMailForLastOrder() {
        $store = Mage::app()->getStore('de');
        $storeId = $store->getId();
        $connection = Mage::getSingleton('core/resource')->getConnection('core/read');
        $queryLastOrder = "select * from sales_flat_order where store_id = $storeId order by entity_id desc limit 1;";
        $id = (integer) $connection->fetchOne($queryLastOrder);
        if ($id) {
            $order = Mage::getModel('sales/order');
            $order->load($id);
            $result = $order->sendNewOrderEmail();
            $this->assertInstanceOf('Mage_Sales_Model_Order', $result);
        }else{
            $this->assertEquals(0, $id);
        }
    }


}