<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nkongme
 * Date: 19.10.13
 * Time: 12:57
 * To change this template use File | Settings | File Templates.
 */

class Kibithek_Import_Model_Onix_XmlTest extends Kibithek_PHPUnit_TestCase
{

    /**
     * @test
     */
    public function testInstance()
    {
        $model = Mage::getModel('kibithek_import/onix_xml');
        $this->assertInstanceOf('Kibithek_Import_Model_Onix_Xml', $model);
    }

//    /**
//     * @test
//     * @expectedException Kibithek_Import_Exception
//     */
//    public function identifyProductThrowsExceptionIfNoVendorIdAvailable()
//    {
//        $mapper = Mage::getModel('kibithek_mapper/book');
//        $identifierValuesString  = $mapper->filterIsbn();
//        $identifierValues = json_decode($identifierValuesString);
//
//        $xmlProduct = $this->_xmlProductNoVendorId();
//        $model = Mage::getModel('kibithek_import/onix_xml');
//        $simpleXml = new SimpleXMLElement($xmlProduct);
//        $model->identifyProduct($simpleXml, $identifierValues);
//    }
//
//    /**
//     * @test
//     */
//    public function identifyProductReturnsFalseIfProductCantBeIdentified()
//    {
//        $mapper = Mage::getModel('kibithek_mapper/book');
//        $identifierValuesString  = $mapper->filterIsbn();
//        $identifierValues = json_decode($identifierValuesString);
//
//        $xmlProduct = $this->_xmlProductWrongProductIdentifierValue();
//        $model = Mage::getModel('kibithek_import/onix_xml');
//        $simpleXml = new SimpleXMLElement($xmlProduct);
//        $result = $model->identifyProduct($simpleXml, $identifierValues);
//        $this->assertFalse($result);
//    }

    /**
     * @test
     */
    public function findProductByIdentifierIdValue()
    {
        /** @var Kibithek_Import_Model_Onix_Dir  $dir */
        $dir = Mage::getModel('kibithek_import/onix_dir');

        // check shell directory, if given
        if (!($productId = (string)$this->getArg('product-id'))) {
            echo "Parameter product-id is not set! Check help for directives\n";
            return;
        }

        // check if parameter dir-read-type is set
        if (!($dirReadType = (string)$this->getArg('dir-read-type'))) {
            echo "Parameter dir-read-type is not set! Check help for directives\n";
            return;
        }

        switch ($dirReadType) {
            case $dir::TYPE_BASE:
                $dir->addPath($dir::PATH_BASE);
                break;
            case $dir::TYPE_UPDATE:
                $dir->addPath($dir::PATH_UPDATE);
                break;
            case $dir::TYPE_ALL:
                $dir->addPath($dir::PATH_BASE);
                $dir->addPath($dir::PATH_UPDATE);
                break;
            case $dir::TYPE_MANUAL:
                if (!($path = (string)$this->getArg('dir-path'))) {
                    echo "Parameter dir-path is not set! Check help for directives\n";
                    return;
                }

                $dir->addPath($path);
                break;
            default:
                echo 'dir-read-type parameter is unknown. Use one of the following types: ' .
                     $dir::TYPE_BASE . ' ' . $dir::TYPE_UPDATE . ' ' . $dir::TYPE_ALL;
                return;
                break;
        }

        $files = $dir->listFiles();


        if (!($cFiles = count($files))) {
            echo "Error: No XML files to search for in given directories\n";
            return;
        }

        $cXml = 0;
        foreach($files as $file) {
            echo "reading file: $file\n";

            $xml = Mage::getModel('kibithek_import/onix_xml');
            $xml->setFile($file);

            echo "searching product ...\n";
            if ($result = $xml->findProductByIdentifierIdValue($productId)) {
                print_r($result);
                return;
            }

            $cXml++;
            echo "product not found\n";
            echo "$cXml of $cFiles files searched\n\n";
        }
    }

    /**
     * @param $arg
     * @return string
     */

    private function getArg($arg)
    {
        $result = '';
        switch($arg){
            case 'product-id':
                $result = '4050003922157';
                break;
            case 'dir-read-type':
                $result = 'manual';
                break;
            case 'dir-path':
                $result = '/Volumes/MyMacintoshHD/work/company/kibi/partner/knv/katalog/onix/gesamt';
                break;
            default:
                break;
        }
        return $result;
    }

