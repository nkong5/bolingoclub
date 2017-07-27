<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nkongme
 * Date: 25.09.13
 * Time: 00:42
 * To change this template use File | Settings | File Templates.
 */

class Kibithek_Tip_Block_Book_ClassificationTest extends Kibithek_PHPUnit_TestCase
{
    /**
     * @test
     */
    public function checkBlockInstance()
    {
        $block = $this->getLayout()->createBlock('kibithek_tip/book_classification');
        $this->assertInstanceOf('Kibithek_Home_Block_Book_Classification', $block);
    }


    /**
     * @test
     */
    public function validateBlock()
    {
        $id = $this->_getReadConnection()->fetchOne("select entity_id from kibithek_tip_book order by entity_id desc limit 1");
        $model = Mage::getModel('kibithek_tip/book')->load($id);

        if ($model->getId()) {
            $hash = $model->getHash();
            $block = Mage::app()->getLayout()->createBlock('kibithek_tip/book_classification');
            $block->setHash($hash);
            $result = $block->validate();
            $this->assertTrue($result);
            $title = $block->getTitle();
            $this->assertNotEmpty($title);
        }else{
            $this->assertFalse(FALSE);
        }
    }
}