<?php


class Kibithek_Share_Helper_DataTest extends Kibithek_PHPUnit_TestCase
{
    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Kibithek_Share_Helper_Email_Mail', Mage::helper('kibithek_share/email_mail'));
    }

    /**
     * @test
     * @expectedException Kibithek_Share_Exception
     */
    public function sendThrowExceptionIfDataparameterNotValid()
    {
        $model = Mage::getModel('kibithek_share/stats');
        $data = array(
            $model::FIELD_SENDER => 'Antonio',
            $model::FIELD_RECIPIENT_EMAILS => 'ed@bongkishiy.de, biz@bongkishiy.de'
//            $model::FIELD_MESSAGE = 'Hallo ihr Lieben, - guten Tag!
//Wir m&ouml;chten euch in die Kibithek auf www.kibithek.de einladen, - die Fundgrube f&uuml;r ein passendes Kinderbuch.
//Liebe Gr&uuml;&szlig;e'
        );

        $helper = Mage::helper('kibithek_share/email_mail');
        $helper->send($data);
    }

//    /**
//     * @test
//     */
//    public function send()
//    {
//        $helper = Mage::helper('kibithek_share/email_mail');
//        $model = Mage::getModel('kibithek_share/stats');
//        $data = array(
//            $model::FIELD_SENDER => 'Antonio',
//            $model::FIELD_RECIPIENT_EMAILS => 'ed@bongkishiy.de, biz@bongkishiy.de',
//            $model::FIELD_MESSAGE => 'Hallo ihr Lieben, - guten Tag!
//Wir m&ouml;chten euch in die Kibithek auf www.kibithek.de einladen, - die Fundgrube f&uuml;r ein passendes Kinderbuch.
//Liebe Gr&uuml;&szlig;e',
//            $model::FIELD_AGREEMENT => 1
//        );
//        $result = $helper->send($data);
//        $this->assertTrue($result);
//    }

    /**
     * @test
     */
    public function splitEmails()
    {
        $helper = Mage::helper('kibithek_share/email_mail');
        $emails = 'some@some.de,';
        $result = $helper->splitEmails($emails);
        $this->assertEquals(1, count($result));

        $emails = ',some@some.de,one@one.de,';
        $result = $helper->splitEmails($emails);
        $this->assertEquals(2, count($result));
    }


}
