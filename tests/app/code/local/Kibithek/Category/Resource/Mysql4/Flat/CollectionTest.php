<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nkongme
 * Date: 11.10.13
 * Time: 20:56
 * To change this template use File | Settings | File Templates.
 */


class Kibithek_Category_Resource_Mysql4_Flat_CollectionTest extends Kibithek_PHPUnit_TestCase
{

    /**
     * @test
     */
    public function checkCollection()
    {
        $res = Mage::getResourceModel('kibithek_category/flat_collection');
        $this->assertInstanceOf('Mage_Core_Model_Mysql4_Collection_Abstract', $res);
        $this->assertInstanceOf('Kibithek_Category_Resource_Mysql4_Flat_Collection', $res);

        $res = Mage::getModel('kibithek_category/flat')->getCollection();
        $this->assertInstanceOf('Mage_Core_Model_Mysql4_Collection_Abstract', $res);
        $this->assertInstanceOf('Kibithek_Category_Resource_Mysql4_Flat_Collection', $res);
    }

    /**
     * @test
     */
    public function getMainItems()
    {
        $res = Mage::getResourceModel('kibithek_category/flat_collection');
        $collection = $res->getMainItems();
        $count = $collection->count();
        $this->assertGreaterThan(0, $count);

//        // use only for instant visual test
//        $items = $collection->getItems();
//        foreach ($items as $item) {
//            $categoryName = $item->getCategoryMainName();
//        }
        $this->assertInstanceOf('Kibithek_Category_Resource_Mysql4_Flat_Collection', $collection);
    }

    /**
     * @test
     */
    public function getMainItemChildren()
    {
        $res = Mage::getResourceModel('kibithek_category/flat_collection');
        $collection = $res->getMainItems();
        $count = $collection->count();
        $this->assertGreaterThan(0, $count);

        // use only for instant visual test
        $items = $collection->getItems();
        foreach ($items as $item) {
            $res = Mage::getResourceModel('kibithek_category/flat_collection');
            $children = $res->getMainItemChildren($item);
            $count = $children->count();
            $this->assertGreaterThan(0, $count);
//            foreach($children as $child) {
//                echo "-- " .$child->getCategorySubName() . "\n";
//            }
        }

        $this->assertInstanceOf('Kibithek_Category_Resource_Mysql4_Flat_Collection', $collection);
    }


}