<?php
 /**
 *
 * @copyright triplesense
 * @author Munkh Balidar <m.balidar@triplesense.de>
 * @version 0.1
 * @package 
 *
 * last change: $Date: 23.04.13 $
 * last author: $Author: mbalidar $
 * svn path: $URL:  $
 * revision: $Rev: 9196 $
 *
 * $Id: IndexControllerTest.php $
 */


/**
 * 
 * @group local
 * @group local_vorwerk
 * @group vorwerk_epos
 * 
 */
class Kibithek_Home_FilterControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
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
//    public function indexAction()
//    {
//        ob_start();
//
//        $params = array(
//            'gender' => 'J',
//            'skill' => 'NZH',
//            'age' => '1',
//            'topic' => 'FUA'
//        );
//
//        Mage::app()->getFrontController()->getRequest()
//            ->setModuleName('home')
//            ->setControllerName('filter')
//            ->setActionName('index')
//            ->setParams($params)
//        ;
//
//        /**
//         * to enable Mage::app()->getFrontController()->dispatch() to run through,
//         * set the followng: Mage::getStoreConfig('web/url/redirect_to_base') = 0
//         * @see Mage_Core_Controller_Varien_Front::_checkBaseUrl()
//         *
//         * setting possibility: Mage::getModel('core/config')->saveConfig('web/url/redirect_to_base', 0);
//         */
//        Mage::app()->getFrontController()->dispatch();
//
//        $this->assertEquals(Mage::app()->getFrontController()->getRequest()->getModuleName() ,'home');
//        $this->assertEquals(Mage::app()->getFrontController()->getRequest()->getControllerName() ,'filter');
//        $this->assertEquals(Mage::app()->getFrontController()->getRequest()->getActionName() ,'index');
//
//        // get json and decode json from response
//        $result = Mage::app()->getFrontController()->getResponse()->getBody();
//        $this->assertInternalType('string', $result);
//
//        ob_get_clean();
//    }

    // TODO find way to run test of controller with two actions
    /**
     * @test
     */
    public function ajaxActionReturnsJSONIfRequestIsMissingItem()
    {
//        ob_start();

        $params = array(
            'gender' => 'J',
            'skill' => 'NZH',
            'age' => '1',
            'topic' => 'FUA'
        );

        Mage::app()->getFrontController()->init();

        Mage::app()->getFrontController()->getRequest()
            ->setModuleName('home')
            ->setControllerName('filter')
            ->setActionName('ajax')
            ->setParams($params)
        ;

        /**
         * to enable Mage::app()->getFrontController()->dispatch() to run through,
         * set the followng: Mage::getStoreConfig('web/url/redirect_to_base') = 0
         * @see Mage_Core_Controller_Varien_Front::_checkBaseUrl()
         *
         * setting possibility: Mage::getModel('core/config')->saveConfig('web/url/redirect_to_base', 0);
         */
        Mage::app()->getFrontController()->dispatch();

        // get json and decode json from response
        $json = Mage::app()->getFrontController()->getResponse()->getBody();
        $result = Mage::helper('core')->jsonDecode($json);
        $this->assertInternalType('array', $result);

//        ob_get_clean();
    }

}