<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nkongme
 * Date: 23.09.13
 * Time: 23:17
 * To change this template use File | Settings | File Templates.
 */


class Kibithek_Mapper_Resource_Mysql4_Book_PropertyTest extends Kibithek_PHPUnit_TestCase
{

    /**
     * @test
     */
    public function testInstance()
    {
        $model = Mage::getResourceModel('kibithek_mapper/book_property');
        $this->assertInstanceOf('Kibithek_Mapper_Resource_Mysql4_Book_Property', $model);
    }

}