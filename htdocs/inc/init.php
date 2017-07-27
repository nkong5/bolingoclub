<?php
/**
 * @package     Vorwerk
 * @category    Vorwerk
 * @author      Edgar Bongkishiy <ed@bongkishiy.de>
 * @version     0.1
 *
 * 
 */

/* default values for environment variables */
(strlen((string) getenv('ENVIRONMENT')))            || putenv('ENVIRONMENT=dev');
(strlen((string) getenv('MAGE_USE_STORE_IN_URL')))  || putenv('MAGE_USE_STORE_IN_URL=1');
(strlen((string) getenv('MAGE_RUN_CODE')))          || putenv('MAGE_RUN_CODE=');
(strlen((string) getenv('MAGE_RUN_TYPE')))          || putenv('MAGE_RUN_TYPE=store');
(strlen((string) getenv('MAGE_THEME')))             || putenv('MAGE_THEME=diecrema');
(strlen((string) getenv('MAGE_DEFAULT_THEME')))     || putenv('MAGE_DEFAULT_THEME=diecrema');
(strlen((string) getenv('MAGE_SKIN')))              || putenv('MAGE_SKIN=diecrema');
(strlen((string) getenv('MAGE_MEDIA_THEME')))       || putenv('MAGE_MEDIA_THEME=diecrema');
(strlen((string) getenv('MAGE_URL_SECURE')))        || putenv('MAGE_URL_SECURE=https://diecrema-magento1pt9.local/');
(strlen((string) getenv('MAGE_URL_UNSECURE')))      || putenv('MAGE_URL_UNSECURE=http://diecrema-magento1pt9.local/');

/* set the environment and check */
defined('ENVIRONMENT') || define('ENVIRONMENT', trim(getenv('ENVIRONMENT')));
if (!in_array(ENVIRONMENT, array('dev', 'test', 'int', 'uat', 'prod'))) {
   throw new Exception("Invalid environment. The environment must be one of 'dev', 'test', 'int', 'uat', 'prod'.");        
}

/* define the applications base path */
define('BASE_PATH', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR));

if (!strlen(trim(getenv('MAGE_DEV_MODE')))) {
    $mode = in_array(ENVIRONMENT, array('prod')) ? '0' : '1';
    putenv('MAGE_DEV_MODE=' . $mode);
}

umask(0);
