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
class Mage_Checkout_Block_Onepage_SuccessTest extends Diecrema_PHPUnit_TestCase
{
    
    /**
     * 
     * 
     * @test
     * 
     */
    public function testSuccessPagecontentDE ()
    {
        Mage::getDesign()->setArea('frontend') //Area (frontend|adminhtml)
            ->setPackageName('diecrema')
            ->setTheme('multilanguage')
        ;

        $locale = 'de_DE';
        Mage::app()->getLocale()->setLocaleCode($locale);
        Mage::getSingleton('core/translate')->setLocale($locale)->init('frontend', true);

        $block = Mage::app()->getLayout()->createBlock('checkout/onepage_success');
        $this->assertInstanceOf('Mage_Checkout_Block_Onepage_Success', $block);
        $block->setTemplate('checkout/success.phtml');

        $storeId = Mage::app()->getStore()->getId();
        $orderId = $this->_getReadConnection()->fetchOne("SELECT `entity_id` FROM `sales_flat_order` where `store_id` = $storeId ORDER BY `entity_id` DESC LIMIT 1");
        $order = Mage::getModel('sales/order')->load($orderId);
        $this->assertNotNull($order->getId()); 
        
        $block->setOrder($order);

        $content = $block->toHtml();
        $this->assertNotEmpty($content);
        $this->assertEmpty($content);
    }

}