    /**
     * @test
     */
    public function mapProduct()
    {
        $product = Mage::getModel('kibithek_import/product');

        $xmlProduct = $this->_xmlProduct();
        $model = Mage::getModel('kibithek_import/onix_xml');
        $simpleXml = new SimpleXMLElement($xmlProduct);
        $result = $model->mapProduct($simpleXml, $product);
        $this->assertInstanceOf('Kibithek_Import_Model_Product', $result);
    }

    /**
     * @test
     */
    public function getDatePublication()
    {
        $model = Mage::getModel('kibithek_import/onix_xml');
        $expected = '2011-06-08';
        $result = $model->getDatePublication('20110608');
        $this->assertEquals($expected, $result);

        $model = Mage::getModel('kibithek_import/onix_xml');
        $expected = '2011-06';
        $result = $model->getDatePublication('201106');
        $this->assertEquals($expected, $result);

        $model = Mage::getModel('kibithek_import/onix_xml');
        $expected = '1986-01';
        $result = $model->getDatePublication('198601');
        $this->assertEquals($expected, $result);

    }

    /**
     * @test
     */
    public function getProductAvailabilityInfo()
    {
        $model = Mage::getModel('kibithek_import/onix_xml');
        $result = $model->getProductAvailabilityInfo('43');
        $this->assertInternalType('string', $result);

    }

    /**
     * @test
     */
    public function importProduct()
    {
        /** @var Kibithek_Import_Model_Onix_Dir  $dir */
        $dir = Mage::getModel('kibithek_import/onix_dir');

        // check if parameter dir-read-type is set
        if (!($dirReadType = (string)$this->getArg('dir-read-type'))) {
            echo "Error: Parameter dir-read-type is not set! Check help for directives.\n";
            return;
        }

        switch ($dirReadType) {
            case $dir::TYPE_BASE:
                $dir->addPath($dir::PATH_BASE);
                break;
            case $dir::TYPE_UPDATE:
                $dir->addPath($dir::PATH_UPDATE);
                break;
            case $dir::TYPE_ALL:
                $dir->addPath($dir::PATH_BASE);
                $dir->addPath($dir::PATH_UPDATE);
                break;
            case $dir::TYPE_MANUAL:
                if (!($path = $this->getArg('dir-path'))) {
                    echo "Parameter dir-path is not set! Check help for directives\n";
                    return;
                }

                $dir->addPath($path);
                break;
            default:
                echo 'dir-read-type parameter is unknown. Use one of the following types: ' .
                    $dir::TYPE_BASE . ' ' . $dir::TYPE_UPDATE . ' ' . $dir::TYPE_ALL . "\n";
                return;
                break;
        }

        $files = $dir->listFiles();

        if (!($cFiles = count($files))) {
            throw new Exception("Error: No XML files to import in set directories");
        }

        $cXml = 0;

        // create report object to use later
        $report = Mage::getModel('kibithek_import/report_product');

        // get identifier values
        $xml = Mage::getModel('kibithek_import/onix_xml');
        $identifierValues = $xml->getIdentifierValues();

        foreach($files as $file) {
            echo "reading file: $file\n";

            $xml = Mage::getModel('kibithek_import/onix_xml');
            $xml->setFile($file);
            $xml->setReport($report);
            $xml->setIdentifierValues($identifierValues);

            echo "parsing file ...\n";
            $xml->parse();

            if (!($products = $xml->getProducts())) {
                echo "no products found\n";
                continue;
            }

            /** @var Kibithek_Import_Model_Product_Mage $productMage */
            $productMage = Mage::getModel('kibithek_import/product_mage');

            echo "importing products ...\n";

            $c = 0;
            $i = 0;
            /** @var Kibithek_Import_Model_Product $product */
            foreach($products as $product) {
                echo 'adapting product SKU ' . $product->sku . ' VENDOR-ID ' . $product->attributeVendorId . ' ... ';
                if($productAdapted = $productMage->adapt($product)){
                    echo "importing ...\n";
                    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
                    $productMage->import($productAdapted);
                    $i++;
                }
                $c++;
            }

            $report->save();

            $cXml++;
            echo "import for $file completed. $i of $c products imported\n";
            echo "$cXml of $cFiles files imported\n\n";
        }

        echo "emailing logfile ...\n";
        if ($result = $report->mail()) {
            echo 'logfile emailed to ';
            $recipients = $result->getRecipients();
            foreach ($recipients as $email) {
                echo $email . ' ';
            }
        }else{
            echo 'logfile could not be emailed';
        }
        echo "\n\n";
    }

