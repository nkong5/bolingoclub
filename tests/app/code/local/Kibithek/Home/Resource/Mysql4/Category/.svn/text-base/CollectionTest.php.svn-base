<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nkongme
 * Date: 23.09.13
 * Time: 23:17
 * To change this template use File | Settings | File Templates.
 */


class Kibithek_Home_Resource_Mysql4_Category_CollectionTest extends Kibithek_PHPUnit_TestCase
{

    /**
     * @var Kibithek_Home_Helper_Data
     */
    protected $_res;

    /**
     * Set up
     */
    public function setUp()
    {
        parent::setUp();
        $this->_res = Mage::getResourceModel('kibithek_home/category_collection');
    }

    /**
     * Tear down
     */
    public function tearDown()
    {
        $this->_res = null;
        parent::tearDown();
    }


    /**
     * @test
     */
    public function checkCollection()
    {
        $this->assertInstanceOf('Kibithek_Home_Resource_Mysql4_Category_Collection', $this->_res);
    }

    /**
     * @test
     */
    public function getCategoriesDistinct()
    {
        $collection = $this->_res->getCategoriesDistinct();
        $this->assertInstanceOf('Kibithek_Home_Resource_Mysql4_Category_Collection', $collection);
        $count = $collection->count();
        foreach ($collection->getItems() as $item) {
            $categoryId = $item->getId();
            $categoryName = $item->getCategory();
        }
    }

}