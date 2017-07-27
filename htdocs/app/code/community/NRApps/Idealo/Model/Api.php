<?php
class NRApps_Idealo_Model_Api
{
    const LIMIT_NUMBER_PRODUCTS_PER_RUN = 100;
    const LIMIT_NUMBER_RUNS = 10;
    const LIMIT_TIMEOUT_PER_PRODUCT = 3;

    protected $_runIndex = 0;

    public function sendKeepAlive()
    {
        if (Mage::getStoreConfigFlag('nrapps_idealo/settings/test_mode') || !Mage::getStoreConfigFlag('nrapps_idealo/settings/is_active')) {
            return;
        }

        $xml = $this->_getKeepAliveXml();

        $response = $this->_getResponseFromAPI($xml);

        $this->_logRequest($response, $xml);
    }
    
    public function transferAll() 
    {
        $this->deleteExistingOffersOnFirstRun();

        if (!Mage::getStoreConfigFlag('nrapps_idealo/settings/is_active')) {
            return;
        }

        foreach(Mage::app()->getStores() as $store) {

            if (!Mage::getStoreConfigFlag('nrapps_idealo/settings/is_active', $store->getId())) {
                continue;
            }

            if (Mage::getStoreConfigFlag('nrapps_idealo/settings/test_mode', $store->getId())) {
                continue;
            }

            try {
                $this->transferStoreData($store->getId());
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }
    
    public function transferStoreData($storeId)
    {
        /** @var $indexResource NRApps_Idealo_Model_Resource_Indexer */
        $indexResource = Mage::getResourceModel('nrapps_idealo/indexer');
        
        $canProcess = true;
        while ($canProcess && ++$this->_runIndex <= self::LIMIT_NUMBER_RUNS) {
            $dataToProcess = $indexResource->getDataToProcess($storeId, self::LIMIT_NUMBER_PRODUCTS_PER_RUN);

            if (sizeof($dataToProcess)) {
                $this->_processData($dataToProcess, $storeId);
            } else {
                if ($this->_runIndex == 1) {
                    $this->sendKeepAlive();
                }
                $canProcess = false;
            }
        }
    }

    /**
     * @param array $dataToProcess
     * @param int $storeId
     */
    protected function _processData($dataToProcess, $storeId)
    {
        $xml = $this->_getAssembledXml($dataToProcess);

        $response = $this->_getResponseFromAPI($xml, $storeId);
        
        $this->_updateIndexOnApiResponse($response, $xml);
    }

    protected function _getAssembledXml($dataToProcess)
    {
        $xml = '';
        
        /** @var $feed NRApps_Idealo_Model_Feed */
        $feed = Mage::getSingleton('nrapps_idealo/feed');

        if ($feed->getIncludeHeader()) {
            $xml .= $feed->getHeader() . $feed->getEol();
        }
        
        foreach($dataToProcess as $dataRow) {
            $xml .= $dataRow['data'] . $feed->getEol();
        }

        $xml .= $feed->getFooter();
        
        return $xml;
    }

    protected function _getKeepAliveXml()
    {
        $xml = '';

        /** @var $feed NRApps_Idealo_Model_Feed */
        $feed = Mage::getSingleton('nrapps_idealo/feed');

        if ($feed->getIncludeHeader()) {
            $xml .= $feed->getHeader() . $feed->getEol();
        }

        $xml .= $feed->getFooter();

        return $xml;
    }

    /**
     * @param $xml
     * @param $storeId
     * @return mixed
     * @throws Mage_Core_Exception
     */
    protected function _getResponseFromAPI($xml, $storeId = null)
    {
        $url = Mage::getStoreConfig('nrapps_idealo/settings/api_update_url', $storeId);
        $shopId = Mage::getStoreConfig('nrapps_idealo/settings/shop_id', $storeId);
        $userName = Mage::getStoreConfig('nrapps_idealo/settings/user', $storeId);
        $password = Mage::getStoreConfig('nrapps_idealo/settings/password', $storeId);

        $header = array(
            'shopId: ' . $shopId,
            'Content-Type: application/xml; charset=UTF-8',
            'Expect:' . '',
            'shopsystem: Magento',
            'shopsystemversion: ' . Mage::getVersion(),
            'idealomodulversion: ' . Mage::getConfig()->getModuleConfig('NRApps_Idealo')->version,
        );
        
        $process = curl_init($url);
        curl_setopt($process, CURLOPT_HTTPHEADER, $header);
        curl_setopt($process, CURLOPT_USERPWD, $userName . ':' . $password);
        curl_setopt($process, CURLOPT_TIMEOUT, self::LIMIT_TIMEOUT_PER_PRODUCT * self::LIMIT_NUMBER_PRODUCTS_PER_RUN);
        curl_setopt($process, CURLOPT_POST, true);
        curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($process, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
        if (Mage::getStoreConfigFlag('nrapps_idealo/settings/disable_certificate_check')) {
            curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        }
        $return = curl_exec($process);

        if (preg_match('/shop.*is.*not.*partner/i', $return)) {
            $this->_getSetup()->setConfigData(
                'nrapps_idealo/settings/is_active',
                0
            );
            $message = 'Shop is not a partner. ' . PHP_EOL . PHP_EOL;
            $message .= 'Request: ' . PHP_EOL . $xml . PHP_EOL . PHP_EOL;
            $message .= 'Response: ' . PHP_EOL . $return . PHP_EOL;
            $this->_handleApiError($message);
            throw new NRApps_Idealo_AccountDisabledException('Please check the access data and the state of the registration of your shop at idealo.');
        }

        if ($return === false) {
            $message = 'cURL Error ' . curl_errno($process) . ': ' . curl_error($process) . PHP_EOL . PHP_EOL;
            $message .= 'Request: ' . PHP_EOL . $xml . PHP_EOL . PHP_EOL;
            $this->_handleApiError($message);
            Mage::throwException($message);
        }
        
        if (substr(curl_getinfo($process, CURLINFO_HTTP_CODE), 0, 1) != 2) {
            $message = 'HTTP Status Code ' . curl_getinfo($process, CURLINFO_HTTP_CODE) . PHP_EOL . PHP_EOL;
            $message .= 'Request: ' . PHP_EOL . $xml . PHP_EOL . PHP_EOL;
            $message .= 'Response: ' . PHP_EOL . $return . PHP_EOL;
            $this->_handleApiError($message);

            Mage::throwException(
                'HTTP Status Code ' . curl_getinfo($process, CURLINFO_HTTP_CODE) . ' on updateOffers request. ' .
                'See var/log/idealo_exceptions.txt for details.'
            );
        }
        
        return $return;
    }

    protected function _updateIndexOnApiResponse($response, $xml)
    {
        $this->_logRequest($response, $xml);

        /** @var $indexResource NRApps_Idealo_Model_Resource_Indexer */
        $indexResource = Mage::getResourceModel('nrapps_idealo/indexer');

        try {
            $xml = new SimpleXMLElement($response);
            foreach ($xml->offerResponse as $xmlOfferResponse) {
                $sku = (string)$xmlOfferResponse->sku;
                $indexResource->markProcessedBySku($sku, (string)$xmlOfferResponse->status, (string)$xmlOfferResponse->statusMsg);
            }
        } catch (Exception $e) {
            $message = $e->getMessage() . PHP_EOL . PHP_EOL;
            $message .= 'Request: ' . PHP_EOL . $xml . PHP_EOL . PHP_EOL;
            $message .= 'Response: ' . PHP_EOL . $response . PHP_EOL;
            $this->_handleApiError($message);
            Mage::throwException($e);
        }
    }

    protected function _logRequest($response, $xml)
    {
        if (Mage::getStoreConfigFlag('nrapps_idealo/settings/logging')) {
            $message = 'Request: ' . PHP_EOL . $xml . PHP_EOL . PHP_EOL;
            $message .= 'Response: ' . PHP_EOL . $response . PHP_EOL;
            Mage::helper('nrapps_idealo')->log($message, null, 'idealo_requests.txt');
        }
    }

    /**
     * @param string $message
     */
    protected function _handleApiError($message)
    {
        Mage::helper('nrapps_idealo')->log($message, Zend_Log::ERR, 'idealo_exceptions.txt', true);

        // send email to administrator
        if ($emailAddresses = Mage::getStoreConfig('nrapps_idealo/email/error_recipient')) {

            foreach(explode(',', $emailAddresses) as $emailAddress) {

                $template = Mage::getStoreConfig('nrapps_idealo/email/error_template');
                $identity = Mage::getStoreConfig('nrapps_idealo/email/error_identity');

                $mailTemplate = Mage::getModel('core/email_template');

                // collect email variables
                $data = array(
                    'message' => $message,
                    'domain' => Mage::getStoreConfig('web/unsecure/base_url'),
                    'idealo_username' => Mage::getStoreConfig('nrapps_idealo/settings/user'),
                    'magento_version' => Mage::getVersion(),
                    'module_version' => Mage::getConfig()->getModuleConfig('NRApps_Idealo')->version,

                );

                try {
                    // send mail
                    $mailTemplate->sendTransactional($template, $identity, $emailAddress, '', $data);
                } catch (Exception $e) {

                    Mage::logException($e);
                }
            }
        }
    }

    public function deleteExistingOffersOnFirstRun()
    {
        foreach(Mage::app()->getStores() as $store) {

            $this->_deleteExistingOffersForStore($store);        
        }
    }
    
    /**
     * @param $storeId
     * @return mixed
     * @throws Mage_Core_Exception
     */
    protected function _getResponseFromGetAPI($storeId = null)
    {
        $url = Mage::getStoreConfig('nrapps_idealo/settings/api_get_url', $storeId);

        $params = array(
            'pageSize=' . self::LIMIT_NUMBER_PRODUCTS_PER_RUN,
            'pageNumber=' . 1,
        );

        if (strpos($url, '?') !== false) {
            $url .= '&';
        } else {
            $url .= '?';
        }

        $url .= implode('&', $params);

        $shopId = Mage::getStoreConfig('nrapps_idealo/settings/shop_id', $storeId);
        $userName = Mage::getStoreConfig('nrapps_idealo/settings/user', $storeId);
        $password = Mage::getStoreConfig('nrapps_idealo/settings/password', $storeId);

        $header = array(
            'shopId: ' . $shopId,
            'Content-Type: application/xml; charset=UTF-8',
            'Expect:' . '',
            'shopsystem: Magento',
            'shopsystemversion: ' . Mage::getVersion(),
            'idealomodulversion: ' . Mage::getConfig()->getModuleConfig('NRApps_Idealo')->version,
        );

        $process = curl_init($url);
        curl_setopt($process, CURLOPT_HTTPHEADER, $header);
        curl_setopt($process, CURLOPT_USERPWD, $userName . ':' . $password);
        curl_setopt($process, CURLOPT_TIMEOUT, self::LIMIT_TIMEOUT_PER_PRODUCT * self::LIMIT_NUMBER_PRODUCTS_PER_RUN);
        curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
        if (Mage::getStoreConfigFlag('nrapps_idealo/settings/disable_certificate_check')) {
            curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        }
        $return = curl_exec($process);

        if ($return === false) {
            Mage::throwException(curl_error($process));
        }

        if (substr(curl_getinfo($process, CURLINFO_HTTP_CODE), 0, 1) != 2) {
            $message = 'HTTP Status Code ' . curl_getinfo($process, CURLINFO_HTTP_CODE) . PHP_EOL . PHP_EOL;
            $message .= 'URL: ' . PHP_EOL . $url . PHP_EOL;
            $message .= 'Response: ' . PHP_EOL . $return . PHP_EOL;
            $this->_handleApiError($message);

            Mage::throwException(
                'HTTP Status Code ' . curl_getinfo($process, CURLINFO_HTTP_CODE) . ' on getOffers request. ' .
                'See var/log/idealo_exceptions.txt for details.'
            );
        }

        return $return;
    }

    /**
     * @param Mage_Core_Model_Store $store
     */
    protected function _deleteExistingOffersForStore($store)
    {
        $storeId = $store->getId();
        if (Mage::getStoreConfigFlag('nrapps_idealo/settings/test_mode', $storeId)
            || !Mage::getStoreConfigFlag('nrapps_idealo/settings/is_active', $storeId)
            || Mage::getStoreConfigFlag('nrapps_idealo/existing_offers_deleted', $storeId)
        ) {
            return;
        }

        $canProcess = true;
        while ($canProcess && ++$this->_runIndex <= self::LIMIT_NUMBER_RUNS) {
            $response = $this->_getResponseFromGetAPI($storeId);

            $this->_logRequest($response, 'getOffers');

            $responseXml = new SimpleXMLElement($response);

            if (sizeof($responseXml)) {
                $rows = array();
                foreach ($responseXml as $offer) {
                    if (!isset($offer->sku)) {
                        continue;
                    }
                    $rows[] = '
    <offer>
        <command>Delete</command>
        <sku>' . (string)$offer->sku . '</sku>
    </offer>';
                }

                $xml = Mage::getModel('nrapps_idealo/generator')->getFeedContent(false, $rows);

                $response = $this->_getResponseFromAPI($xml, $storeId);
                $this->_logRequest($response, $xml);
            } else {
                $canProcess = false;
                
                Mage::getResourceModel('core/setup', 'core_setup')->setConfigData('nrapps_idealo/existing_offers_deleted', 1, 'stores', $storeId);
                Mage::app()->getCacheInstance()->cleanType('config');
                Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => 'config'));
            }
        }
    }

    /**
     * @return Mage_Catalog_Model_Resource_Setup
     */
    protected function _getSetup()
    {
        return Mage::getResourceModel('catalog/setup', 'catalog_setup');
    }
}