<?php


class Kibithek_Tip_Helper_Book_EmailTest extends Kibithek_PHPUnit_TestCase
{
    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Kibithek_Tip_Helper_Book_Email', Mage::helper('kibithek_tip/book_email'));
    }


    /**
     * @test
     * @expectedException Kibithek_Tip_Exception
     */
    public function sendThrowsExceptionIfEmailTemplateDoesntExist()
    {
        $helper = Mage::helper('kibithek_tip/book_email');
        $helper->setTemplateId('FAKE TEMPLATE ID XXXX');
        $data = $this->_getRequiredFields();
        $helper->send($data);
    }

    /**
     * @return array
     */
    private function _getRequiredFields()
    {
        $model = Mage::getModel('kibithek_tip/book');
        $hash = Mage::helper('kibithek_tip/book')->getHash();
        return array(
            $model::FIELD_TITLE => 'My Test title',
            $model::FIELD_PREFIX => 'Herr',
            $model::FIELD_FIRSTNAME => 'Ed',
            $model::FIELD_LASTNAME => 'Nkongme',
            $model::FIELD_EMAIL => 'biz@bongkishiy.de',
            $model::FIELD_HASH => $hash,
            $model::FIELD_AGREEMENT => 1
        );
    }

}
