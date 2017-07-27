<?php
class NRApps_Idealo_Model_Generator extends Mage_Core_Model_Abstract
{
    /** @var Mage_Core_Model_Store */
    protected $_store;

    /** @var Mage_Catalog_Model_Entity_Attribute[] */
    protected $_attributes = array();

    /** @var string[] */
    protected $_categoryNames = array();

    /** @var array int[]|null */
    protected $_excludedCategoryIds = null;

    /** @var string[] */
    protected $_categoryPaths = array();

    /** @var array[] */
    protected $_categoryAttributeValues = array();

    /** @var array[] */
    protected $_categoryAttributeLevels = array();

    /** @var string[] */
    protected $_usedAttributeCodes = array();

    /** @var string[] */
    protected $_productSkusById = array();

    /** @var Mage_Catalog_Model_Product */
    protected $_product;

    /**
     * @param Mage_Core_Model_Store $store
     */
    public function __construct(Mage_Core_Model_Store $store)
    {
        parent::__construct();

        $this->_init('nrapps_idealo/indexer');

        $this->_store = $store;
    }

    /**
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_store;
    }

    /**
     * @return NRApps_Idealo_Model_Feed
     */
    public function getFeed()
    {
        return Mage::getSingleton('nrapps_idealo/feed');
    }

    /**
     * @return string
     */
    public function getFeedType()
    {
        return $this->getFeed()->getType();
    }

    /**
     * @param bool $useIndex
     * @param string[] $alternativeContent
     * @return string
     */
    public function getFeedContent($useIndex = true, $alternativeContent = array())
    {
        $feedContent = array();
        if ($this->getFeed()->getIncludeHeader()) {
            $feedContent[] = $this->_getHeader();
        }

        if ($useIndex) {
            foreach ($this->getProductDataFromIndex() as $productRow) {
                $feedContent[] = $productRow['data'];
            }
        } else {
            foreach($alternativeContent as $row) {
                $feedContent[] = $row;
            }
        }

        if ($footer = $this->_getFooter()) {
            $feedContent[] = $footer;
        }

        return implode($this->_getEol(), $feedContent);
    }

    /**
     * @return string
     */
    public function generateFile()
    {
        $filename = $this->_getFilename();

        $dirname = Mage::getBaseDir('media') . DS . 'nrapps_idealo' . DS;

        if (!is_dir($dirname)) {
            mkdir($dirname, 0777, true);
        }

        $path = $dirname . $filename;

        $fileHandle = fopen($path, 'w');

        if ($this->getFeed()->getIncludeHeader()) {
            $this->_writeLineToFile($fileHandle, $this->_getHeader());
        }

        $feedIndexDbStatement = $this->_getFeedIndexDbStatement();
        $productData = array();
        $i = 0;
        // write product data to file in chunks
        while ($productRow = $feedIndexDbStatement->fetch()) {
            $productData[] = $productRow['data'];
            if ($i++ > 1000) {
                $i = 0;
                $this->_writeLineToFile($fileHandle, $productData);
                $productData = array();
            }
        }
        $this->_writeLineToFile($fileHandle, $productData);

        if ($footer = $this->_getFooter()) {
            $this->_writeLineToFile($fileHandle, $footer);
        }

        fclose($fileHandle);

        return $filename;
    }

    /**
     * @return Zend_Db_Statement
     */
    protected function _getFeedIndexDbStatement()
    {
        $condition = $this->_getResource()->getReadConnection()->quoteInto('store_id = ?', $this->_getStoreId());
        $condition .= ' AND ' . $this->_getResource()->getReadConnection()->quoteInto('feed_type = ?', $this->getFeedType());
        $select = $this->_getResource()->getReadConnection()->select()
            ->from($this->_getResource()->getTable('nrapps_idealo/index'), array('data'))
            ->where($condition);

        $statement = $this->_getResource()->getReadConnection()->query($select);
        return $statement;
    }

    /**
     * @param resource $fileHandle
     * @param string $content
     */
    protected function _writeLineToFile($fileHandle, $content)
    {
        if (is_array($content)) {
            $content = implode($this->_getEol(), $content);
        }
        if (!trim($content)) {
            return;
        }

        $content .= $this->_getEol();

        fwrite($fileHandle, $content);
    }

    /**
     * @return string
     */
    protected function _getFilename()
    {
        if ($filename = $this->getFeed()->getFilename()) {
            return $filename;
        }
        return 'idealo_' . $this->_getStoreId() . '_' . $this->getStore()->getCode() . '.' . $this->getFeed()->getType();
    }

    /**
     * @return string
     */
    protected function _getEol()
    {
        $search = array('\n', '\r', '\t');
        $replacement = array("\n", "\r", "\t");

        return str_replace($search, $replacement, $this->getFeed()->getEol());
    }

    /**
     * @return string
     */
    protected function _getHeader()
    {
        $search = array('\n', '\r', '\t');
        $replacement = array("\n", "\r", "\t");

        return str_replace($search, $replacement, $this->getFeed()->getHeader());
    }

    /**
     * @return string
     */
    protected function _getBody()
    {
        $search = array('\n', '\r', '\t');
        $replacement = array("\n", "\r", "\t");

        return str_replace($search, $replacement, $this->getFeed()->getBody());
    }

    /**
     * @return string
     */
    protected function _getFooter()
    {
        $search = array('\n', '\r', '\t');
        $replacement = array("\n", "\r", "\t");

        return str_replace($search, $replacement, $this->getFeed()->getFooter());
    }

