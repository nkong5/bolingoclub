<?php

/**
 *
 *
 * @author     Edgar Bongkishiy <ed@bongkishiy.de>
 *
 * @backupGlobals disabled
 */

/**
 *
 * Base class for all Model Test cases
 *
 */
abstract class Diecrema_PHPUnit_TestCase extends PHPUnit_Framework_TestCase
{

    /**
     * put required modules that should
     * be enabled to run tests
     *
     * leave empty to skip check
     *
     * @var array
     */
    protected $_requiredModules = null;

    /**
     *
     * @var Varien_Db_Adapter_Pdo_Mysql
     */
    protected $_readConnection = null;


    /**
     *
     * @var array
     */
    protected $_changedStoreConfigKeys = array();


    /**
     *
     * caches the systems current store property
     *
     * @var Mage_Core_Model_Store
     */
    protected $_currentStore = null;


    /**
     * init
     */
    protected function setUp()
    {
        $this->_currentStore = Mage::app()->getStore();
        parent::setUp();
        if (($modules = $this->_requiredModules) && !empty($modules)) {
            $disabledModules = array();
            foreach ($modules as $name) {
                $enabled = Mage::helper('diecrema_base')->isModuleEnabled($name);
                if (!$enabled) {
                    $disabledModules[] = $name;
                }
            }
            if (!empty($disabledModules)) {
                $str = join(',', $disabledModules);
                $this->markTestSkipped("Required module(s) '$str' is/are not enabled");
                return;
            }
        }
    }

    /**
     * test done, and clean up
     *
     */
    protected function tearDown()
    {
        $this->_resetStoreConfigChanges();
        /* reset the systems current store,
         * only if changed */
        if (isset($this->_currentStore) && (Mage::app()->getStore() !== $this->_currentStore)) {
            Mage::app()->setCurrentStore($this->_currentStore);
        }
        $this->_currentStore = null;
        parent::tearDown();
    }

    /**
     *
     * returns a simple read connection
     * to database
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _getReadConnection()
    {
        if (!isset($this->_readConnection)) {
            $this->_readConnection = Mage::getSingleton('core/resource')->getConnection('core/read');
        }
        return $this->_readConnection;
    }

    /**
     *
     * returns a simple write connection
     * to database
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _getWriteConnection()
    {
        if (!isset($this->_readConnection)) {
            $this->_readConnection = Mage::getSingleton('core/resource')->getConnection('core/write');
        }
        return $this->_readConnection;
    }


    /**
     *
     */
    protected function _setStoreConfigValue($path, $value, Mage_Core_Model_Store $store = null)
    {
        $store = ($store) ? $store : Mage::app()->getStore();
        $code = $store->getCode();
        if (!isset($this->_changedStoreConfigKeys[$code])) {
            $this->_changedStoreConfigKeys[$code] = array();
        }
        $store->setCacheConfigValue($path, $value);
        $this->_changedStoreConfigKeys[$code][$path] = true;
    }


    /**
     *
     */
    protected function _resetStoreConfigChanges()
    {
        foreach ($this->_changedStoreConfigKeys as $storeCode => $pathes) {
            $store = Mage::app()->getStore($storeCode);
            foreach ($pathes as $path => $value) {
                $store->setCacheConfigValue($path, null);
            }
        }
    }


    /**
     *
     *
     * @param string | int | Mage_Core_Model_Store $store
     *
     */
    protected function _setCurrentStore($store)
    {
        if (is_string($store)) {
            $store = Mage::app()->getStore($store);
        } if (is_integer($store)) {
            $store = Mage::app()->getStore($store);
        }
        if (is_array($store) || !is_object($store)) {
            throw new Exception ("invalid store type");
        }
        if (!$store instanceof Mage_Core_Model_Store) {
            throw new Exception ("invalid store model");
        }
        Mage::app()->setCurrentStore($store);
    }

}