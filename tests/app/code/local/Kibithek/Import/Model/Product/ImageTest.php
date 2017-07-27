<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nkongme
 * Date: 19.10.13
 * Time: 12:57
 * To change this template use File | Settings | File Templates.
 */

class Kibithek_Import_Model_Product_ImageTest extends Kibithek_PHPUnit_TestCase
{

    /**
     * @test
     */
    public function testInstance()
    {
        $model = Mage::getModel('kibithek_import/product_image');
        $this->assertInstanceOf('Kibithek_Import_Model_Product_Image', $model);
    }

    /**
     * @test
     */
    public function importImage()
    {
        $memoryUsageStart = memory_get_usage();
        $rustart = getrusage();

        //IMPORTANT!!!
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


//        $dirBase = $this->_getRootPath();
        $dirBase = Mage::getBaseDir('base');

        try {
            if (!($dirMain = $this->getArg('dir-main'))) {
                throw new Exception('Parameter dir-main is not set!');
                if (!realpath($dirMain)) {
                    throw new Exception("Given path not correct: " . $dirMain);
                }
            }
        }catch (Exception $e) {
            echo 'Error: ' .  $e . "\n"
                . "type 'php " .__FILE__ . " help' for directives.\n";
            return;
        }

        $products = Mage::getModel('catalog/product')->getCollection();
        $products->addAttributeToSelect('kibithek_vendor_id');
        $products->addAttributeToSelect('image');
        $products->addAttributeToSelect('name');

        /** @var Kibithek_Import_Model_Product_Image $image */
        $image = Mage::getModel('kibithek_import/product_image');
        $image->setDirMain($dirMain);
        $mainSubdirs = $image->getDirMainSubs();

        $imageKnv = Mage::getModel('kibithek_import/product_image_knv');

        $report = Mage::getModel('kibithek_import/report_image');

        $c = $products->count();
        $i = 0;
        foreach($products as $product) {
            $sku = $product->getSku();
            $productId = $product->getId();
            $vendorId = $product->getKibithekVendorId();

            /** @var Kibithek_Import_Model_Product_Image $image */
            $image = Mage::getModel('kibithek_import/product_image');
            $image->setDirMain($dirMain);
            $image->setDirInflixLarge($imageKnv::DIR_INFLIX_LARGE);
            $image->setDirInflixSmall($imageKnv::DIR_INFLIX_SMALL);
            $image->setDirInflixThumbnail($imageKnv::DIR_INFLIX_THUMBNAIL);
            $image->setDirMainSubs($mainSubdirs);

            echo "Analyzing image of product $sku ...\n";

            try{
                if (!($imageLargeFound = $image->find($vendorId))) {
                    throw new Kibithek_Import_Exception("Image of product not found");
                }
            }catch (Kibithek_Import_Exception $e) {
                $report->addRecord(Zend_Log::CRIT, $e->getMessage(), $sku, $product->getName(), $vendorId . '*');
                $report->save();

                echo 'Error: ' . $e->getMessage() . "\n";
                echo "Continuing on to next product ...\n";
                continue;
            }

            $productImageLarge = $dirBase . '/media/catalog/product' .$product->getImage();

            if (!empty($productImageLarge) &&
                (sha1_file($productImageLarge) === sha1_file($imageLargeFound))) {
                echo "Image was already imported, aborting current import ...\n";
                continue;
            } else {
                echo "Image is new ...\n";
                $mediaApi = Mage::getModel("catalog/product_attribute_media_api");
                if ($items = $mediaApi->items($productId)) {
                    echo "Deleting old images ...\n";
                    foreach($items as $item) {
                        $mediaApi->remove($product->getId(), $item['file']);
                    }
                }

                echo "Importing new image ...\n";
                $product->addImageToMediaGallery($image->getLarge(), 'image', false, false);
                $product->addImageToMediaGallery ($image->getSmall(), array ('small_image','thumbnail'), false, false);
                echo "Saving product with new image ...\n\n";

                $product->save();
            }

            $i++;
            echo "Image import of product " . $sku . " completed. $i of $c products imported\n";
        }


        echo "emailing report ...\n";
        if ($result = $report->mail()) {
            echo 'report emailed to ';
            $recipients = $result->getRecipients();
            foreach ($recipients as $email) {
                echo $email . ' ';
            }
        }else{
            echo 'report could not be emailed';
        }
        echo "\n\n";

        $memoryUsage = memory_get_usage() - $memoryUsageStart;
        $memoryUsageMB = $memoryUsage/(1024*1024) . " MB";
        echo "Memory Usage: $memoryUsageMB \n";

        $ru = getrusage();
        echo "Time spent for computations: " . $this->_resourceUsageTime($ru, $rustart, "utime")  ." s\n";
        echo "Time spent for systemcalls: " . $this->_resourceUsageTime($ru, $rustart, "stime") . " s\n";
    }

    protected function getDirectoriesSub($dir)
    {

        $directoriesSub = array();
        foreach(scandir($dir) as $item ) {
            if ($item == '.' || $item ==='..' || $item == '.DS_Store') {
                continue;
            }

            if (!is_dir($dir . '/' . $item)) {
                continue;
            }
            $dateModified = filemtime($dir . '/' . $item);
            $directoriesSub[$dateModified] = $item;
        }

        asort($directoriesSub); // value is passed by reference

        return $directoriesSub;
    }

    /**
     * @param $arg
     * @return string
     */
    protected function getArg($arg)
    {
        $result = '';
        switch ($arg) {
            case 'dir-main':
                $result = '/Volumes/MyMacintoshHD/work/company/kibi/partner/knv/grundbestand_coverabbildungen';
                break;
            default:
                break;
        }
        return $result;
    }

    // Script end
    private function _resourceUsageTime($ru, $rus, $index) {
        $timeMilliseconds = ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
            -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
        $timeSeconds = $timeMilliseconds * 0.001;
        return $timeSeconds;
    }


}