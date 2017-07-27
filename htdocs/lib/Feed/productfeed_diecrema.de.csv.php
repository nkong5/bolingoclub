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
$fName = 'productfeed_diecrema.de.csv';
$fNameNew = 'new.productfeed_diecrema.de.csv';
$file = ($fPath) ? "$fPath/$fNameNew" : $fNameNew;

$fhandleGuensigerDe = fopen($file, "w");

$products = Mage::getModel('catalog/product')
            ->getCollection()
//            ->addAttributeToSelect('*');
            ->addAttributeToSelect('media_gallery_images')
            ->addAttributeToSelect('brand')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
	    ->addAttributeToSelect('image')
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
    'id',
	'brand',
	'title',
    'price',
	'link',
	'image_link',
    'description',
    'shipping_weight',
    'shipping',
    'google_product_category',
    'condition',
    'availability',
    'gtin',
    'mpn',
    'identifier_exists' 
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

    $id = ($product->getSku()) ? $product->getSku() : '' ;
    $brand = ($brand)
        ? $brand : '';
    $title = ($product->getName())
        ? $product->getName() : '';

    // use special price as top priority
    $price = '';
    $basePrice = '';
    if ($product->getPrice()) $price = $basePrice = $product->getPrice();
    if ($product->getSpecialPrice()) $price = $product->getSpecialPrice();

    $shippingCost = calcDeliveryCost($price);

    $price = myMoneyFormat($price);
    $basePrice = myMoneyFormat($basePrice);

    $deliverPeriod = ($product->getAttributeText('delivery_period'))
        ? $product->getAttributeText('delivery_period') : '';
    $productLink = ($product->getProductUrl())
        ? $product->getProductUrl() : '';

   // $imageLink = (string) Mage::helper('catalog/image')
   //                 ->init($product, 'image')
   //                 ->resize(336)
   //                 ->setQuality(90);

    $imageLink = ($product->getImageUrl())
        ? $product->getImageUrl() : '';
    
    $productDescription = ($product->getDescription())
        ? removeNewLineReturn($product->getDescription())
        : '';
    $productWeight = ($product->getWeight())
        ? $product->getWeight() : '';

    $googleProductCategory = 'Nahrungsmittel, Getränke & Tabak > Getränke > Kaffee';
    $condition = 'New';
    $availability = 'in stock';
    $gtin = '';
    $mpn = '';
    $identifierExists = 'false';

    if (1 == $product->getStatus()) {
        $row = array(
            $id,
            $brand,
            $title,
            $price,
            $productLink,
            $imageLink,
            $productDescription,
            $productWeight,
            $shippingCost,
            $googleProductCategory,
            $condition,
            $availability,
            $gtin,
            $mpn,
            $identifierExists
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

?>
