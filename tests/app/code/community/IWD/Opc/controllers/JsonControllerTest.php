<?php
/**
 * Created by PhpStorm.
 * User: nkongme
 * Date: 29.10.13
 * Time: 14:19
 */

/**
 *
 * @group local
 * @group local_vorwerk
 * @group vorwerk_epos
 *
 */
class Vorwerk_Systempay_PaymentControllerTest extends Diecrema_PHPUnit_Controller_TestCase
{
    /**
     * Set up
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Tear down
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function testSaveOrderAction()
    {
        ob_start();

        $params = $this->_saveOrderParams();

        $frontController = Mage::app()->getFrontController();

        $frontController->getRequest()
            ->setModuleName('onepage')
            ->setControllerName('json')
            ->setActionName('saveOrder')
            ->setParams($params)
        ;

        Mage::getModel('core/config')->saveConfig('web/url/redirect_to_base', 0);

        /**
         * to enable Mage::app()->getFrontController()->dispatch() to run through,
         * set the followng: Mage::getStoreConfig('web/url/redirect_to_base') = 0
         * @see Mage_Core_Controller_Varien_Front::_checkBaseUrl()
         *
         * setting possibility: Mage::getModel('core/config')->saveConfig('web/url/redirect_to_base', 0);
         */
        $frontController->dispatch();

        $moduleName = $frontController->getRequest()->getModuleName();
        $controllerName = $frontController->getRequest()->getControllerName();
        $actionName = $frontController->getRequest()->getActionName();

        // get json and decode json from response
        $result = $frontController->getResponse()->getBody();
        $this->assertInternalType('string', $result);

        ob_get_clean();
    }

    protected function _saveOrderParams()
    {
        return array();
    }


    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockQuote()
    {
        $quote = $this->getMock('Mage_Sales_Model_Quote');
        $items = array();
        for ($i = 0; $i < 5; $i++) {
            $item = new Varien_Object();
            $item->setSku('sku_' . $i);
            $items[] = $item;
        }
        $quote->expects($this->any())
            ->method('getAllVisibleItems')
            ->will($this->returnValue($items));
        return $quote;
    }

}