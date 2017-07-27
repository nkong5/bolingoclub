<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nkongme
 * Date: 19.10.13
 * Time: 12:57
 * To change this template use File | Settings | File Templates.
 */

class Kibithek_Import_Model_ProductTest extends Kibithek_PHPUnit_TestCase
{

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Kibithek_Import_Model_Product', Mage::getModel('kibithek_import/product'));
    }

    /**
     * @test
     */
    public function generateUrlKey()
    {
        $model = Mage::getModel('kibithek_import/product');
        $model->name = 'Der Maulwurf';
        $model->attributeAuthor = 'Jens Poschadel';
        $model->attributeProductIdValue = '9783480231393';
        $expected = 'der-maulwurf-jens-poschadel-9783480231393';
        $result = $model->generateUrlKey();
        $this->assertEquals($expected, $result);

        $model = Mage::getModel('kibithek_import/product');
        $model->name = 'Weißt du auch, was in der Nacht Fledermausi gerne macht?';
        $model->attributeAuthor = 'Werner, Brigitte';
        $model->attributeProductIdValue = '9783772527821';
        $expected = 'weisst-du-auch-was-in-der-nacht-fledermausi-gerne-macht-werner-brigitte-9783772527821';
        $result = $model->generateUrlKey();
        $this->assertEquals($expected, $result);

        $model = Mage::getModel('kibithek_import/product');
        $model->name = 'Rosas schlimmste Jahre - Wie überlebe ich mein Leben ohne dich?';
        $model->attributeAuthor = 'Oomen, Francine';
        $model->attributeProductIdValue = '9783473352913';
        $expected = 'rosas-schlimmste-jahre-wie-uberlebe-ich-mein-leben-ohne-dich-oomen-francine-9783473352913';
        $result = $model->generateUrlKey();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function getProductIdName()
    {
        $model = Mage::getModel('kibithek_import/product');
        $model->attributeProductIdCode = '03';
        $model->attributeProductIdValue = '9783649613565';
        $result = $model->getProductIdName();
        $expected = $model::PRODUCT_IDENTIFIER_NAME_ISBN;
        $this->assertEquals($expected, $result);

        $model = Mage::getModel('kibithek_import/product');
        $model->attributeProductIdCode = '03';
        $model->attributeProductIdValue = '4050003922157';
        $result = $model->getProductIdName();
        $expected = $model::PRODUCT_IDENTIFIER_NAME_ISBN_EAN;
        $this->assertEquals($expected, $result);

    }
}