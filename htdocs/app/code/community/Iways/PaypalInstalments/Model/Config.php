<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com and you will be sent a copy immediately.
 *
 * Created on 02.03.2015
 * Author Robert Hillebrand - hillebrand@i-ways.de - i-ways sales solutions GmbH
 * Copyright i-ways sales solutions GmbH © 2015. All Rights Reserved.
 * License http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

/**
 * Iways PayPalInstalments Model Instalments
 *
 * @category   Iways
 * @package    Iways_PaypalInstalments
 * @author robert
 */
class Iways_PaypalInstalments_Model_Config extends Mage_Paypal_Model_Config
{

    /**
     * Instructions for generating proper BN code
     *
     * @var array
     */
    protected $_buildNotationPPMap = array(
        'paypal_standard'  => 'WPS',
        'paypal_express'   => 'EC',
        'paypal_direct'    => 'DP',
        'paypaluk_express' => 'EC',
        'paypaluk_direct'  => 'DP',
        'iways_paypalinstalments' => 'EC'
    );

    /**
     * PayPal Instalments
     * @var string
     */
    const METHOD_INSTALMENTS         = 'iways_paypalinstalments';

    /**
     * PayPal Instalments BN Code
     */
    const BN_CODE = 'Magento_Cart_Installments_DE';

    /**
     * Config path for enabling/disabling order review step in express checkout
     */
    const XML_PATH_PAYPAL_EXPRESS_SKIP_ORDER_REVIEW_STEP_FLAG = 'payment/paypal_express/skip_order_review_step';


    /**
     * Return list of allowed methods for specified country iso code
     *
     * @param string $countryCode 2-letters iso code
     * @return array
     */
    public function getCountryMethods($countryCode = null)
    {
        return $countryCode == 'DE' ? array(self::METHOD_INSTALMENTS) : array();
    }

    /**
     * Map any supported payment method into a config path by specified field name
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _getSpecificConfigPath($fieldName)
    {
        $path = null;

        if($this->_methodCode == self::METHOD_INSTALMENTS) {
            $path = $this->_mapInstalmentsFieldset($fieldName);
        } else {
            $path = parent::_getSpecificConfigPath($fieldName);
        }
        if ($path === null) {
            $path = $this->_mapGeneralFieldset($fieldName);
        }
        if ($path === null) {
            $path = $this->_mapGenericStyleFieldset($fieldName);
        }
        if($path === null) {
            $path = $this->_mapWppFieldset($fieldName);
        }
        return $path;
    }

    /**
     * Map PayPal Instalments Settings
     *
     * @param $fieldName
     * @return null|string
     */
    protected function _mapInstalmentsFieldset($fieldName)
    {
        switch ($fieldName)
        {
            case 'active':
            case 'title':
            case 'payment_action':
            case 'debug':
            case 'line_items_summary':
            case 'sandbox_flag':
            case 'min_order_total':
            case 'line_items_enabled':
            case 'allowspecific':
            case 'specificcountry':
            case 'client_id':
            case 'client_secret':
                return 'payment/' . self::METHOD_INSTALMENTS . "/{$fieldName}";
            default:
                return $this->_mapMethodFieldset($fieldName);
        }
    }

    /**
     * Get url for dispatching customer to express checkout start
     *
     * @param string $token
     * @return string
     */
    public function getExpressCheckoutStartUrl($token)
    {
        return $this->getPaypalUrl(array(
            'token' => $token,
        ));
    }

    /**
     * Return start url for PayPal Basic
     *
     * @param string $token
     * @return string
     */
    public function getPayPalBasicStartUrl($token)
    {
        $params = array(
            'token' => $token,
        );

        if ($this->isOrderReviewStepDisabled()) {
            $params['useraction'] = 'commit';
        }

        return $this->getPaypalUrl($params);
    }

    /**
     * PayPal web URL generic getter modified for new url
     *
     * @param array $params
     * @return string
     */
    public function getPaypalUrl(array $params = array())
    {
        return sprintf('https://www.%spaypal.com/checkoutnow/2%s',
            $this->sandboxFlag ? 'sandbox.' : '',
            $params ? '?' . http_build_query($params) : ''
        );
    }

    /**
     * Payment actions source getter
     *
     * @return array
     */
    public function getPaymentActions()
    {
        return array(
            self::PAYMENT_ACTION_SALE => Mage::helper('paypal')->__('Sale'),
            self::PAYMENT_ACTION_ORDER => Mage::helper('paypal')->__('Order')
        );
    }

    /**
     * Check whether order review step enabled in configuration
     *
     * @return bool
     */
    public function isOrderReviewStepDisabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PAYPAL_EXPRESS_SKIP_ORDER_REVIEW_STEP_FLAG);
    }

    /**
     * @return mixed
     */
    public function getBuildNotationCode()
    {
        return self::BN_CODE;
    }
}