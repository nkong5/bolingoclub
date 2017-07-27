<?php


class Kibithek_Home_Helper_FilterAjaxTest extends Kibithek_PHPUnit_TestCase
{

    /**
     * @test
     */
    public function testInstance()
    {
        $helper = Mage::helper('kibithek_home/filterAjax');
        $this->assertInstanceOf('Kibithek_Home_Helper_Data', $helper);
    }

    /**
     * @test
     */
    public function getResponse()
    {
        $helper = Mage::helper('kibithek_home/filterAjax');

        // exceptions when valid property is not set
        $exception = false;
        try{
            $helper->getResponse();
        }catch (Exception $e) {
            $exception = true;
        }
        $this->assertTrue($exception);

        $helper->setValid(true);
        $helper->setRedirectPath('buch/junge-noch-zu-hause-1-freunde-abenteuer');

        $response = $helper->getResponse();
        $this->assertArrayHasKey('valid', $response);
        $this->assertTrue($response['valid']);
    }

}