    /**
     * @return string
     */
    private function _xmlFile()
    {
        $xml = <<<XML
<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE ONIXmessage SYSTEM "http://www.editeur.org/onix/2.1/short/onix-international.dtd">
<ONIXmessage release="2.1">
  <header>
    <m172>4026725000008</m172>
    <m174>KNV</m174>
    <m175>Angelika Rausch</m175>
    <m283>onix-info@KNV.de</m283>
    <m182>201311150153</m182>
    <m184>ger</m184>
    <m186>EUR</m186>
  </header>
  <product>
    <a001>KNV2013111403674</a001>
    <a002>04</a002>
    <a194>03</a194>
    <a197>KNV</a197>
    <productidentifier>
      <b221>03</b221>
      <b244>9783480231393</b244>
    </productidentifier>
    <productidentifier>
      <b221>01</b221>
      <b233>KNV</b233>
      <b244>43728407</b244>
    </productidentifier>
    <b012>BB</b012>
    <productclassification>
      <b274>04</b274>
      <b275>49019900</b275>
    </productclassification>
    <series>
      <title>
        <b202>01</b202>
        <b203>Meine große Tier-Bibliothek</b203>
      </title>
    </series>
    <title>
      <b202>01</b202>
      <b203>Der Maulwurf</b203>
    </title>
    <title>
      <b202>10</b202>
      <b203>Poschadel:Der Maulwurf</b203>
    </title>
    <contributor>
      <b034>1</b034>
      <b035>A01</b035>
      <b037>Poschadel, Jens</b037>
    </contributor>
    <b058>1., Aufl.</b058>
    <b061>32</b061>
    <b255>32</b255>
    <b062>m. zahlr. farb. Fotos</b062>
    <mainsubject>
      <b191>26</b191>
      <b069>12820</b069>
    </mainsubject>
    <mainsubject>
      <b191>26</b191>
      <b068>2.0</b068>
      <b069>1282</b069>
    </mainsubject>
    <subject>
      <b067>20</b067>
      <b070>Grundschule</b070>
    </subject>
    <subject>
      <b067>20</b067>
      <b070>Maulwurf</b070>
    </subject>
    <subject>
      <b067>24</b067>
      <b171>KNV type of article</b171>
      <b069>25</b069>
    </subject>
    <subject>
      <b067>24</b067>
      <b171>KNV group code</b171>
      <b069>000</b069>
    </subject>
    <audiencerange>
      <b074>17</b074>
      <b075>03</b075>
      <b076>4</b076>
      <b075>04</b075>
      <b076>6</b076>
    </audiencerange>
    <b207>ab 4 J.</b207>
    <othertext>
      <d102>02</d102>
      <d103>00</d103>
      <d104>Er ist der weltbeste Tunnelbauer und ein heimlicher Erdwerfer : Der Maulwurf lebt tief unter der Erde und ist tagaus und tagein damit beschäftigt, sich Nahrung zu suchen und seine perfekt entwickelten Hände zum Graben zu benutzen.</d104>
    </othertext>
    <publisher>
      <b291>01</b291>
      <b081>Esslinger Verlag Schreiber</b081>
    </publisher>
    <b394>02</b394>
    <b003>20140110</b003>
    <b087>2014</b087>
    <measure>
      <c093>01</c093>
      <c094>240</c094>
      <c095>mm</c095>
    </measure>
    <measure>
      <c093>02</c093>
      <c094>210</c094>
      <c095>mm</c095>
    </measure>
    <supplydetail>
      <supplieridentifier>
        <j345>06</j345>
        <b244>4026743000004</b244>
      </supplieridentifier>
      <j137>KNV</j137>
      <j292>04</j292>
      <j396>11</j396>
      <j260>00</j260>
      <j142>20140110</j142>
      <price>
        <j148>04</j148>
        <j267>33.3</j267>
        <j151>9.95</j151>
        <b251>DE</b251>
        <j153>R</j153>
      </price>
      <price>
        <j148>04</j148>
        <j151>10.3</j151>
        <b251>AT</b251>
      </price>
      <price>
        <j148>04</j148>
        <j151>14.9</j151>
        <j152>CHF</j152>
        <b251>CH</b251>
      </price>
    </supplydetail>
  </product>
</ONIXmessage>
XML;
        return $xml;
    }

