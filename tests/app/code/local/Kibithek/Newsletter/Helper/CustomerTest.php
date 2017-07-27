<?php


class Kibithek_Newsletter_Helper_CustomerTest extends Kibithek_PHPUnit_TestCase
{
    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Kibithek_Newsletter_Helper_CustomerTest', Mage::helper('kibithek_newsletter/customer'));
    }


    /**
     * @test
     * @expectedException Kibithek_Newsletter_Exception
     */
    public function isValidThrowsExceptionIfRequiredFieldIsMissing ()
    {
        $helper = Mage::helper('kibithek_newsletter/customer');
        $params = array(
            'prefix' => 'Mr',
            'firstname' => 'Max',
            'lastname' => 'Mustermann',
            'email' => 'max@domain.de',
            'agreement' => 1
        );

        $helper->isValid($params);
    }


}
