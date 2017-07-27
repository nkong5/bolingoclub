<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nkongme
 * Date: 19.10.13
 * Time: 12:57
 * To change this template use File | Settings | File Templates.
 */

class Kibithek_Home_Model_Book_BookTest extends Kibithek_PHPUnit_TestCase
{

    /**
     * @test
     */
    public function testInstance()
    {
        $model = Mage::getModel('kibithek_mapper/book');
        $this->assertInstanceOf('Kibithek_Mapper_Model_Book', $model);
    }

    /**
     * @test
     */
    public function filterIsbn()
    {
        $model = Mage::getModel('kibithek_mapper/book');
        $result = $model->filterIsbn();
        $this->assertInternalType('array', $result);
    }

//    /**
//     * @test
//     */
//    public function filterIsbnMemoryUsageAnalyses()
//    {
//        $model = Mage::getModel('kibithek_mapper/book');
//        $memoryUsageStart = memory_get_usage();
//        $json = $model->filterIsbn();
//        $memoryUsage = memory_get_usage() - $memoryUsageStart;
//
//        $memoryUsageMB = $memoryUsage/(1024*1024) . " MB";
//
//        // json_decode returns stdClass
//        $jsonDecoded = json_decode($json);
//        if (isset($jsonDecoded->{9783407753564})) {
//            $property = $jsonDecoded->{9783407753564};
//        };
//
//    }



}