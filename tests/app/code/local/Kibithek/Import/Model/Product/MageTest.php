<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nkongme
 * Date: 19.10.13
 * Time: 12:57
 * To change this template use File | Settings | File Templates.
 */

class Kibithek_Import_Model_Product_MageTest extends Kibithek_PHPUnit_TestCase
{

    /**
     * @test
     */
    public function testInstance()
    {
        $model = Mage::getModel('kibithek_import/product_mage');
        $this->assertInstanceOf('Kibithek_Import_Model_Product_Mage', $model);
    }

    /**
     * @test
     */
    public function adapt()
    {
        // IMPORT ONIX XML
        // IMPORT ONIX DIR
        $dir = Mage::getModel('kibithek_import/onix_dir');
        $files = $dir->readFiles();

        foreach($files as $file) {
            $xml = Mage::getModel('kibithek_import/onix_xml');
            $xml->setFile($dir->getPath() . "/$file");
            $xml->parsePerXmlReader();

            $products = $xml->getProducts();
            $this->assertInternalType('array', $products);

            // IMPORT PRODUCT MAGE
            $productMage = Mage::getModel('kibithek_import/product_mage');

            $image = Mage::getModel('kibithek_import/product_image');
            $imageKnv = Mage::getModel('kibithek_import/product_image_knv');

            $dirMain = "/Volumes/MyMacintoshHD/work/company/kibi/partner/knv/grundbestand_coverabbildungen/";
            $image->setDirMain($dirMain);
            $image->setDirInflixLarge($imageKnv::DIR_INFLIX_LARGE);
            $image->setDirInflixSmall($imageKnv::DIR_INFLIX_SMALL);
            $image->setDirInflixThumbnail($imageKnv::DIR_INFLIX_THUMBNAIL);

            $imageKibithek = Mage::getModel('kibithek_import/product_image_kibithek');
            $dirArchive = BASE_PATH . "/var/import/image/catalog/";
            $image->setDirArchiveMain($dirArchive);
            $image->setDirArchiveInflixLarge($imageKibithek::DIR_INFLIX_LARGE);
            $image->setDirArchiveInflixSmall($imageKibithek::DIR_INFLIX_SMALL);
            $image->setDirArchiveInflixThumbnail($imageKibithek::DIR_INFLIX_THUMBNAIL);

            $productMage->setImage($image);
            $productMage->setArchiveImage(true);

            foreach($products as $product) {
                if($productAdapted = $productMage->adapt($product)){
                    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
                    $productMage->import($productAdapted);
                }
            }

            $dir->archiveFile($file);

        }

    }


    private function getArg($arg)
    {
        $result = '';
        switch ($arg) {
            case 'dir-read-type':
                $result = 'all';
                break;
            case 'dir-path':
                $result = '/Volumes/MyMacintoshHD/work/company/kibi/partner/knv/katalog/onix/gesamt';
                break;
        }
        return $result;
    }

    /**
     * @test
     * @throws Exception
     */
    public function testRunScriptImportImages()
    {

        // set main directory
        $dirMain = "/Volumes/MyMacintoshHD/work/company/kibi/partner/knv/grundbestand_coverabbildungen/";
        if ($dirMainArg = $this->getArg('dir-main')) {
            if (!realpath($dirMainArg)) {
                throw new Exception("The given main path is incorrect:" . $this->getArg('dir-archive'));
            }
            $dirMain = $dirMainArg;
        }

        $products = Mage::getModel('catalog/product')->getCollection();
        $c = $products->count();
        $i = 0;
        foreach($products as $product) {
            $isbn = $product->getSku();
            $vendorTitleNumber = $product->getKibithekVendorTitleNumber();

            /** @var Kibithek_Import_Model_Product_Image $image */
            $image = Mage::getModel('kibithek_import/product_image');

            $imageKnv = Mage::getModel('kibithek_import/product_image_knv');
            $image->setDirMain($dirMain);
            $image->setDirInflixLarge($imageKnv::DIR_INFLIX_LARGE);
            $image->setDirInflixSmall($imageKnv::DIR_INFLIX_SMALL);
            $image->setDirInflixThumbnail($imageKnv::DIR_INFLIX_THUMBNAIL);

            $dirArchive = realpath($this->_getRootPath() . "/var/import/image/catalog/");
            if ($dirArchiveArg = $this->getArg('dir-archive')) {
                if (!realpath($dirArchiveArg)) {
                    throw new Exception("The given archive path is incorrect:" . $this->getArg('dir-archive'));
                }
                $dirArchive = $dirArchiveArg;
            }

            $imageKibithek = Mage::getModel('kibithek_import/product_image_kibithek');
            $image->setDirArchiveMain($dirArchive);
            $image->setDirArchiveInflixLarge($imageKibithek::DIR_INFLIX_LARGE);
            $image->setDirArchiveInflixSmall($imageKibithek::DIR_INFLIX_SMALL);
            $image->setDirArchiveInflixThumbnail($imageKibithek::DIR_INFLIX_THUMBNAIL);

            try {
                if (!($imageLargeFound = $image->find($vendorTitleNumber))) {
                    echo 'Image not found: ISBN ' . $isbn . ', Title number ' . $vendorTitleNumber;
                    continue;
                }

                if ($this->getArg('archive')) {
                    $image->archive();
                }

                echo "Checking if image with ISBN $isbn was already imported ...\n";
                if ($imageImported = $image->alreadyImported($vendorTitleNumber)) {
                    echo "Image was already imported to: $imageImported\n";
                }else{
                    echo 'Adding image to product: ISBN ' . $isbn . ', Title number ' . $vendorTitleNumber . " ...\n";
                    $product->addImageToMediaGallery($image->getLarge(), 'image', false, false);
                    $product->addImageToMediaGallery ($image->getSmall(), array ('small_image','thumbnail'), false, false);
                    echo "Saving product ...\n\n";
                    $product->save();
                }
            }catch (Exception $e) {
                $message = "Following error occured importing product with ISBN $isbn \n"
                    . $e->getMessage();
                Mage::helper('kibithek_import')->log($message, Zend_Log::WARN);
            }

            $i++;
            echo "imported image for product " . $product->getSku() . " completed. $i of $c products imported\n";
        }

    }
}