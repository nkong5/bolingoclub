<?php 
ini_set('memory_limit','128M');

// allow all errors be reported
ini_set("display_errors","on"); ERROR_REPORTING(E_ALL);   

function pr($value) {
    echo '<pre>'.print_r($value, true).'</pre>';
}

function prdie($value) {
    pr($value);
    exit;
}


//$env can be production, testing, development
$env = 'production';

switch ($env) {
    case 'production':
        // Load Up Magento Core
        define('MAGENTO',
        realpath('/srv/www/www.diecrema.de/htdocs')
        );

        $fPath = MAGENTO . '/var/csv';
        break;
    case 'testing':
        define('MAGENTO',
        realpath('/var/www/magento/htdocs')
        );

        $fPath = MAGENTO . '/var/csv';
        break;
    case 'development':
        define('MAGENTO',
        realpath('/Users/nkongme/Dev/diecrema/shop_magento1pt9/htdocs')
        );

        $fPath = MAGENTO . '/var/csv';
        break;
    default:
        break;
}

require_once(MAGENTO . '/app/Mage.php');

Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);

umask(0);

$app = Mage::app();


/*****************************************************************
 * CSV für günstiger.de erstellen 
 *****************************************************************/
$fName = 'produktliste_diecrema.de.csv';
$fNameNew = 'new.produktliste_diecrema.de.csv';
$file = ($fPath) ? "$fPath/$fNameNew" : $fNameNew;
$fhandleGuensigerDe = fopen($file, "w");

$products = Mage::getModel('catalog/product')
            ->getCollection()
//->addAttributeToSelect('*');
            ->addAttributeToSelect('media_gallery_images')
            ->addAttributeToSelect('brand')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('special_price')
            ->addAttributeToSelect('attribute_text')
            ->addAttributeToSelect('delivery_period')
            ->addAttributeToSelect('product_url')
            ->addAttributeToSelect('image_url')
            ->addAttributeToSelect('description')
            ->addAttributeToSelect('weight')
            ->addAttributeToSelect('category_ids')
            ->addAttributeToSelect('status')
;
$titles = array(
    'Bestellnummer',
    'HerstellerArtNr',
	'Hersteller',
	'ProduktBezeichnung',
    'Preis',
    'Lieferzeit',
	'ProduktLink',
	'FotoLink',	
    'ProduktBeschreibung',	
    'EANCode',	
    'Gewichtinkg',	
    'VersandVorkasse',
    'VersandNachnahme',	
    'VersandKreditkarte',	
    'VersandLastschrift',	
    'VersandBankeinzug',	
    'VersandRechnung',	
	'VersandPayPal',	
    'VersandClickAndBuy',	
    'VersandGoogleCheckout',	
    'VersandGiropay',	
    'VersandMoneybookers',
    'VersandPaysafecard', 
    'VersandSofortueberweisung',
    'ProductCategory',
    'GrundPreis',
    'NachnahmeUebermittlungsentgelt',	
    'VersandOesterreich',
    'VersandFrankreich',
    'VersandGrossbritannien'			
);            

// write titles
fputcsv($fhandleGuensigerDe, $titles, '|');   

// for money_format()
setlocale(LC_MONETARY, 'de_DE');

foreach ($products as $product) {
// product ist of Class  Mage_Catalog_Model_Product

//    prdie($product);
//    $productData = $product->getData();    
    if($product->getMediaGalleryImages()) prdie($product->getMediaGalleryImages());

    $brand = $product->getResource()->getAttribute('brand')
                ->getFrontend()->getValue($product);
  
    $bestellNr = ($product->getSku()) ? $product->getSku() : '' ;
    $herstellerArtNr = '';
    $hersteller = ($brand)
        ? $brand : ''; 
    $produktBezeichnung = ($product->getName())
        ? $product->getName() : '';

    // use special price as top priority        
    $price = '';
    $grundPreis = '';
    if ($product->getPrice()) $price = $grundPreis = $product->getPrice();
    if ($product->getSpecialPrice()) $price = $product->getSpecialPrice();
    
    $versandKosten = calcDeliveryCost($price);
    
    $price = myMoneyFormat($price);
    $grundPreis = myMoneyFormat($grundPreis);
        
    $lieferzeit = ($product->getAttributeText('delivery_period'))
        ? $product->getAttributeText('delivery_period') : ''; 
    $produktLink = ($product->getProductUrl())
        ? $product->getProductUrl() : '';
    $fotoLink = ($product->getImageUrl())
        ? $product->getImageUrl() : '';
    $produktBeschreibung = ($product->getDescription())
        ? removeNewLineReturn($product->getDescription())         
        : '';
    $eanCode = '';
    $produktGewicht = ($product->getWeight())
        ? $product->getWeight() : '';
    $versandVorkasse = $versandKosten;
    $versandNachnahme = myMoneyFormat(6); // number_format(6.000, 3, '.', ''); // 6.00; //money_format('%!.2n', 60000); //number_format(6.00, 2, '.', '');
    $versandKreditkarte = $versandKosten;
    $versandLastschrift = $versandKosten;
    $versandBankeinzug = $versandKosten;
    $versandRechnung = $versandKosten;
    $versandPayPal = $versandKosten;
    $versandClickAndBuy = $versandKosten;
    $versandGoogleCheckout = $versandKosten;
    $versandGiropay = $versandKosten;
    $versandMoneybookers = $versandKosten;
    $versandPaysafecard = $versandKosten;
    $versandSofortueberweisung = $versandKosten;    
    
    // building of breadcrumb
    $categoryIds = $product->getCategoryIds();
    
    $breadCrumbArr = array();
    
    foreach($categoryIds as $categoryId) {
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $breadCrumbArr[] =  $category->getName();
    }
    
    $produktCategory = implode(' > ', $breadCrumbArr);
    
    $nachnahmeUebermittlungsentgelt = myMoneyFormat(2); 
           
    $versandOesterreich = myMoneyFormat(23);
    $versandFrankreich = myMoneyFormat(23);
    $versandGrossbritannien = myMoneyFormat(35);	
    
    if (1 == $product->getStatus()) {
        $row = array(
            $bestellNr, 
            $herstellerArtNr,
            $hersteller,
            $produktBezeichnung,
            $price,
            $lieferzeit,
            $produktLink,
            $fotoLink,	
            $produktBeschreibung,
            $eanCode,
            $produktGewicht,	
            $versandVorkasse,
            $versandNachnahme,
            $versandKreditkarte,	
            $versandLastschrift,	
            $versandBankeinzug,
            $versandRechnung,	
            $versandPayPal,	
            $versandClickAndBuy,	
            $versandGoogleCheckout,	
            $versandGiropay,	
            $versandMoneybookers,
            $versandPaysafecard, 
            $versandSofortueberweisung,
            $produktCategory,
            $grundPreis,
            $nachnahmeUebermittlungsentgelt,
            $versandOesterreich,
            $versandFrankreich,
            $versandGrossbritannien
        );
        
        $row = purifyString($row);
        $data[] = $row;
        
        // write rows
        fputcsv($fhandleGuensigerDe, $row, '|', '"');    
    }   
}

