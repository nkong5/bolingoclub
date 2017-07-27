<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nkongme
 * Date: 19.10.13
 * Time: 12:57
 * To change this template use File | Settings | File Templates.
 */

class Kibithek_Home_Model_FilterTest extends Kibithek_PHPUnit_TestCase
{

    /**
     * @test
     */
    public function testInstance()
    {
        $model = Mage::getModel('kibithek_home/filter');
        $this->assertInstanceOf('Kibithek_Home_Model_Filter', $model);
    }

    /**
     * @test
     * @expectedException Kibithek_Home_Exception
     */
    public function unknownCategoryCodeCausesException()
    {
        $params = array(
            'gender' => 'J',
            'skillXX' => 'NZH',
            'age' => '1',
            'topic' => 'FUA'
        );
        $model = Mage::getModel('kibithek_home/filter');
        $model->isValid($params);
    }

    /**
     * @test
     * @expectedException Kibithek_Home_Exception
     */
    public function unknownCategoryCodeValueCausesException()
    {
        $params = array(
            'gender' => 'J',
            'skill' => 'NZH',
            'age' => '1xx',
            'topic' => 'FUA'
        );
        $model = Mage::getModel('kibithek_home/filter');
        $model->isValid($params);
    }

    /**
     * @test
     */
    public function missingCategoryReturnsFalse()
    {
        $params = array(
            'gender' => 'J',
            'skill' => 'NZH',
            'age' => '1'
        );
        $model = Mage::getModel('kibithek_home/filter');
        $result = $model->isValid($params);
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function missingCategorySetsErrors()
    {
        $params = array(
            'gender' => 'J',
            'skill' => 'NZH',
            'age' => '1'
        );
        $model = Mage::getModel('kibithek_home/filter');
        $result = $model->isValid($params);
        $this->assertFalse($result);

        $errors = $model->getErrors();
        $this->assertGreaterThan(0, count($errors));
    }

    /**
     * @test
     */
    public function getErrorResponse()
    {
        $model = Mage::getModel('kibithek_home/filter');
        $model->addErrors('a test error');
        $errorResponse = $model->getErrorResponse();
        $this->assertArrayHasKey('valid', $errorResponse);
        $this->assertEquals($errorResponse['valid'], false);
    }


}