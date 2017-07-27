<?php


class Kibithek_Home_Helper_DataTest extends Kibithek_PHPUnit_TestCase
{
    /**
     * @var Kibithek_Home_Helper_Data
     */
    protected $_helper;

    /**
     * Set up
     */
    public function setUp()
    {
        parent::setUp();
        $this->_helper = Mage::helper('kibithek_home');
    }

    /**
     * Tear down
     */
    public function tearDown()
    {
        $this->_helper = null;
        parent::tearDown();
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Kibithek_Home_Helper_Data', $this->_helper);
    }

}
