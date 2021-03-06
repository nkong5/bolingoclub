<?php

/**
 *
 *
 * @copyright triplesense
 * @author Edgar Bongkishiy <ed@bongkishiy.de>
 * @version 0.1
 * @package Diecrema_Logger
 *
 *
 */

/**
 *
 *
 *
 */
class Diecrema_Logger_Memory extends Zend_Log_Writer_Abstract
{

    protected $_buffer = null;

    /**
     * 
     */
    public function __construct()
    {
        $this->clear();
    }

    public function clear()
    {
        $this->_buffer = array();
    }

    /**
     * Write a message to the log.
     *
     * @param  array  $event  log data event
     * @return void
     */
    protected function _write($event)
    {
        $this->_buffer[] = $event;
    }
    
    
    /**
     * Construct a Zend_Log driver
     * 
     * @param  array|Zen_Config $config
     * @return Zend_Log_FactoryInterface
     */
    static public function factory($config)
    {
        return new self();
    }
    
    
    /**
     * 
     * @return type
     */
    public function getBuffer()
    {
        return $this->_buffer;
    }

}