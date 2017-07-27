<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nkongme
 * Date: 12.10.13
 * Time: 09:33
 * To change this template use File | Settings | File Templates.
 */

class Kibithek_Base_Helper_SanitizeTest extends Kibithek_PHPUnit_TestCase
{
    /**
     * @test
     */
    public function testInstance()
    {
        $helper = Mage::helper('kibithek_base/sanitize');
        $this->assertInstanceOf('Kibithek_Base_Helper_Sanitize', $helper);
        $this->assertInstanceOf('Mage_Core_Helper_Abstract', $helper);
    }

    /**
     * @test
     */
    public function urlMustContainOnlyOneDash()
    {
        $helper = Mage::helper('kibithek_base/sanitize');
        $collection = Mage::getResourceModel('kibithek_category/flat_collection');
        $this->assertInstanceOf('Mage_Core_Model_Mysql4_Collection_Abstract', $collection);
        $items = $collection->getItems();
        foreach ($items as $item) {
            $name = $item->getCategoryMainName();
            $url = $helper->url($name);
            $result = strpos($url, '--');
            $this->assertFalse($result);

            // check names of children
            $res = Mage::getResourceModel('kibithek_category/flat_collection');
            $children = $res->getMainItemChildren($item);
            $count = $children->count();
            foreach($children as $child) {
                $name = $child->getCategoryMainName();
                $url = $helper->url($name);
                $result = strpos($url, '--');
                $this->assertFalse($result);
            }
        }
    }

    public function testPlaygroundPopulateCsv ()
    {
        $file = "/Users/nkongme/Dev/kibi/shop/src/shell/importProducts/onixList.csv";
        $lines = array (
          array('my line 1'),
          array('my line 2'),
          array('my line 3'),
          array('my line 4'),
          array('my line 5'),
        );
        $this->populateCsv($file, $lines);
    }

    public function testPlaygroundDeleteLineFromCsv ()
    {
        $file = "/Users/nkongme/Dev/kibi/shop/src/shell/importProducts/onixList.csv";
        $line = 'my line 3';
        $this->deleteCsvLine($file, $line);
    }



    /**
     * Populates CSV file
     *
     * @param string $csv   Csv
     * @param array  $lines Lines
     *
     * @return void
     */
    protected function populateCsv($csv, array $lines)
    {
        $fp = fopen($csv, 'w');
        foreach ($lines as $line) {
            fputcsv($fp, $line);
        }
        fclose($fp);
    }

    /**
     * Deletes a line from CSV
     *
     * @param string $csv  Csv
     * @param string $line Line
     *
     * @return void
     */
    protected function deleteCsvLine($csv, $line)
    {
        $data = array();
        if (($handle = fopen($csv, "r")) !== FALSE) {
            while (($lineArr = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (!in_array($line, $lineArr)) {
                    $data[] = $lineArr;
                }
            }
            fclose($handle);

            $this->populateCsv($csv, $data);
        }
    }
}