fclose($fhandleGuensigerDe);

if (!rename("$fPath/$fNameNew", "$fPath/$fName")) {
    echo "failed moving $fPath/$fNameNew...\n";
}


if ($env == 'development') pr($data);

/**
 * 
 * Calculates the delivery cost ...
 * @param string $figure
 */
function calcDeliveryCost ($figure)
{
   //pr($figure);
    $price = 6;
    if ($figure > 150) $price = 0;    
    return money_format('%!.2n', $price);    
}

// BEGIN CODE convert all price amounts into well formatted values
function convertMoneyFormatToNum($convertnum,$fieldinput){
        $bits = explode(",",$convertnum); // split input value up to allow checking
       
        $first = strlen($bits[0]); // gets part before first comma (thousands/millions)
        $last = strlen($bits[1]); // gets part after first comma (thousands (or decimals if incorrectly used by user)
       
        if ($last <3){ // checks for comma being used as decimal place
            $convertnum = str_replace(",",".",$convertnum);
        }
        else{ // assume comma is a thousands seperator, so remove it
            $convertnum = str_replace(",","",$convertnum);
        }
       
        $_POST[$fieldinput] = $convertnum; // redefine the vlaue of the variable, to be the new corrected one
} 

/**
 * 
 * Returns the currency money format here ...
 * @param string $figure
 */
function myMoneyFormat($figure)
{
    return money_format('%!.2n', $figure);    
}

/**
 * 
 * Enter description here ...
 * @param array $productInfos
 */
function purifyString ($productInfos) {
    foreach ($productInfos as &$info) {
       // remove html tags  
       $info = strip_tags($info);  
       
       // decode string to utf8
       $info = utf8_decode($info);  
       
       //do html and character decoding
       $info = html_entity_decode($info);

    }
    
    return $productInfos;
}

function removeNewLineReturn ($str)
{
    return str_replace(array("\n", "\r"), ' ', $str);    
}


//function myFputcsv (&$handle, $fields = array(), $delimiter = ',', $enclosure = '"') {
//    $str = '';
//    $escape_char = '\\';
//    foreach ($fields as $value) {
//      if (strpos($value, $delimiter) !== false ||
//          strpos($value, $enclosure) !== false ||
//          strpos($value, "\n") !== false ||
//          strpos($value, "\r") !== false ||
//          strpos($value, "\t") !== false ||
//          strpos($value, ' ') !== false) {
//        $str2 = $enclosure;
//        $escaped = 0;
//        $len = strlen($value);
//        for ($i=0;$i<$len;$i++) {
//          if ($value[$i] == $escape_char) {
//            $escaped = 1;
//          } else if (!$escaped && $value[$i] == $enclosure) {
//            $str2 .= $enclosure;
//          } else {
//            $escaped = 0;
//          }
//          $str2 .= $value[$i];
//        }
//        $str2 .= $enclosure;
//        $str .= $str2.$delimiter;
//        
//      } else {
//        $str .= $value.$delimiter;
//      }
//    }
//    $str = substr($str,0,-1);
//    $str .= "\n";
//        
//    return fwrite($handle, $str);
//}


//$list = array (
//    array('aaa', 'bbb', 'ccc', 'dddd'),
//    array('123', '456', '789'),
//    array('meinedatei', 'deine', 'ihre')
//);
//
//$simpleCsv = new SimpleCsv();
//$simpleCsv->createCsv($list, 'mycsv');
//
//class SimpleCsv
//{
//    /**
//     * 
//     * Outputs a CSV-File of the following format: 
//     * $list = array (
//            array('aaa', 'bbb', 'ccc', 'dddd'),
//            array('123', '456', '789'),
//            array('"aaa"', '"bbb"')
//        );
//     *   
//     * @param array $data
//     * @param string $fileName
//     * @param string $filePath
//     */
//    public function createCsv($data, $fileName, $filePath = '')
//    {
//        
//        $file = (empty($filePath))
//            ?  "$fileName.csv" : "$filePath/$fileName.csv";
//        
//        $fp = fopen($file, 'w');
//        
//        foreach ($data as $fields) {
//            fputcsv($fp, $fields);
//        }
//        
//        fclose($fp);
//        
//    }
//
//}
?>
