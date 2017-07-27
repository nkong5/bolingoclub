<?php

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

if (function_exists('apc_clear_cache')) {
    apc_clear_cache();
}

define('ENVIRONMENT', 'test');
define ('HOSTNAME', 'diecrema.local');

putenv('MAGE_THEME=diecrema');
putenv('MAGE_SKIN=diecrema');
putenv('MAGE_MEDIA_THEME=unique');
putenv('MAGE_DEFAULT_THEME=unique,default');
putenv('MAGE_URL_SECURE=https://diecrema.local/');
putenv('MAGE_URL_UNSECURE=http://diecrema.local/');

$basePath = realpath(dirname(__FILE__));
$shopRoot = realpath($basePath . DIRECTORY_SEPARATOR . '../htdocs');
$localLibDir = realpath($basePath . DIRECTORY_SEPARATOR . 'lib');
$testLibDir = realpath($basePath . DIRECTORY_SEPARATOR . 'app/lib');
$testModuleDir = realpath($basePath . DIRECTORY_SEPARATOR . 'app/code');

include "$shopRoot/inc/init.php";

$pathes = array(
    '.',
    $shopRoot,
    $localLibDir,
    $testLibDir,
    $testModuleDir .'/local',
    get_include_path()
);
set_include_path(join(PATH_SEPARATOR, $pathes));



/* include magento libs */
require_once 'app/Mage.php';

//echo getenv('MAGE_DEFAULT_THEME');


/**
 * Pseudo error handler. If function returns false the
 * default PHP error handler will be triggered.
 *
 * @return boolean
 */
function diecremaTestErrorHandler()
{
    return false;
}


Varien_Profiler::disable();

umask(0);


Mage::$headersSentThrowsException = false;

/* init magento engine */
Mage::setIsDeveloperMode(true);
$app = Mage::app('german');
$app->setUseSessionInUrl(false);


$store = $app->getStore('german');
$store->setCacheConfigValue(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL, getenv('MAGE_URL_UNSECURE'));
$store->setCacheConfigValue(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL, getenv('MAGE_URL_UNSECURE'));

/** 
 * setup/prepare backend design, 
 * notice: order export renders templates
 */


srand();

if (!isset($_SESSION)) {
    $session = Mage::getSingleton('core/session');
}