    /**
     * @return array
     */
    public function getProductDataFromIndex()
    {
        return Mage::getResourceSingleton('nrapps_idealo/indexer')->getDataByStoreId($this->_getStoreId());
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getProductData(Mage_Catalog_Model_Product $product)
    {
        $product->setStoreId($this->_getStoreId());

        /* @var $processor Mage_Cms_Model_Template_Filter */
        $processor = Mage::getModel('nrapps_idealo/filter');

        $productData = $this->_getProductData($product);

        $productData = $this->_getFilteredValue($productData);

        $processor->setVariables($productData);

        return $processor->filter($this->_getBody());
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _getProductData(Mage_Catalog_Model_Product $product)
    {
        $product->setStockItem(Mage::getModel('cataloginventory/stock_item')->loadByProduct($product));

        $productDataTransport = new Varien_Object();
        Mage::dispatchEvent('nrapps_idealo_generate_product_data_before', array('product' => $product, 'product_data' => $productDataTransport, 'store_id' => $this->getStore()->getId()));

        $productData = $productDataTransport->getData();

        if ($this->_isAttributeUsed('base_price') || $this->_isAttributeUsed('base_price_raw')) {

            $this->_loadDefaultBasePriceValues($product);

            if ($this->_isAttributeUsed('base_price')) {
                $productData['base_price'] = $this->_getBasePrice($product);
            }

            if ($this->_isAttributeUsed('base_price_raw')) {
                $productData['base_price_raw'] = $this->_getBasePriceRaw($product);
            }
        }

        foreach ($product->getData() as $key => $value) {
            if ($this->_isAttributeUsed($key)) {
                $productData[$key] = $this->_getAttributeValue($product, $key);

                $productDataRaw = $this->_getAttributeValueRaw($product, $key);
                if ($productDataRaw !== null) {
                    $productData[$key . '__raw'] = $productDataRaw;
                }
            }
        }

        $productData['offer_name'] = $this->_getOfferName($product);

        $productData['url'] = $this->_getEncodedValue($this->_getProductUrl($product));

        $productData['qty'] = $product->getStockItem()->getQty();
        $productData['is_in_stock'] = $product->getStockItem()->getIsInStock();

        $productData['price'] = ( $this->_getPrice($product) ? $this->_formatPrice($this->_getPrice($product)) : '' );

        $productData['currency_code'] = $this->getStore()->getBaseCurrencyCode();
        $productData['country_code'] = Mage::getStoreConfig('general/country/default', $this->_getStoreId());
        $productData['language_code'] = current(explode('_', Mage::getStoreConfig('general/locale/code', $this->_getStoreId())));
        $productData['store_code'] = $this->getStore()->getCode();
        $productData['is_downloadable'] = ( $product->getTypeId() == 'downloadable' );

        $productCategoryIds = $this->_getCategoryIds($product);
        if ($this->_isAttributeUsed('category_names') || $this->_isAttributeUsed('category_name')) {
            $productData['category_names'] = $this->_getCategoryNamesByIds($productCategoryIds);
            $productData['category_name'] = current($productData['category_names']);
        }
        if ($this->_isAttributeUsed('category_paths') || $this->_isAttributeUsed('category_path') || $this->_isAttributeUsed($this->_getCategoryAttributeCodes())) {
            $productData['category_paths'] = $this->_getCategoryPathsByIds($productCategoryIds);
            $productData['category_path'] = current($productData['category_paths']);
            foreach ($this->_getCategoryAttributeCodes() as $attributeCode) {
                if ($this->_isAttributeUsed($attributeCode)) {
                    if (!$product->getData($attributeCode)) {
                        $storedLevel = 0;
                        foreach ($productCategoryIds as $categoryId) {
                            if (isset($this->_categoryAttributeValues[$attributeCode][$categoryId]) && $this->_categoryAttributeValues[$attributeCode][$categoryId] && $this->_categoryAttributeLevels[$attributeCode][$categoryId] > $storedLevel) {
                                $storedLevel = $this->_categoryAttributeLevels[$attributeCode][$categoryId];
                                $productData[$attributeCode] = $this->_getEncodedValue($this->_categoryAttributeValues[$attributeCode][$categoryId]);
                            }
                        }
                    }
                }
            }
        }

        $this->_addImageData($product, $productData);

        if ($product->getParentProductIds()) {
            $productData['parent_product_sku'] = $this->_getSkuByProductId(current($product->getParentProductIds()));
        }

        // Manage custom attributes defined in attribute management
        foreach (Mage::getStoreConfig('nrapps_idealo/attributes') as $attributeIdentifier => $attributeCode) {
            if ($attributeIdentifier == 'shipping_cost') {
                continue;
            } else if (!isset($productData[$attributeIdentifier]) || !$productData[$attributeIdentifier]) {
                if ($attributeCode != 'none' && $this->_isAttributeUsed($attributeIdentifier)) {
                    if (!strlen($product->getData($attributeCode))) {
                        if ($this->_getAttribute($attributeCode)->getSourceModel() == 'nrapps_idealo/source_yesNoDefault') {
                            $productData[$attributeIdentifier] = intval(Mage::getStoreConfig('nrapps_idealo/default_values/' . $attributeIdentifier, $this->_getStoreId()));
                        } else {
                            $productData[$attributeIdentifier] = $this->_getEncodedValue(Mage::getStoreConfig('nrapps_idealo/default_values/' . $attributeIdentifier, $this->_getStoreId()));
                        }
                    } else {
                        $productData[$attributeIdentifier] = $this->_getAttributeValue($product, $attributeCode);
                    }
                }
            }
        }

        $this->_addAdditionalAttributesData($productData);
        
        foreach(explode(',', Mage::getStoreConfig('nrapps_idealo/listing/listing_at', $this->_getStoreId())) as $listingCountry) {
            $this->_addPaymentMethodData($productData, $listingCountry);
        }
        $this->_addMinimumOrderAmountData($productData);
        $this->_addShippingCommentsData($productData);
        $this->_addDeliveryTimesData($productData);

        $productDataTransport->setData($productData);

        Mage::dispatchEvent('nrapps_idealo_generate_product_data_after', array('product' => $product, 'product_data' => $productDataTransport, 'store_id' => $this->getStore()->getId(), 'generator' => $this));

        return $productDataTransport->getData();
    }

    /**
     * generate image urls
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $productData
     */
    protected function _addImageData(Mage_Catalog_Model_Product $product, &$productData)
    {
        unset($productData['image']);
        unset($productData['small_image']);
        unset($productData['thumbnail']);

        foreach (array('image', 'small_image', 'thumbnail') as $imageAttributeCode) {
            if ($this->_isAttributeUsed($imageAttributeCode)) {
                $productImage = $this->_getProductImage($product, $imageAttributeCode);
                if ($productImage && file_exists(Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $productImage)) {
                    $productData[$imageAttributeCode] = Mage::getBaseUrl('media') . 'catalog/product' . $productImage;
                }
            }
        }

        $productData['additional_images'] = array();
        if (is_object($product->getMediaGalleryImages())) {
            $i = 1;
            foreach ($product->getMediaGalleryImages() as $image) {
                // limit to 10 images
                if ($i > 10) {
                    break;
                }
                $productData['additional_image_' . $i++] = Mage::getBaseUrl('media') . 'catalog/product' . $image->getFile();
                $productData['additional_images'][] = Mage::getBaseUrl('media') . 'catalog/product' . $image->getFile();
            }
        }
    }

    /**
     * add data for additional attributes
     *
     * @param array $productData
     */
    protected function _addAdditionalAttributesData(&$productData)
    {
        $productData['additional_attributes'] = array();
        foreach (unserialize(Mage::getStoreConfig('nrapps_idealo/product_options/add_attributes_to_export', $this->_getStoreId())) as $row) {

            /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            $attribute = $this->_getAttribute($row['attribute_code']);
            if (!$attribute instanceof Mage_Catalog_Model_Resource_Eav_Attribute) {
                return;
            }
            if (!isset($productData[$attribute->getAttributeCode()])) {
                continue;
            }

            $productData['additional_attributes'][] = new Varien_Object(array(
                'attribute_code' => $attribute->getFrontendLabel(),
                'attribute_value' => $productData[$attribute->getAttributeCode()],
            ));
        }
    }

    /**
     * add data for payment methods
     *
     * @param array $productData
     * @param string $listingCountry
     */
    protected function _addPaymentMethodData(&$productData, $listingCountry)
    {
        $productData['shipping_cost'] = $this->_getEncodedValue($this->_getDefaultShippingCost($productData['price'], $productData['weight'], $listingCountry));
        if (!isset($productData['shipping_costs'])) {
            $productData['shipping_costs'] = array();
        }
        foreach (unserialize(Mage::getStoreConfig('nrapps_idealo/' . $this->_getConfigGroup($listingCountry) . '/payment_methods', $this->_getStoreId())) as $row) {

            if (!isset($productData['shipping_cost'])) {
                $productData['shipping_cost'] = 0;
            }
            $shippingCost = $productData['shipping_cost'] + floatval($row['surcharge']);
            if (isset($row['percental_surcharge']) && $row['percental_surcharge']) {

                $percentalSurcharge = floatval($row['percental_surcharge']);
                if (Mage::getStoreConfig('nrapps_idealo/' . $this->_getConfigGroup($listingCountry) . '/percental_surcharge_calculation_method') ==
                    NRApps_Idealo_Model_Source_PercentalSurchargeCalculationMethod::CALCULATION_METHOD_PRODUCT_PRICE_AND_SHIPPING_COST) {

                    $shippingCost += $shippingCost * $percentalSurcharge / 100;
                }
                $productPrice = $productData['price'];
                if (isset($productData['special_price']) && $productData['special_price']) {

                    $productPrice = $productData['special_price'];
                }
                $shippingCost += $productPrice * $percentalSurcharge / 100;
            }

            if (Mage::getStoreConfigFlag('nrapps_idealo/minimum_order_surcharge/is_active')) {
                $productPrice = $productData['price'];
                if (isset($productData['special_price']) && $productData['special_price']) {
                    $productPrice = $productData['special_price'];
                }
                $minimumOrderAmountForSurcharge = Mage::getStoreConfig('nrapps_idealo/minimum_order_surcharge/minimum_order_value');
                if ($minimumOrderAmountForSurcharge > $productPrice) {
                    $shippingCost += Mage::getStoreConfig('nrapps_idealo/minimum_order_surcharge/surcharge');
                }
            }

            if (Mage::getStoreConfigFlag('nrapps_idealo/minimum_order_weight_surcharge/is_active')) {

                if ($productWeight = $productData['weight']) {
                    $minimumOrderWeightForSurcharge = Mage::getStoreConfig('nrapps_idealo/minimum_order_weight_surcharge/minimum_order_weight');
                    if ($minimumOrderWeightForSurcharge > $productWeight) {
                        $shippingCost += Mage::getStoreConfig('nrapps_idealo/minimum_order_weight_surcharge/surcharge');
                    }
                }
            }

            $shippingCost = number_format($shippingCost, 2);

            $productData['shipping_cost_' . $row['payment_method']] = $shippingCost;
            $productData['shipping_costs'][] = new Varien_Object(array(
                'payment_method' => $row['payment_method'],
                'shipping_cost' => $shippingCost,
                'context' => $this->_getContext($listingCountry),
            ));
            $productData['shipping_cost_' . $row['payment_method'] . $this->_getSuffix($listingCountry)] = $shippingCost;
        }
    }

    /**
     * add data for shipping comments
     *
     * @param array $productData
     */
    protected function _addShippingCommentsData(&$productData)
    {
        $productData['shipping_comments'] = array();
        foreach (unserialize(Mage::getStoreConfig('nrapps_idealo/default_values/shipping_comments', $this->_getStoreId())) as $row) {

            $comment = $row['comment'];
            if (isset($productData['shipping_comment']) && trim($productData['shipping_comment'])) {
                $comment .= $this->_getLineDivider() . $productData['shipping_comment'];
            }
            $productData['shipping_comments'][] = new Varien_Object(array(
                'shipping_comment' => $comment,
                'context' => $this->_getContext($row['listing_at'])
            ));
            $productData['shipping_comment' . $this->_getSuffix($row['listing_at'])] = $comment;
        }
    }

    /**
     * add data for delivery times
     *
     * @param array $productData
     */
    protected function _addDeliveryTimesData(&$productData)
    {
        if (!isset($productData['delivery_times'])) {
            $productData['delivery_times'] = array();
        }

        if (!isset($productData['delivery_time']) || !$productData['delivery_time']) {
            return;
        }
            
        foreach (unserialize(Mage::getStoreConfig('nrapps_idealo/delivery_times/mapping', $this->_getStoreId())) as $row) {

            if ($row['attribute_value'] == $productData['delivery_time']) {
                foreach(explode(',', Mage::getStoreConfig('nrapps_idealo/listing/listing_at', $this->_getStoreId())) as $listingCountry) {
                    if (isset($row[$listingCountry]) && $row[$listingCountry]) {
                        $productData['delivery_times'][] = new Varien_Object(array(
                            'delivery_time' => $row[$listingCountry],
                            'context' => $this->_getContext($listingCountry)
                        ));
                        $productData['delivery_time' . $this->_getSuffix($listingCountry)] = $row[$listingCountry];
                    }
                }
                break;
            }
        }
    }

    /**
     * add data for minimum order value and minimum order surcharge
     *
     * @param array $productData
     */
    protected function _addMinimumOrderAmountData(&$productData)
    {
        if (!isset($productData['shipping_comment'])) {
            $productData['shipping_comment'] = '';
        }

        if (Mage::getStoreConfigFlag('sales/minimum_order/active') || Mage::getStoreConfigFlag('nrapps_idealo/minimum_order_surcharge/is_active')) {
            $productPrice = $productData['price'];
            if (isset($productData['special_price']) && $productData['special_price']) {
                $productPrice = $productData['special_price'];
            }
            $minimumOrderAmount = Mage::getStoreConfig('sales/minimum_order/amount');
            if (Mage::getStoreConfigFlag('sales/minimum_order/active') && $minimumOrderAmount > $productPrice) {
                if ($productData['shipping_comment']) {
                    $productData['shipping_comment'] .= $this->_getLineDivider();
                }
                $productData['shipping_comment'] .= Mage::helper('nrapps_idealo')->__(
                    'Minimum order amount: %s',
                    Mage::helper('core')->formatCurrency($minimumOrderAmount, false)
                );
            }
            $minimumOrderAmountForSurcharge = Mage::getStoreConfig('nrapps_idealo/minimum_order_surcharge/minimum_order_value');
            if (Mage::getStoreConfigFlag('nrapps_idealo/minimum_order_surcharge/is_active') && $minimumOrderAmountForSurcharge > $productPrice) {
                if ($productData['shipping_comment']) {
                    $productData['shipping_comment'] .= $this->_getLineDivider();
                }
                $productData['shipping_comment'] .= Mage::helper('nrapps_idealo')->__(
                    'For orders below %s, a surcharge of %s will be charged.',
                    Mage::helper('core')->formatCurrency($minimumOrderAmountForSurcharge, false),
                    Mage::helper('core')->formatCurrency(Mage::getStoreConfig('nrapps_idealo/minimum_order_surcharge/surcharge'), false)
                );
            }
        }

        if (Mage::getStoreConfigFlag('nrapps_idealo/minimum_order_weight_surcharge/is_active')) {

            if ($productWeight = $productData['weight']) {
                $minimumOrderWeightForSurcharge = Mage::getStoreConfig('nrapps_idealo/minimum_order_weight_surcharge/minimum_order_weight');
                if ($minimumOrderWeightForSurcharge > $productWeight) {
                    if ($productData['shipping_comment']) {
                        $productData['shipping_comment'] .= $this->_getLineDivider();
                    }
                    $productData['shipping_comment'] .= Mage::helper('nrapps_idealo')->__(
                        'For orders below a weight of %s %s, a surcharge of %s will be charged.',
                        $minimumOrderWeightForSurcharge,
                        Mage::getStoreConfig('nrapps_idealo/minimum_order_weight_surcharge/unit'),
                        Mage::helper('core')->formatCurrency(Mage::getStoreConfig('nrapps_idealo/minimum_order_weight_surcharge/surcharge'), false)
                    );
                }
            }
        }
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    protected function _getPrice(Mage_Catalog_Model_Product $product)
    {
        if ($product->getParentProductIds()) {

            if (Mage::getStoreConfig('nrapps_idealo/product_options/configurable_child_products_price', $this->_getStoreId())
                == NRApps_Idealo_Model_Source_ConfigurableChildProductsPriceType::PRICE_TYPE_PARENT_WITH_MODIFICATIONS) {

                /** @var $parentProduct Mage_Catalog_Model_Product */
                $parentProduct = $this->_getProduct(current($product->getParentProductIds()));

                /** @var $productTypeInstance Mage_Catalog_Model_Product_Type_Configurable */
                $productTypeInstance = $parentProduct->getTypeInstance(true);

                /** @var $configurableAttributes array */
                $configurableAttributes = $productTypeInstance->getConfigurableAttributesAsArray($parentProduct);

                /**
                 * Required to determinate product child price
                 * @see Mage_Catalog_Model_Product_Type_Configurable_Price::getFinalPrice()
                 */
                $customOptionAttributes = array();
                foreach ($configurableAttributes as $attribute) {
                    $customOptionAttributes[$attribute['attribute_id']] = $product->getData($attribute['attribute_code']);
                }

                $parentProduct->addCustomOption('attributes', serialize($customOptionAttributes));

                /** @var $priceModel Mage_Catalog_Model_Product_Type_Configurable_Price */
                $priceModel = Mage::getModel('catalog/product_type_configurable_price');

                return $priceModel->getFinalPrice(null, $parentProduct);
            }

            if (Mage::getStoreConfig('nrapps_idealo/product_options/configurable_child_products_price', $this->_getStoreId())
                == NRApps_Idealo_Model_Source_ConfigurableChildProductsPriceType::PRICE_TYPE_PARENT_WITHOUT_MODIFICATIONS) {

                return Mage::getResourceSingleton('catalog/product')->getAttributeRawValue(current($product->getParentProductIds()), 'price', $product->getStoreId());
            }
        }

        if ($product->isGrouped()) {

            $childrenIds = $product->getTypeInstance(true)->getChildrenIds($product->getId());

            if (!sizeof($childrenIds)) {
                return 0;
            }

            $usedAttributes = array('price', 'special_price', 'special_from_date', 'special_to_date');

            foreach (Mage::getStoreConfig('nrapps_idealo/attributes') as $attributeCode => $newAttributeCode) {
                if ($newAttributeCode && $newAttributeCode != 'none') {
                    $usedAttributes[] = $newAttributeCode;
                } else if ($newAttributeCode != 'none') {
                    $usedAttributes[] = $attributeCode;
                }
            }

            /** @var $childrenProducts Mage_Catalog_Model_Resource_Product_Collection */
            $childrenProducts = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToFilter('entity_id', array('in' => $childrenIds))
                ->addWebsiteFilter($this->getStore()->getWebsiteId())
                ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->addAttributeToSelect('nrapps_idealo_exclude')
                ->addAttributeToSelect($usedAttributes)
                ->setOrder('price');

            foreach ($childrenProducts as $childProduct) { /** @var Mage_Catalog_Model_Product $childProduct */
                if ($childProduct->getId() && !$childProduct->getFeedgeneratorExclude()) {
                    foreach ($usedAttributes as $attributeCode) {
                        if (!$product->getData($attributeCode) && $childProduct->getData($attributeCode)) {
                            $product->setData($attributeCode, $childProduct->getData($attributeCode));
                        }
                    }
                    return $childProduct->getPriceModel()->getFinalPrice(1, $childProduct);
                }
            }
        }

        return $product->getPrice();
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    protected function _getBasePrice(Mage_Catalog_Model_Product $product)
    {
        if (Mage::getConfig()->getModuleConfig('DerModPro_BasePrice')->is('active', 'true')) {

            if (! ($productAmount = $product->getBasePriceAmount())) return '';

            if (! ($referenceAmount = $product->getBasePriceBaseAmount())) return '';
            if (! ($productPrice = $product->getFinalPrice())) return '';
            if (! is_numeric($productAmount) || ! is_numeric($referenceAmount) || ! is_numeric($productPrice)) return '';

            $productUnit = $product->getBasePriceUnit();
            $referenceUnit = $product->getBasePriceBaseUnit();
            $referenceUnitLabel = Mage::helper('baseprice')->__($referenceUnit . ' short');

            $productPrice = Mage::helper('tax')->getPrice($product, $productPrice, Mage::getStoreConfig('catalog/baseprice/base_price_incl_tax'));
            $basePriceModel = Mage::getModel('baseprice/baseprice', array('reference_unit' => $referenceUnit, 'reference_amount' => $referenceAmount));
            $basePrice = $basePriceModel->getBasePrice($productAmount, $productUnit, $productPrice);

            $basePrice = Mage::helper('core')->currencyByStore($basePrice, $product->getStoreId(), true, false);

            return $basePrice . ' / ' . $referenceAmount . ' ' . $referenceUnitLabel;
        }

        return '';
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    protected function _getBasePriceRaw(Mage_Catalog_Model_Product $product)
    {
        if (Mage::getConfig()->getModuleConfig('DerModPro_BasePrice')->is('active', 'true')) {

            if (! ($productAmount = $product->getBasePriceAmount())) return '';

            if (! ($referenceAmount = $product->getBasePriceBaseAmount())) return '';
            if (! ($productPrice = $product->getFinalPrice())) return '';
            if (! is_numeric($productAmount) || ! is_numeric($referenceAmount) || ! is_numeric($productPrice)) return '';

            $productUnit = $product->getBasePriceUnit();
            $referenceUnit = $product->getBasePriceBaseUnit();

            $productPrice = Mage::helper('tax')->getPrice($product, $productPrice, Mage::getStoreConfig('catalog/baseprice/base_price_incl_tax'));
            $basePriceModel = Mage::getModel('baseprice/baseprice', array('reference_unit' => $referenceUnit, 'reference_amount' => $referenceAmount));
            $basePrice = $basePriceModel->getBasePrice($productAmount, $productUnit, $productPrice);

            return $basePrice;
        }

        return '';
    }

    /**
     * Set the configuration default values on the product model.
     * Used when products already existed when the extension was installed.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return DerModPro_BasePrice_Helper_Data
     */
    protected function _loadDefaultBasePriceValues($product)
    {
        foreach (array('base_price_base_amount', 'base_price_unit', 'base_price_base_unit') as $attributeCode) {
            if (! $product->getDataUsingMethod($attributeCode)) {
                /** @var $attribute Mage_Eav_Model_Entity_Attribute */
                $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeCode);
                $product->setDataUsingMethod($attributeCode, $attribute->getFrontend()->getValue($product));
            }
        }
        return $this;
    }

    /**
     * @param int $productId
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct($productId)
    {
        if (is_null($this->_product) || $this->_product->getId() != $productId) {
            $this->_product = Mage::getModel('catalog/product')->setStoreId()->load($productId);
        }
        return $this->_product;
    }

    /**
     * @return int
     */
    protected function _getStoreId()
    {
        return $this->getStore()->getId();
    }

    /**
     * @param float $productPrice
     * @param float $productWeight
     * @param string $listingCountry
     * @return float
     */
    protected function _getDefaultShippingCost($productPrice, $productWeight = 0.00, $listingCountry)
    {
        $defaultShippingCost = null;

        $group = $this->_getConfigGroup($listingCountry);
        
        switch (Mage::getStoreConfig('nrapps_idealo/' . $group . '/shipping_costs_calculation_method', $this->_getStoreId())) {

            case NRApps_Idealo_Model_Source_ShippingCostsMethod::SHIPPING_COSTS_BY_PRICE:
                $shippingCosts = unserialize(Mage::getStoreConfig('nrapps_idealo/' . $group . '/shipping_costs', $this->_getStoreId()));

                $selectedMinPrice = 0;
                foreach ($shippingCosts as $shippingCost) {
                    if ($shippingCost['from_price'] <= $productPrice) {
                        if (is_null($defaultShippingCost) || $shippingCost['from_price'] > $selectedMinPrice) {
                            $defaultShippingCost = $this->_formatPrice(floatval($shippingCost['cost']));
                            $selectedMinPrice = $shippingCost['from_price'];
                        }
                    }
                }
                break;

            case NRApps_Idealo_Model_Source_ShippingCostsMethod::SHIPPING_COSTS_BY_WEIGHT:
                $shippingCosts = unserialize(Mage::getStoreConfig('nrapps_idealo/' . $group . '/shipping_costs_by_weight', $this->_getStoreId()));

                foreach ($shippingCosts as $shippingCost) {
                    if ($shippingCost['from_weight'] <= floatval($productWeight)) {
                        if (is_null($defaultShippingCost) || $defaultShippingCost < floatval($shippingCost['cost'])) {
                            $defaultShippingCost = $this->_formatPrice(floatval($shippingCost['cost']));
                        }
                    }
                }
                break;
        }

        if (is_null($defaultShippingCost)) {
            $defaultShippingCost = 0.00;
        }

        return $defaultShippingCost;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    protected function _getProductUrl(Mage_Catalog_Model_Product $product)
    {
        if (!$product->isVisibleInSiteVisibility()) {
            if ($product->getParentProductIds()) {

                // get product url of parent product
                $productId = current($product->getParentProductIds());

                /** @var $rewrite Mage_Core_Model_Url_Rewrite */
                $rewrite = Mage::getModel('core/url_rewrite');
                $rewrite->setStoreId($this->_getStoreId())
                    ->loadByIdPath('product/' . $productId);
                if ($rewrite->getId()) {
                    $requestPath = $rewrite->getRequestPath();
                    $routeParams['_direct'] = $requestPath;
                    $routeParams['_store_to_url'] = true;
                    $routeParams['_store'] = $this->_getStoreId();

                    $parentProduct = $this->_getProduct($productId);
                    if ($parentProduct->isConfigurable()) {
                        /** @var Mage_Catalog_Model_Product_Type_Configurable $typeInstance */
                        $typeInstance = $parentProduct->getTypeInstance();
                        $urlParts = array();
                        foreach($typeInstance->getConfigurableAttributes($parentProduct) as $attribute) {
                            $urlParts[] = $attribute->getAttributeId() . '=' . $product->getData($attribute->getProductAttribute()->getAttributeCode());
                        }
                        if (sizeof($urlParts)) {
                            return Mage::getModel('core/url')->getUrl(null, $routeParams) . '#' . implode('&', $urlParts);
                        }
                    }

                    return Mage::getModel('core/url')->getUrl(null, $routeParams);
                }
            }
        }

        return $product->getProductUrl();
    }

    /**
     * Get Image filename from product or configurable parent product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $imageAttribute
     * @return string
     */
    protected function _getProductImage(Mage_Catalog_Model_Product $product, $imageAttribute = 'image')
    {
        $productImage = $product->getData($imageAttribute);
        if ($productImage && $productImage != 'no_selection') {
            return $productImage;
        }

        if ($product->getParentProductIds()) {
            $parentProductId = current($product->getParentProductIds());
            $parentProductImage = $this->_getProduct($parentProductId)->getData($imageAttribute);
            if ($parentProductImage && $parentProductImage != 'no_selection') {
                return $parentProductImage;
            }
        }

        return '';
    }

    /**
     * @param int $productId
     * @return string
     */
    protected function _getSkuByProductId($productId)
    {
        if (!isset($this->_productSkusById[$productId])) {
            $this->_productSkusById[$productId] = Mage::getResourceSingleton('catalog/product')->getAttributeRawValue($productId, 'sku', Mage_Core_Model_App::ADMIN_STORE_ID);
        }
        return $this->_productSkusById[$productId];
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param $attributeCode
     * @return string|null
     */
    protected function _getAttributeValue(Mage_Catalog_Model_Product $product, $attributeCode)
    {
        if (!$product->getData($attributeCode) || is_object($product->getData($attributeCode)) || is_array($product->getData($attributeCode))) {
            return null;
        }

        $value = $this->_getRawAttributeValue($product, $attributeCode);
        if ($value == 'no-display') {
            return null;
        }
        return $this->_getEncodedValue($value);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param $attributeCode
     *
     * @return string|null
     */
    protected function _getAttributeValueRaw(Mage_Catalog_Model_Product $product, $attributeCode)
    {
        if (!$product->getData($attributeCode) || is_object($product->getData($attributeCode)) || is_array($product->getData($attributeCode))) {
            return null;
        }

        $value = $this->_getRawAttributeValueRaw($product, $attributeCode);
        if ($value == 'no-display') {
            return null;
        }

        return $this->_getEncodedValue($value);
    }

    /**
     * @param string $value
     * @return string
     */
    protected function _getEncodedValue($value)
    {
        if ($value === 'no-display') {
            return '';
        }

        switch ($this->getFeed()->getType()) {

            case 'csv':
                if ($search = $this->getFeed()->getQuoteSymbol()) {
                    if ($search == '\t') {
                        $search = "\t";
                    }
                    $replacement = $this->getFeed()->getQuoteSymbolReplacement();
                    return str_replace($search, $replacement, $value);
                } else {
                    return $value;
                }

            case 'xml':

                return $value;
        }
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param $attributeCode
     * @return mixed
     */
    protected function _getRawAttributeValue(Mage_Catalog_Model_Product $product, $attributeCode)
    {
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $attribute = $this->_getAttribute($attributeCode);
        if (!$attribute instanceof Mage_Catalog_Model_Resource_Eav_Attribute) {
            return null;
        }

        switch($attribute->getFrontendInput()) {
            case 'multiselect':
                $value = '';
                $valueIds = explode(',', $product->getData($attributeCode));
                foreach($valueIds as $valueId) {
                    $value[] = $attribute->getSource()->getOptionText($valueId);
                }
                return implode(', ', $value);
            case 'select':
                if (strpos($attributeCode, 'google_') !== false) {
                    return $product->getData($attributeCode);
                }
                if (strpos($attributeCode, 'leguide_') !== false) {
                    return $product->getData($attributeCode);
                }
                return $product->getAttributeText($attributeCode);
            case 'price':
                return $this->_formatPrice($product->getData($attributeCode));
            case 'date':
                $date = new Zend_Date($product->getData($attributeCode), Zend_Date::ISO_8601);
                $date->setTimezone(Mage::getStoreConfig('general/locale/timezone'));
                return $date->get(Zend_Date::ISO_8601);
            case 'textarea':
            default:
                return $product->getData($attributeCode);
        }
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param $attributeCode
     * @return mixed
     */
    protected function _getRawAttributeValueRaw(Mage_Catalog_Model_Product $product, $attributeCode)
    {
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $attribute = $this->_getAttribute($attributeCode);
        if (!$attribute instanceof Mage_Catalog_Model_Resource_Eav_Attribute) {
            return null;
        }

        if ($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'multiselect') {
            return $product->getData($attributeCode);
        }

        return null;
    }

    /**
     * @param string $attributeCode
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected function _getAttribute($attributeCode)
    {
        if (!isset($this->_attributes[$attributeCode])) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
            $attribute = Mage::getSingleton('eav/config')
                ->getAttribute('catalog_product', $attributeCode)->setStoreId($this->_getStoreId());

            $this->_attributes[$attributeCode] = $attribute;
        }

        return $this->_attributes[$attributeCode];
    }

    /**
     * Return the category name(s) of the given category id(s) i.e. "Cell Phones"
     *
     * @param int|int[] $categoryIds
     * @return string[]
     */
    protected function _getCategoryNamesByIds($categoryIds)
    {
        if (is_array($categoryIds)) {
            $categoryNames = array();
            foreach ($categoryIds as $categoryId) {
                $categoryName = $this->_getCategoryNamesByIds($categoryId);
                if ($categoryName) {
                    $categoryNames[$categoryId] = $categoryName;
                }
            }
            return $categoryNames;
        }

        $categoryId = $categoryIds;
        if (!isset($this->_categoryNames[$categoryId]) && !$this->isExcludedCategoryId($categoryId)) {
            $this->_categoryNames[$categoryId] =
                $this->_getEncodedValue(Mage::getResourceSingleton('catalog/category')->getAttributeRawValue($categoryId, 'name', $this->getStore()->getId()));
        }
        return isset($this->_categoryNames[$categoryId]) ? $this->_categoryNames[$categoryId] : '';
    }

    /**
     * Get attributes which should be taken from categories if not present at the product itself
     * Configured in config.xml
     *
     * @return array
     */
    protected function _getCategoryAttributeCodes()
    {
        if (!is_array(Mage::getStoreConfig('nrapps_idealo/category_attributes'))) {
            return array();
        }
        return array_keys(Mage::getStoreConfig('nrapps_idealo/category_attributes'));
    }

    /**
     * Return the category path(s) of the given category id(s) i.e. "Electronics &gt; Computers &gt; Build Your Own"
     *
     * @param int|int[] $categoryIds
     * @return string|string[]
     */
    protected function _getCategoryPathsByIds($categoryIds)
    {
        if (is_array($categoryIds)) {
            $categoryPaths = array();
            $i = 0;
            foreach ($categoryIds as $categoryId) {
                // limit to 10 categories
                if ($i++ >= 10) {
                    break;
                }
                if ($categoryPath = $this->_getCategoryPathsByIds($categoryId)) {
                    $categoryPaths[$categoryId] = $categoryPath;
                }
            }
            return $categoryPaths;
        }

        /** @var int $categoryId */
        $categoryId = $categoryIds;
        if ($this->isExcludedCategoryId($categoryId)) {
            return '';
        }

        if (!isset($this->_categoryPaths[$categoryId]) && !$this->isExcludedCategoryId($categoryId)) {

            $parentCategoryId = Mage::getResourceSingleton('catalog/category')->getAttributeRawValue($categoryId, 'parent_id', $this->_getStoreId());
            $categoryLevel = Mage::getResourceSingleton('catalog/category')->getAttributeRawValue($categoryId, 'level', $this->_getStoreId());
            foreach ($this->_getCategoryAttributeCodes() as $attributeCode) {
                $this->_categoryAttributeValues[$attributeCode][$categoryId] = Mage::getResourceSingleton('catalog/category')->getAttributeRawValue($categoryId, $attributeCode, $this->_getStoreId());
                $this->_categoryAttributeLevels[$attributeCode][$categoryId] = $categoryLevel;
            }
            if ($parentCategoryId == $this->getStore()->getGroup()->getRootCategoryId() || $parentCategoryId == Mage_Catalog_Model_Category::TREE_ROOT_ID) {
                $this->_categoryPaths[$categoryId] = $this->_getCategoryNamesByIds($categoryId);
            } else {
                $parentCategoryPath = $this->_getCategoryPathsByIds($parentCategoryId);
                if (!$parentCategoryPath) {
                    return '';
                }

                $this->_categoryPaths[$categoryId] = $parentCategoryPath . $this->_getCategoryPathDivider() . $this->_getCategoryNamesByIds($categoryId);
                foreach ($this->_getCategoryAttributeCodes() as $attributeCode) {
                    if (!$this->_categoryAttributeValues[$attributeCode][$categoryId]) {
                        $this->_categoryAttributeValues[$attributeCode][$categoryId] = $this->_categoryAttributeValues[$attributeCode][$parentCategoryId];
                        $this->_categoryAttributeLevels[$attributeCode][$categoryId] = $this->_categoryAttributeLevels[$attributeCode][$parentCategoryId];
                    }
                }
            }
        }
        return $this->_categoryPaths[$categoryId];
    }

    /**
     * @return string
     */
    protected function _getCategoryPathDivider()
    {
        return ' > ';
    }

    /**
     * Check if attribute / any attribute is used
     *
     * @param string|string[] $attributeCodes
     * @return bool
     */
    protected function _isAttributeUsed($attributeCodes)
    {
        $additionalUsedAttributeCodes = $this->_getAdditionalUsedAttributeCodes();

        if (is_array($attributeCodes)) {
            foreach ($attributeCodes as $attributeCode) {
                if ($this->_isAttributeUsed($attributeCode)) {
                    return true;
                }
            }
            return false;
        }
        $attributeCode = $attributeCodes;
        if (!isset($this->_usedAttributeCodes[$attributeCode])) {
            if (in_array($attributeCode, $additionalUsedAttributeCodes)) {
                $this->_usedAttributeCodes[$attributeCode] = true;
            } else {
                $this->_usedAttributeCodes[$attributeCode] = (strpos($this->getFeed()->getBody(), $attributeCode) !== false);
            }
        }
        return $this->_usedAttributeCodes[$attributeCode];
    }

    /**
     * @param float $price
     * @return string
     */
    protected function _formatPrice($price)
    {
        return number_format($price, 2, '.', '');
    }

    /**
     * Get attribute codes which should always be included into the product data
     *
     * @return string[]
     */
    protected function _getAdditionalUsedAttributeCodes()
    {
        $attributeCodesObject = new Varien_Object();

        $attributeCodes = array(
            'shipping_cost',
            'weight',
            'price',
            'special_price',
        );

        foreach (unserialize(Mage::getStoreConfig('nrapps_idealo/product_options/add_attributes_to_export', $this->_getStoreId())) as $row) {
            $attributeCodes[] = $row['attribute_code'];
        }

        $attributeCodesObject->setAttributeCodes($attributeCodes);

        return $attributeCodesObject->getAttributeCodes();
    }

    /**
     * Sort categories so lowest level categories are first
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _getCategoryIds(Mage_Catalog_Model_Product $product)
    {
        $categoryIds = $product->getCategoryIds();

        /** @var $categoryCollection Mage_Catalog_Model_Resource_Category_Collection */
        $categoryCollection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToFilter('entity_id', array('in' => $categoryIds))
            ->addAttributeToFilter('level', array('gt' => 1))
            ->setOrder('level', 'DESC');
        return array_keys($categoryCollection->getItems());
    }

    /**
     * @param int $categoryId
     */
    protected function _excludeCategoryId($categoryId)
    {
        $this->_excludedCategoryIds[$categoryId] = $categoryId;
    }

    /**
     * @param int $categoryId
     * @return bool
     */
    public function isExcludedCategoryId($categoryId)
    {
        if (is_null($this->_excludedCategoryIds)) {

            $this->_excludedCategoryIds = array();

            /** @var $categories Mage_Catalog_Model_Resource_Category_Collection */
            $categories = Mage::getResourceModel('catalog/category_collection')
                ->setStoreId($this->_getStoreId())
                ->addAttributeToSelect(array('is_active', 'include_in_menu', 'nrapps_idealo_exclude'))
                ->addAttributeToFilter('level', array('gt' => 0))
                ->setOrder('level', 'asc');

            foreach ($categories as $category) { /** @var Mage_Catalog_Model_Category $category */
                if ($category->getLevel() == 1 && $category->getId() != $this->getStore()->getGroup()->getRootCategoryId()) {
                    // Exclude categories which are not in the current store root
                    $this->_excludeCategoryId($category->getId());
                }
                if (in_array($category->getParentId(), $this->_excludedCategoryIds)) {
                    // exclude categories whose parents are excluded
                    $this->_excludeCategoryId($category->getId());
                }
                if (!$category->getIsActive() || !$category->getIncludeInMenu() || $category->getFeedgeneratorExclude()) {
                    // exclude inactive, invisible or excluded categories
                    $this->_excludeCategoryId($category->getId());
                }
            }
        }
        return isset($this->_excludedCategoryIds[$categoryId]);
    }

    protected function _getOfferName($product)
    {
        $offerName = $product->getName();

        $attributes = array(
            0 => array(),
        );

        // get product url of parent product
        if ($productId = current($product->getParentProductIds())) {

            $parentProduct = $this->_getProduct($productId);
            if ($parentProduct->isConfigurable()) {
                /** @var Mage_Catalog_Model_Product_Type_Configurable $typeInstance */
                $typeInstance = $parentProduct->getTypeInstance();
                foreach ($typeInstance->getConfigurableAttributes($parentProduct) as $attribute) {
                    $attributes[0][] = $attribute->getProductAttribute()->getAttributeCode();
                }
            }
        }

        $configuredAttributes = Mage::getStoreConfig('nrapps_idealo/product_options/add_attribute_contents_to_name');
        if ($configuredAttributes) {
            if ($configuredAttributes = @unserialize($configuredAttributes)) {
                foreach($configuredAttributes as $configuredAttributeData) {
                    if (!in_array($configuredAttributeData['attribute_code'], $attributes[0])) {
                        $attributes[$configuredAttributeData['sort_order']][] = $configuredAttributeData['attribute_code'];
                    }
                }
            }
        }

        ksort($attributes);
        foreach($attributes as $attributesBySort) {
            foreach($attributesBySort as $attributeCode) {
                if ($value = $this->_getAttributeValue($product, $attributeCode)) {
                    $offerName .= ' ' . $value;
                }
            }
        }

        return $offerName;
    }

    /**
     * @param string $listingCountry
     * @return string
     */
    protected function _getConfigGroup($listingCountry)
    {
        switch ($listingCountry) {
            case NRApps_Idealo_Model_Source_ListingCountries::LISTING_COUNTRY_DE:
                $group = 'shipping';
                break;
            default:
                $group = 'shipping_' . strtolower($listingCountry);
                return $group;
        }
        return $group;
    }

    /**
     * @param $listingCountry
     * @return string
     */
    protected function _getContext($listingCountry)
    {
        return $listingCountry;
    }

    /**
     * @param $listingCountry
     * @return string
     */
    protected function _getSuffix($listingCountry)
    {
        return '_' . $listingCountry;
    }

    /**
     * @return string
     */
    protected function _getLineDivider()
    {
        if ($this->getFeed()->getType() == 'csv') {
            return '; ';
        }
        return PHP_EOL;
    }

    /**
     * @param string|array $value
     * @return string|array
     */
    protected function _getFilteredValue($value)
    {
        if (is_string($value)) {
            $value = trim(strip_tags($value));
            $value = str_replace(
                array("\r\n", "\n", "\r", '  '),
                ' ',
                $value
            );

            return $value;
        }
        if (is_array($value)) {
            foreach ($value as $key => $subValue) {
                $value[$key] = $this->_getFilteredValue($subValue);
            }
        }
        if ($value instanceof Varien_Object) {
            foreach ($value->getData() as $key => $subValue) {
                $value->setData($key, $this->_getFilteredValue($subValue));
            }
        }
        return $value;
    }
}