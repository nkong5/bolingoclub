<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nkongme
 * Date: 19.10.13
 * Time: 12:57
 * To change this template use File | Settings | File Templates.
 */

class Kibithek_Home_Model_Book_PropertyTest extends Kibithek_PHPUnit_TestCase
{

    /**
     * @test
     */
    public function testInstance()
    {
        $model = Mage::getModel('kibithek_mapper/book_property');
        $this->assertInstanceOf('Kibithek_Mapper_Model_Book_Property', $model);
    }



}