    /**
     * @return string
     */
    private function _xmlProduct()
    {
        $xml = <<<XML
<?xml version="1.0" encoding="ISO-8859-1"?>
  <product>
    <a001>KNV2013111403674</a001>
    <a002>04</a002>
    <a194>03</a194>
    <a197>KNV</a197>
    <productidentifier>
      <b221>03</b221>
      <b244>9783480231393</b244>
    </productidentifier>
    <productidentifier>
      <b221>01</b221>
      <b233>KNV</b233>
      <b244>43728407</b244>
    </productidentifier>
    <b012>BB</b012>
    <productclassification>
      <b274>04</b274>
      <b275>49019900</b275>
    </productclassification>
    <series>
      <title>
        <b202>01</b202>
        <b203>Meine große Tier-Bibliothek</b203>
      </title>
    </series>
    <title>
      <b202>01</b202>
      <b203>Der Maulwurf</b203>
    </title>
    <title>
      <b202>10</b202>
      <b203>Poschadel:Der Maulwurf</b203>
    </title>
    <contributor>
      <b034>1</b034>
      <b035>A01</b035>
      <b037>Poschadel, Jens</b037>
    </contributor>
    <b058>1., Aufl.</b058>
    <b061>32</b061>
    <b255>32</b255>
    <b062>m. zahlr. farb. Fotos</b062>
    <mainsubject>
      <b191>26</b191>
      <b069>12820</b069>
    </mainsubject>
    <mainsubject>
      <b191>26</b191>
      <b068>2.0</b068>
      <b069>1282</b069>
    </mainsubject>
    <subject>
      <b067>20</b067>
      <b070>Grundschule</b070>
    </subject>
    <subject>
      <b067>20</b067>
      <b070>Maulwurf</b070>
    </subject>
    <subject>
      <b067>24</b067>
      <b171>KNV type of article</b171>
      <b069>25</b069>
    </subject>
    <subject>
      <b067>24</b067>
      <b171>KNV group code</b171>
      <b069>000</b069>
    </subject>
    <audiencerange>
      <b074>17</b074>
      <b075>03</b075>
      <b076>4</b076>
      <b075>04</b075>
      <b076>6</b076>
    </audiencerange>
    <b207>ab 4 J.</b207>
    <othertext>
      <d102>02</d102>
      <d103>00</d103>
      <d104>Er ist der weltbeste Tunnelbauer und ein heimlicher Erdwerfer : Der Maulwurf lebt tief unter der Erde und ist tagaus und tagein damit beschäftigt, sich Nahrung zu suchen und seine perfekt entwickelten Hände zum Graben zu benutzen.</d104>
    </othertext>
    <publisher>
      <b291>01</b291>
      <b081>Esslinger Verlag Schreiber</b081>
    </publisher>
    <b394>02</b394>
    <b003>20140110</b003>
    <b087>2014</b087>
    <measure>
      <c093>01</c093>
      <c094>240</c094>
      <c095>mm</c095>
    </measure>
    <measure>
      <c093>02</c093>
      <c094>210</c094>
      <c095>mm</c095>
    </measure>
    <supplydetail>
      <supplieridentifier>
        <j345>06</j345>
        <b244>4026743000004</b244>
      </supplieridentifier>
      <j137>KNV</j137>
      <j292>04</j292>
      <j396>11</j396>
      <j260>00</j260>
      <j142>20140110</j142>
      <price>
        <j148>04</j148>
        <j267>33.3</j267>
        <j151>9.95</j151>
        <b251>DE</b251>
        <j153>R</j153>
      </price>
      <price>
        <j148>04</j148>
        <j151>10.3</j151>
        <b251>AT</b251>
      </price>
      <price>
        <j148>04</j148>
        <j151>14.9</j151>
        <j152>CHF</j152>
        <b251>CH</b251>
      </price>
    </supplydetail>
  </product>
XML;
        return $xml;
    }

