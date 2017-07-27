<?php

class NRApps_Idealo_Model_Filter extends Varien_Filter_Template
{
    const CONSTRUCTION_EACH_PATTERN = '/{{foreach\s(.*?)as\s(.*?)}}(.*?){{\\/foreach\s*}}/si';

    /**
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        try {

            $pattern = self::CONSTRUCTION_EACH_PATTERN;
            $directive = 'foreachDirective';

            if (preg_match_all($pattern, $value, $constructions, PREG_SET_ORDER)) {
                foreach ($constructions as $construction) {
                    $replacedValue = '';
                    $callback = array($this, $directive);
                    if (!is_callable($callback)) {
                        continue;
                    }
                    try {
                        $replacedValue = call_user_func($callback, $construction);
                    } catch (Exception $e) {
                        Mage::throwException($e);
                    }
                    $value = str_replace($construction[0], $replacedValue, $value);
                }
            }

            $value = parent::filter($value);
        } catch (Exception $e) {
            Mage::throwException(Mage::helper('nrapps_idealo')->__('The filter can not be applied to the template'));
        }

        return $value;
    }

    /**
     *
     * @param array $construction
     * @return string
     */
    public function foreachDirective($construction)
    {
        if (count($this->_templateVars) == 0) {
            // If template preprocessing
            return $construction[0];
        }

        if ($this->_getVariable($construction[1], '') == '') {
            return '';
        } else {

            $value = '';
            foreach ($this->_getVariable($construction[1]) as $item) {
                $subFilter = new self;
                $subFilter->setVariables(array($construction[2] => $item));
                $value .= $subFilter->filter($construction[3]);
            }

            return $value;
        }
    }

    public function varDirective($construction)
    {
        if (count($this->_templateVars)==0) {
            // If template preprocessing
            return $construction[0];
        }

        $maxLength = null;
        $variable = $construction[2];
        $colonPosition = strpos($variable, ':');
        if ($colonPosition !== false) {
            $maxLength = substr($variable, $colonPosition + 1);
            $variable = substr($variable, 0, $colonPosition);
        }
        $replacedValue = $this->_getVariable($variable, '');
        
        if (!is_null($maxLength)) {
            $replacedValue = substr($replacedValue, 0, $maxLength);
        }
        
        return $replacedValue;
    }
}