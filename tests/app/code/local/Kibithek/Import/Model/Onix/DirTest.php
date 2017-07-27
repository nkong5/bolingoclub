<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nkongme
 * Date: 19.10.13
 * Time: 12:57
 * To change this template use File | Settings | File Templates.
 */

class Kibithek_Import_Model_Onix_DirTest extends Kibithek_PHPUnit_TestCase
{

    /**
     * @test
     */
    public function testInstance()
    {
        $model = Mage::getModel('kibithek_import/onix_dir');
        $this->assertInstanceOf('Kibithek_Import_Model_Onix_Dir', $model);
    }

    /**
     * @test
     */
    public function listFiles()
    {
        $dir = Mage::getModel('kibithek_import/onix_dir');
        $dir->addPath($dir::PATH_BASE);
        $dir->addPath($dir::PATH_UPDATE);

        $result = $dir->listFiles();
        $this->assertInternalType('array', $result);
    }

    /**
     * @test
     */
    public function deleteOrders()
    {
        //IMPORTANT!!!
        Mage::app('admin')->setUseSessionInUrl(false);

        $orders = Mage::getModel('sales/order')->getCollection();

        foreach($orders as $order){
            try{
                $id = $order->getId();
                Mage::getModel('sales/order')->loadByIncrementId($id)->delete();
                echo "order #".$id." is removed".PHP_EOL;
            }catch(Exception $e){
                echo "order #".$id." could not be remvoved: ".$e->getMessage().PHP_EOL;
            }
        }
    }

}