    /**
     * @return string
     */
    private function _xmlProductNoVendorId()
    {
        $xml = <<<XML
<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE ONIXmessage SYSTEM "http://www.editeur.org/onix/2.1/short/onix-international.dtd">
<ONIXmessage release="2.1">
  <header>
    <m172>4026725000008</m172>
    <m174>KNV</m174>
    <m175>Angelika Rausch</m175>
    <m283>onix-info@KNV.de</m283>
    <m182>201311150153</m182>
    <m184>ger</m184>
    <m186>EUR</m186>
  </header>
  <product>
    <a002>04</a002>
    <a194>03</a194>
    <a197>KNV</a197>
    <productidentifier>
      <b221>03</b221>
      <b244>9783480231393</b244>
    </productidentifier>
    <productidentifier>
      <b221>01</b221>
      <b233>KNV</b233>
      <b244>43728407</b244>
    </productidentifier>
    <b012>BB</b012>
    <productclassification>
      <b274>04</b274>
      <b275>49019900</b275>
    </productclassification>
    <series>
      <title>
        <b202>01</b202>
        <b203>Meine große Tier-Bibliothek</b203>
      </title>
    </series>
    <title>
      <b202>01</b202>
      <b203>Der Maulwurf</b203>
    </title>
    <title>
      <b202>10</b202>
      <b203>Poschadel:Der Maulwurf</b203>
    </title>
    <contributor>
      <b034>1</b034>
      <b035>A01</b035>
      <b037>Poschadel, Jens</b037>
    </contributor>
    <b058>1., Aufl.</b058>
    <b061>32</b061>
    <b255>32</b255>
    <b062>m. zahlr. farb. Fotos</b062>
    <mainsubject>
      <b191>26</b191>
      <b069>12820</b069>
    </mainsubject>
    <mainsubject>
      <b191>26</b191>
      <b068>2.0</b068>
      <b069>1282</b069>
    </mainsubject>
    <subject>
      <b067>20</b067>
      <b070>Grundschule</b070>
    </subject>
    <subject>
      <b067>20</b067>
      <b070>Maulwurf</b070>
    </subject>
    <subject>
      <b067>24</b067>
      <b171>KNV type of article</b171>
      <b069>25</b069>
    </subject>
    <subject>
      <b067>24</b067>
      <b171>KNV group code</b171>
      <b069>000</b069>
    </subject>
    <audiencerange>
      <b074>17</b074>
      <b075>03</b075>
      <b076>4</b076>
      <b075>04</b075>
      <b076>6</b076>
    </audiencerange>
    <b207>ab 4 J.</b207>
    <othertext>
      <d102>02</d102>
      <d103>00</d103>
      <d104>Er ist der weltbeste Tunnelbauer und ein heimlicher Erdwerfer : Der Maulwurf lebt tief unter der Erde und ist tagaus und tagein damit beschäftigt, sich Nahrung zu suchen und seine perfekt entwickelten Hände zum Graben zu benutzen.</d104>
    </othertext>
    <publisher>
      <b291>01</b291>
      <b081>Esslinger Verlag Schreiber</b081>
    </publisher>
    <b394>02</b394>
    <b003>20140110</b003>
    <b087>2014</b087>
    <measure>
      <c093>01</c093>
      <c094>240</c094>
      <c095>mm</c095>
    </measure>
    <measure>
      <c093>02</c093>
      <c094>210</c094>
      <c095>mm</c095>
    </measure>
    <supplydetail>
      <supplieridentifier>
        <j345>06</j345>
        <b244>4026743000004</b244>
      </supplieridentifier>
      <j137>KNV</j137>
      <j292>04</j292>
      <j396>11</j396>
      <j260>00</j260>
      <j142>20140110</j142>
      <price>
        <j148>04</j148>
        <j267>33.3</j267>
        <j151>9.95</j151>
        <b251>DE</b251>
        <j153>R</j153>
      </price>
      <price>
        <j148>04</j148>
        <j151>10.3</j151>
        <b251>AT</b251>
      </price>
      <price>
        <j148>04</j148>
        <j151>14.9</j151>
        <j152>CHF</j152>
        <b251>CH</b251>
      </price>
    </supplydetail>
  </product>
</ONIXmessage>
XML;
        return $xml;
    }

