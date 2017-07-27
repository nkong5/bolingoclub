<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nkongme
 * Date: 19.10.13
 * Time: 12:57
 * To change this template use File | Settings | File Templates.
 */

class Kibithek_Import_Model_Report_ProductTest extends Kibithek_PHPUnit_TestCase
{

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Kibithek_Import_Model_Report_Product', Mage::getModel('kibithek_import/report_product'));
    }

    /**
     * @test
     */
    public function save()
    {
        $model = Mage::getModel('kibithek_import/report_product');
        $model->addRecord(Zend_Log::CRIT, '987654321', '12344NAME.xml', 'Critical message');
        $model->addRecord(Zend_Log::DEBUG, '987654321', '12344NAME.xml', 'Debug message');
        $model->addRecord(Zend_Log::ERR, '987654321', '12344NAME.xml', 'Error message');
        $model->addRecord(Zend_Log::NOTICE, '987654321', '12344NAME.xml', 'Notice message');

        $result = $model->save();
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function mail()
    {
        $model = Mage::getModel('kibithek_import/report_product');
        $model->addRecord(Zend_Log::CRIT, '987654321', '12344NAME.xml', 'Critical message');
        $model->addRecord(Zend_Log::DEBUG, '987654321', '12344NAME.xml', 'Debug message');
        $model->addRecord(Zend_Log::ERR, '987654321', '12344NAME.xml', 'Error message');
        $model->addRecord(Zend_Log::NOTICE, '987654321', '12344NAME.xml', 'Notice message');

        $result = $model->save();
        $this->assertTrue($result);

        $result = $model->mail();
        $this->assertInstanceOf('Zend_Mail', $result);

    }

    /**
     * @test
     */
    public function getDir()
    {
        $model = Mage::getModel('kibithek_import/report_product');
        $result = $model->getDir();
        $result = realpath($result);
        $this->assertstring('String', $result);
        $this->assertContains('report', $result);
    }

    /**
     * @test
     */
    public function getReportName()
    {
        $model = Mage::getModel('kibithek_import/report_product');
        $result = $model->getName();
        $this->assertContains('product', $result);
    }

}