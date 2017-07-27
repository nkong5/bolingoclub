<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nkongme
 * Date: 11.10.13
 * Time: 21:22
 * To change this template use File | Settings | File Templates.
 */

class Kibithek_Category_Model_GenerateTest extends Kibithek_PHPUnit_TestCase
{
    /**
     * @test
     */
    public function checkModel()
    {
        $model = Mage::getModel('kibithek_category/generate');
        $this->assertInstanceOf('Varien_Object', $model);
        $this->assertInstanceOf('Kibithek_Category_Model_Generate', $model);
    }

    /**
     * @test
     */
    public function getUrl(){
        /** for detail test
         * @see Kibithek_Base_Helper_SanitizeTest::urlMustContainOnlyOneDash()
         */

        $model = Mage::getModel('kibithek_category/generate');
        $this->assertInstanceOf('Kibithek_Category_Model_Generate', $model);

        $collection = Mage::getResourceModel('kibithek_category/flat_collection');
        $this->assertInstanceOf('Mage_Core_Model_Mysql4_Collection_Abstract', $collection);

//        $items = $collection->getItems();
//        foreach($items as $item) {
//            $name = $item->getCategoryMainName();
//            $url = $model->getUrl($name);
//            echo $url . "\n";
//        }

    }

    /**
     * @test
     */
    public function root ()
    {
        $generate = Mage::getModel('kibithek_category/generate');
        $category = $generate->root();
        $this->assertInstanceOf('Mage_Catalog_Model_Category', $category);
    }

    /**
     * @test
     */
    public function sortCategories()
    {
        $collection = Mage::getResourceModel('catalog/category_collection');
        $categories = $collection->getItems();

        /** @var Mage_Catalog_Model_Category  $category */
        foreach($categories as $category) {
            $path = $category->getPath();
            $paths = explode('/', $path);
            // if it's third level
            if (isset($paths[2])) {
                $colSub = $category->getChildrenCategories();
                $catsSub = $colSub->getItems();
                /** @var Mage_Catalog_Model_Category $catSub */
                foreach($catsSub as $catSub) {

                    $colSubSub = $catSub->getChildrenCategories();
                    $catsSubSub = $colSubSub->getItems();

                    // getting positions of products
                    $positionsSub = $catSub->getProductsPosition();

                    /** @var Mage_Catalog_Model_Category $catSubSub */
                    foreach ($catsSubSub as $catSubSub) {
                        $id = $catSubSub->getId();
                        $positionsSubSub = $catSubSub->getProductsPosition();

                        // setting position of products with subcategory IDs
                        foreach($positionsSubSub as $key => $value) {
                            if (array_key_exists($key, $positionsSub)) {
                                $positionsSub[$key] = $id;
                            }
                        }
                    }

                    // saving new category positions
                    $catSub->setPostedProducts($positionsSub);
//                    $catSub->setProductsPosition($positionsSub);
                    $catSub->save();
                }

            }

        }
    }

//$products = $category->getProductCollection();
//
//foreach ($products as $product)
//
//if ($products->count()) {
//$id = $category->getId();
//$name = $category->getName();
//$productsPositions = $category->getProductsPosition();
//foreach ($products as $id=>$value){
//$productsPositions[$id] = $id;
//}
//$category->setProductsPosition($productsPositions);
//$category->save();
//}

    // run test only when menus are actually to be generated
    /**
     * @test
     */
//    public function menus()
//    {
//        $collection = Mage::getResourceModel('kibithek_category/flat_collection');
//        $generate = Mage::getModel('kibithek_category/generate');
//        $result = $generate->menus($collection);
//        $this->assertTrue($result);
//    }

}