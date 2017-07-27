<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nkongme
 * Date: 11.10.13
 * Time: 21:22
 * To change this template use File | Settings | File Templates.
 */

class Kibithek_Category_Model_ObserverTest extends Kibithek_PHPUnit_TestCase
{
    /**
     * @test
     */
    public function checkModel()
    {
        $model = Mage::getModel('kibithek_category/observer');
        $this->assertInstanceOf('Kibithek_Category_Model_Observer', $model);
    }

    /**
     * @test
     */
    public function homeControllerActionForward(){

        $params = array(
            'gender' => 'J',
            'skill' => 'NZH',
            'age' => '1',
            'topic' => 'FUA'
        );

        $forward = new Varien_Object();
        $forward->setRedirect(false);
        $forward->setModule(null);
        $forward->setController(null);
        $forward->setAction(null);
        $forward->setParams(null);

        $observer = new Varien_Event_Observer();
        $observer->setParams($params);
        $observer->setForward($forward);

        $model = Mage::getModel('kibithek_category/observer');
        $model->filterForwardToCatalog($observer);

        $module = $forward->getModule();
        $this->assertInternalType('string', $module);
        $controller = $forward->getController();
        $this->assertInternalType('string', $controller);
        $action = $forward->getAction();
        $this->assertInternalType('string', $action);
        $params = $forward->getParams();
        $this->assertInternalType('array', $params);

    }


    /**
     * @test
     * @expectedException Exception
     */
    public function homeControllerActionForwardThrowsExceptionIfRecordIsNotFound(){

        $params = array(
            'gender' => 'J',
            'skill' => 'NZH XXXX',
            'age' => '1',
            'topic' => 'FUA'
        );

        $forward = new Varien_Object();
        $forward->setRedirect(false);
        $forward->setModule(null);
        $forward->setController(null);
        $forward->setAction(null);
        $forward->setParams(null);

        $observer = new Varien_Event_Observer();
        $observer->setParams($params);
        $observer->setForward($forward);

        $model = Mage::getModel('kibithek_category/observer');
        $model->filterForwardToCatalog($observer);
    }

}