    /**
     * @return string
     */
    private function _xmlProductNoVendorProductId()
    {
        $xml = <<<XML
<?xml version="1.0" encoding="ISO-8859-1"?>
  <product>
    <a001>KNV2013111403674</a001>
    <a002>04</a002>
    <a194>03</a194>
    <a197>KNV</a197>
    <productidentifier>
      <b221>03</b221>
      <b244>9783480231393</b244>
    </productidentifier>
    <b012>BB</b012>
    <productclassification>
      <b274>04</b274>
      <b275>49019900</b275>
    </productclassification>
    <series>
      <title>
        <b202>01</b202>
        <b203>Meine große Tier-Bibliothek</b203>
      </title>
    </series>
    <title>
      <b202>01</b202>
      <b203>Der Maulwurf</b203>
    </title>
    <title>
      <b202>10</b202>
      <b203>Poschadel:Der Maulwurf</b203>
    </title>
    <contributor>
      <b034>1</b034>
      <b035>A01</b035>
      <b037>Poschadel, Jens</b037>
    </contributor>
    <b058>1., Aufl.</b058>
    <b061>32</b061>
    <b255>32</b255>
    <b062>m. zahlr. farb. Fotos</b062>
    <mainsubject>
      <b191>26</b191>
      <b069>12820</b069>
    </mainsubject>
    <mainsubject>
      <b191>26</b191>
      <b068>2.0</b068>
      <b069>1282</b069>
    </mainsubject>
    <subject>
      <b067>20</b067>
      <b070>Grundschule</b070>
    </subject>
    <subject>
      <b067>20</b067>
      <b070>Maulwurf</b070>
    </subject>
    <subject>
      <b067>24</b067>
      <b171>KNV type of article</b171>
      <b069>25</b069>
    </subject>
    <subject>
      <b067>24</b067>
      <b171>KNV group code</b171>
      <b069>000</b069>
    </subject>
    <audiencerange>
      <b074>17</b074>
      <b075>03</b075>
      <b076>4</b076>
      <b075>04</b075>
      <b076>6</b076>
    </audiencerange>
    <b207>ab 4 J.</b207>
    <othertext>
      <d102>02</d102>
      <d103>00</d103>
      <d104>Er ist der weltbeste Tunnelbauer und ein heimlicher Erdwerfer : Der Maulwurf lebt tief unter der Erde und ist tagaus und tagein damit beschäftigt, sich Nahrung zu suchen und seine perfekt entwickelten Hände zum Graben zu benutzen.</d104>
    </othertext>
    <publisher>
      <b291>01</b291>
      <b081>Esslinger Verlag Schreiber</b081>
    </publisher>
    <b394>02</b394>
    <b003>20140110</b003>
    <b087>2014</b087>
    <measure>
      <c093>01</c093>
      <c094>240</c094>
      <c095>mm</c095>
    </measure>
    <measure>
      <c093>02</c093>
      <c094>210</c094>
      <c095>mm</c095>
    </measure>
    <supplydetail>
      <supplieridentifier>
        <j345>06</j345>
        <b244>4026743000004</b244>
      </supplieridentifier>
      <j137>KNV</j137>
      <j292>04</j292>
      <j396>11</j396>
      <j260>00</j260>
      <j142>20140110</j142>
      <price>
        <j148>04</j148>
        <j267>33.3</j267>
        <j151>9.95</j151>
        <b251>DE</b251>
        <j153>R</j153>
      </price>
      <price>
        <j148>04</j148>
        <j151>10.3</j151>
        <b251>AT</b251>
      </price>
      <price>
        <j148>04</j148>
        <j151>14.9</j151>
        <j152>CHF</j152>
        <b251>CH</b251>
      </price>
    </supplydetail>
  </product>
XML;
        return $xml;
    }


