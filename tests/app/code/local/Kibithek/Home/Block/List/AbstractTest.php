<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nkongme
 * Date: 22.09.13
 * Time: 00:32
 * To change this template use File | Settings | File Templates.
 */


class Kibithek_Home_Block_List_AbstractTest extends Kibithek_PHPUnit_TestCase
{
    /**
     * @var Kibithek_Home_Block_List_Abstract
     */
    protected $_block;

    /**
     * Set up
     */
    public function setUp()
    {
        parent::setUp();
        $this->_block = Mage::app()->getLayout()->createBlock('kibithek_home/list_abstractShell');
    }

    /**
     * Tear down
     */
    public function tearDown()
    {
        unset($this->_block);
        $this->_block = null;
        parent::tearDown();
    }

    /**
     * @test
     */
    public function checkBlockModel()
    {
        $this->assertInstanceOf('Kibithek_Home_Block_List_Abstract', $this->_block);
    }

}