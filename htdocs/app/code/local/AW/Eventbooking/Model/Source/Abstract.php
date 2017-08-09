<?php

abstract class AW_Eventbooking_Model_Source_Abstract
{
    protected $_optionArray = null;

    public function toOptionArray()
    {
        if ($this->_optionArray === null) {
            $this->_optionArray = $this->_toOptionArray();
        }
        return $this->_optionArray;
    }

    /**
     * Returns array(value => ..., label => ...) for option with given value
     * @param string $value
     * @return array
     */
    public function getOption($value)
    {
        foreach ($this->toOptionArray() as $_option) {
            if ($_option['value'] == $value) {
                return $_option;
            }
        }
        return false;
    }

    /**
     * Get label for option with given value
     * @param string $value
     * @return string
     */
    public function getOptionLabel($value)
    {
        $_option = $this->getOption($value);
        if (!$_option) {
            return false;
        }
        return $_option['label'];
    }

    /**
     * Returns associative array $value => $label
     * @return array
     */
    public function toShortOptionArray()
    {
        $_options = array();
        foreach ($this->toOptionArray() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

    protected function _getHelper($ext = '')
    {
        return Mage::helper('aw_eventbooking' . ($ext ? '/' . $ext : ''));
    }

    abstract protected function _toOptionArray();
}