    /**
     * @return string
     */
    private function _xmlProductWrongProductIdentifierValue()
    {
        $xml = <<<XML
<?xml version="1.0" encoding="ISO-8859-1"?>
  <product>
    <a001>KNV2013111403674</a001>
    <a002>04</a002>
    <a194>03</a194>
    <a197>KNV</a197>
    <productidentifier>
      <b221>03</b221>
      <b244>XXXX9783480231393</b244>
    </productidentifier>
    <productidentifier>
      <b221>01</b221>
      <b233>KNV</b233>
      <b244>43728407</b244>
    </productidentifier>
    <b012>BB</b012>
    <productclassification>
      <b274>04</b274>
      <b275>49019900</b275>
    </productclassification>
    <series>
      <title>
        <b202>01</b202>
        <b203>Meine große Tier-Bibliothek</b203>
      </title>
    </series>
    <title>
      <b202>01</b202>
      <b203>Der Maulwurf</b203>
    </title>
    <title>
      <b202>10</b202>
      <b203>Poschadel:Der Maulwurf</b203>
    </title>
    <contributor>
      <b034>1</b034>
      <b035>A01</b035>
      <b037>Poschadel, Jens</b037>
    </contributor>
    <b058>1., Aufl.</b058>
    <b061>32</b061>
    <b255>32</b255>
    <b062>m. zahlr. farb. Fotos</b062>
    <mainsubject>
      <b191>26</b191>
      <b069>12820</b069>
    </mainsubject>
    <mainsubject>
      <b191>26</b191>
      <b068>2.0</b068>
      <b069>1282</b069>
    </mainsubject>
    <subject>
      <b067>20</b067>
      <b070>Grundschule</b070>
    </subject>
    <subject>
      <b067>20</b067>
      <b070>Maulwurf</b070>
    </subject>
    <subject>
      <b067>24</b067>
      <b171>KNV type of article</b171>
      <b069>25</b069>
    </subject>
    <subject>
      <b067>24</b067>
      <b171>KNV group code</b171>
      <b069>000</b069>
    </subject>
    <audiencerange>
      <b074>17</b074>
      <b075>03</b075>
      <b076>4</b076>
      <b075>04</b075>
      <b076>6</b076>
    </audiencerange>
    <b207>ab 4 J.</b207>
    <othertext>
      <d102>02</d102>
      <d103>00</d103>
      <d104>Er ist der weltbeste Tunnelbauer und ein heimlicher Erdwerfer : Der Maulwurf lebt tief unter der Erde und ist tagaus und tagein damit beschäftigt, sich Nahrung zu suchen und seine perfekt entwickelten Hände zum Graben zu benutzen.</d104>
    </othertext>
    <publisher>
      <b291>01</b291>
      <b081>Esslinger Verlag Schreiber</b081>
    </publisher>
    <b394>02</b394>
    <b003>20140110</b003>
    <b087>2014</b087>
    <measure>
      <c093>01</c093>
      <c094>240</c094>
      <c095>mm</c095>
    </measure>
    <measure>
      <c093>02</c093>
      <c094>210</c094>
      <c095>mm</c095>
    </measure>
    <supplydetail>
      <supplieridentifier>
        <j345>06</j345>
        <b244>4026743000004</b244>
      </supplieridentifier>
      <j137>KNV</j137>
      <j292>04</j292>
      <j396>11</j396>
      <j260>00</j260>
      <j142>20140110</j142>
      <price>
        <j148>04</j148>
        <j267>33.3</j267>
        <j151>9.95</j151>
        <b251>DE</b251>
        <j153>R</j153>
      </price>
      <price>
        <j148>04</j148>
        <j151>10.3</j151>
        <b251>AT</b251>
      </price>
      <price>
        <j148>04</j148>
        <j151>14.9</j151>
        <j152>CHF</j152>
        <b251>CH</b251>
      </price>
    </supplydetail>
  </product>
XML;
        return $xml;
    }

    // Script end
    private function _resourceUsageTime($ru, $rus, $index) {
        $timeMilliseconds = ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
            -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
        $timeSeconds = $timeMilliseconds * 0.001;
        return $timeSeconds;
    }
}