<?php


class Diecrema_Home_Helper_PagecontentTest extends Diecrema_PHPUnit_TestCase
{
    /**
     * @var Diecrema_Home_Helper_Pagecontent
     */
    protected $_helper;

    /**
     * Set up
     */
    public function setUp()
    {
        parent::setUp();
        $this->_helper = Mage::helper('diecrema_startpage/pagecontent');
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
        $this->assertInstanceOf('Diecrema_Startpage_Helper_Pagecontent', $this->_helper);
    }

    public function testPagecontent()
    {
        $result  = $this->_helper->pagecontent();
        $this->assertInternalType('array', $result);

    }

}
