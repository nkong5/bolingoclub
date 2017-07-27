<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nkongme
 * Date: 25.09.13
 * Time: 00:42
 * To change this template use File | Settings | File Templates.
 */

class Kibithek_Home_Block_List_Item_CheckboxTest extends Kibithek_PHPUnit_TestCase
{
    /**
     * @var Kibithek_Home_Helper_Data
     */
    protected $_block;

    /**
     * Set up
     */
    public function setUp()
    {
        parent::setUp();
        $this->_block = Mage::app()->getLayout()->createBlock('kibithek_home/list_item_checkbox');
    }

    /**
     * Tear down
     */
    public function tearDown()
    {
        $this->_block = null;
        parent::tearDown();
    }

    /**
     * @test
     */
    public function checkBlock()
    {
        $this->assertInstanceOf('Kibithek_Home_Block_List_Item_Checkbox', $this->_block);
    }


    /**
     * @test
     */
    public function getImageName()
    {
        $names = array('Mädchen', 'noch zu Hause', 'Meine Freunde & Abenteuer',
            'Fest- & Feiertage', 'Hand-Werks-Kunst', 'Märchen, Sagen & Fabeln');

        foreach($names as $name) {
            $result = $this->_sanitize($name);
            $this->assertInternalType('string', $result);
        }
    }

    private function _sanitize($string, $forceLowercase = true, $anal = false) {
        $umlaute = array("/ä/","/ö/","/ü/","/Ä/","/Ö/","/Ü/","/ß/", "/  /", "/ /");
        $replace = array("ae","oe","ue","Ae","Oe","Ue","ss", "_", "_");
        $string = preg_replace($umlaute, $replace, $string);

        $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(",
            ")", "&", "=", "+", "[", "{", "]", "}", "\\", "|", ";",
            ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;",
            "&#8221;", "&#8211;", "&#8212;", "â€”", "â€“", ",",
            "<", ".", ">", "/", "?", "-");
        $clean = trim(str_replace($strip, "", strip_tags($string)));
        $clean = preg_replace(array('/\s+/', '/__/'), array("", "_"), $clean);

        $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;

        return ($forceLowercase) ? strtolower($clean) : $clean;
    }
}