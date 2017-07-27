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
 * Iways PayPalInstalments Model Api Rest
 *
 * @category   Iways
 * @package    Iways_PaypalInstalments
 * @author Robert Hillebrand - hillebrand@i-ways.de - i-ways sales solutions GmbH
 */
class Iways_PaypalInstalments_Model_Api_Rest extends Varien_Object
{
    /**
     * @var mixed
     */
    protected $clientId;
    /**
     * @var mixed
     */
    protected $clientSecret;
    /**
     * @var mixed
     */
    protected $sandboxFlag;
    /**
     * Cache key
     *
     * @var string
     */
    const AUTH_CACHE_KEY = 'paypal_rest_auth';
    /**
     * Cache lifetime
     *
     * @var int
     */
    const AUTH_CACHE_LIFETIME = 7200;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->clientId = Mage::getStoreConfig('payment/iways_paypalinstalments/client_id');
        $this->clientSecret = Mage::getStoreConfig('payment/iways_paypalinstalments/client_secret');
        $this->sandboxFlag = Mage::getStoreConfig('payment/iways_paypalinstalments/sandbox_flag');
    }

    /**
     * Get finace information for given amount
     *
     * @param float $amt
     * @return array|mixed
     */
    public function getFinanceInfo($amt)
    {
        return $this->request('v1/credit/calculated-financing-options', Zend_Http_Client::POST, array(
            'financing_country_code' => Mage::helper('iways_paypalinstalments')->getConfigurationCountryCode(),
            'transaction_amount' => array(
                'value' => $amt,
                'currency_code' => Mage::app()->getStore()->getCurrentCurrencyCode()
            )
        ));
    }

    /**
     * Performs an request to the PayPal Rest Api
     */
    protected function request($url, $method = Zend_Http_Client::GET, $params = array())
    {
        try {
            if (!$this->clientId || !$this->clientSecret) {
                return array();
            }
            $request = new Zend_Http_Client($this->getPayPalUrl($url));
            $request->setHeaders($this->getStandardHeaders());
            if ($method != Zend_Http_Client::POST) {
                $request->setParameterGet($params);
            } else {
                $request->setRawData(json_encode($params));
            }
            return json_decode($request->request($method)->getBody());
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return array();
    }

    /**
     * Get default headers
     *
     * @param bool|true $withAuth
     * @return array
     */
    protected function getStandardHeaders($withAuth = true)
    {
        $headers = array('Accept' => 'application/json', 'Content-Type' => 'application/json');
        if ($withAuth) {
            $headers['Authorization'] = 'Bearer ' . $this->getAccessToken();
        }
        return $headers;
    }

    /**
     * Get access token
     *
     * @return mixed
     * @throws Zend_Http_Client_Exception
     */
    protected function getAccessToken()
    {
        $auth = Mage::app()->loadCache(self::AUTH_CACHE_KEY);
        if (!$auth) {
            $auth = $this->refreshAccessToken();
        }
        $auth = json_decode($auth);
        return $auth->access_token;
    }

    /**
     * Refresh access token
     *
     * @return string
     * @throws Mage_Core_Exception
     * @throws Zend_Http_Client_Exception
     */
    protected function refreshAccessToken()
    {
        $client = new Zend_Http_Client($this->getPayPalUrl('v1/oauth2/token'));
        $client->setAuth($this->clientId, $this->clientSecret);
        $client->setHeaders($this->getStandardHeaders(false));
        $client->setParameterPost('grant_type', 'client_credentials');
        $auth = $client->request(Zend_Http_Client::POST)->getBody();
        $authArray = json_decode($auth);
        if (!isset($authArray->access_token)) {
            Mage::throwException('PayPal Access Token could not be retrieved.');
        }
        Mage::app()->saveCache($auth, self::AUTH_CACHE_KEY, array('block_html'), self::AUTH_CACHE_LIFETIME);
        return $auth;
    }

    /**
     * Get PayPal endpoint url (sandbox/live)
     *
     * @param string $endpoint
     * @return string
     */
    protected function getPayPalUrl($endpoint = '')
    {
        return sprintf('https://api.%spaypal.com/%s',
            $this->sandboxFlag ? 'sandbox.' : '', ltrim($endpoint, '/'));
    }

}