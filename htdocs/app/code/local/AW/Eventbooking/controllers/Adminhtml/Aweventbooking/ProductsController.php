<?php

require_once 'AbstractController.php';

class AW_Eventbooking_Adminhtml_Aweventbooking_ProductsController extends AW_Eventbooking_Adminhtml_Aweventbooking_AbstractController
{
    public function listAction()
    {
        $this->_initAction(array('Event Tickets', 'Manage Products'))
            ->renderLayout();
    }

    public function ajaxgridAction()
    {
        $this->_initAction()->renderLayout();
    }

    protected function _invalidateFPC($productIds)
    {
        /** @var AW_Eventbooking_Helper_Data $helper */
        $helper = Mage::helper('aw_eventbooking');
        $fullPageCache = $helper->getFPCInstance();
        if (!$fullPageCache) {
            return null;
        };
        $cacheIdTags = array();
        foreach ($productIds as $productId) {
            /** @var Mage_Catalog_Model_Product $product */
            $product = Mage::getModel('catalog/product')->load($productId);
            $productCacheIdTags = $product->getCacheIdTags();
            if ($productCacheIdTags) {
                $cacheIdTags = array_merge($cacheIdTags, $productCacheIdTags);
            }
        }

        if ($cacheIdTags) {
            $fullPageCache->clean($cacheIdTags);
        }
        return $this;
    }

    public function massstatusAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        $newStatus = $this->getRequest()->getParam('status')
            ? AW_Eventbooking_Model_Source_Status::ENABLED
            : AW_Eventbooking_Model_Source_Status::DISABLED;

        if (!$productIds) {
            return $this->_redirectReferer();
        }

        $this->_invalidateFPC($productIds);

        /** @var Magento_Db_Adapter_Pdo_Mysql $dbConnection */
        $dbConnection = Mage::getSingleton('core/resource')->getConnection('aw_eventbooking_write');
        $rowCount = $dbConnection->update(
            Mage::getSingleton('core/resource')->getTableName('aw_eventbooking/event'),
            array('is_enabled' => $newStatus),
            new Zend_Db_Expr('product_id IN (' . implode(',', $productIds) . ')')
        );
        switch ($rowCount) {
            case 0:
                $this->_getSession()->addWarning($this->__('Events were not updated'));
                break;
            case 1:
                $this->_getSession()->addSuccess($this->__('Event has been successfully updated'));
                break;
            default:
                $this->_getSession()->addSuccess($this->__('%d events has been successfully updated', $rowCount));
                break;
        }
        return $this->_redirectReferer();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/aw_eventbooking/products');
    }
}
