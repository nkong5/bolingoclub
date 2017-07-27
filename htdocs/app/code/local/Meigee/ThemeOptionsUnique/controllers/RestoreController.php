<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsUnique_RestoreController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
     $this->loadLayout(array('default'));

         $this->_addLeft($this->getLayout()
                ->createBlock('core/text')
                ->setText('
                    <h5>Predefined pages:</h5>
                    <ul>
                        <li>home</li>
                    </ul><br />
                    <h5>Predefined static blocks:</h5>
                    <ul>
                        <li>unique_header_phones</li>
						<li>unique_header1_boxed_slide_1</li>
						<li>unique_header1_boxed_slide_2</li>
						<li>unique_header1_boxed_slide_3</li>
						<li>unique_header1_boxed_slide_with_banners_1</li>
						<li>unique_header1_boxed_slide_with_banners_2</li>
						<li>unique_header1_boxed_slide_with_banners_3</li>
						<li>unique_header1_slider_banners</li>
						<li>unique_header2_boxed_slide_1</li>
						<li>unique_header2_boxed_slide_2</li>
						<li>unique_header2_boxed_slide_3</li>
						<li>unique_header2_boxed_slide_with_banners_1</li>
						<li>unique_header2_boxed_slide_with_banners_2</li>
						<li>unique_header2_boxed_slide_with_banners_3</li>
						<li>unique_header2_wide_slide_1</li>
						<li>unique_header2_wide_slide_2</li>
						<li>unique_header2_wide_slide_3</li>
						<li>unique_header2_slider_banners</li>
						<li>unique_sidebar_slide_1</li>
						<li>unique_sidebar_slide_2</li>
						<li>unique_sidebar_slide_3</li>
						<li>unique_sidebar_slide_4</li>
						<li>unique_social_likes</li>
						<li>unique_product_banner</li>
						<li>unique_footer</li>
						<li>unique_original_footer_logo</li>
						<li>unique_contact_map_block</li>
                    </ul><br />
                    <strong style="color:red;">To get more info regarding these blocks please read documentation that comes with this theme.</strong>'));
		$this->_addContent($this->getLayout()->createBlock('themeoptionsunique/adminhtml_restore_edit'));
		$this->renderLayout();


    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
        	
        $stores = $this->getRequest()->getParam('stores', array(0));
        $setup_package = $this->getRequest()->getParam('setup_package', 0);
        $this->_clear = $this->getRequest()->getParam('clear_scope', false);
        $setup_pages = $this->getRequest()->getParam('setup_pages', 0);
        $setup_blocks = $this->getRequest()->getParam('setup_blocks', 0);

        if ($this->_clear) {
            if ( !in_array(0, $this->_stores) )
                $stores[] = 0;
        }

        try {
            foreach ($stores as $store) {
                $scope = ($store ? 'stores' : 'default');
                Mage::getConfig()->saveConfig('design/header/logo_src', 'images/logo.gif', $scope, $store);
                Mage::getConfig()->saveConfig('design/footer/copyright', '&copy; 2012 Magento Demo Store. All Rights Reserved.', $scope, $store);

                if ($setup_package) {
                    Mage::getConfig()->saveConfig('design/package/name', 'default', $scope, $store);
                }
            }
            $defaults = new Varien_Simplexml_Config();
            $defaults->loadFile(Mage::getBaseDir().'/app/code/local/Meigee/ThemeOptionsUnique/etc/config.xml');

            $this->_restoreSettings($defaults->getNode('default/meigee_unique_general')->children(), 'meigee_unique_general');
            $this->_restoreSettings($defaults->getNode('default/meigee_unique_design')->children(), 'meigee_unique_design');
			$this->_restoreSettings($defaults->getNode('default/meigee_unique_design_skin1')->children(), 'meigee_unique_design_skin1');
			$this->_restoreSettings($defaults->getNode('default/meigee_unique_design_skin2')->children(), 'meigee_unique_design_skin2');
			$this->_restoreSettings($defaults->getNode('default/meigee_unique_design_skin3')->children(), 'meigee_unique_design_skin3');
			$this->_restoreSettings($defaults->getNode('default/meigee_unique_design_skin4')->children(), 'meigee_unique_design_skin4');
            $this->_restoreSettings($defaults->getNode('default/meigee_unique_productpage')->children(), 'meigee_unique_productpage');
            $this->_restoreSettings($defaults->getNode('default/meigee_unique_sidebar')->children(), 'meigee_unique_sidebar');
            $this->_restoreSettings($defaults->getNode('default/meigee_unique_headerslider')->children(), 'meigee_unique_headerslider');
            $this->_restoreSettings($defaults->getNode('default/meigee_unique_bgslider')->children(), 'meigee_unique_bgslider');

            if ($setup_pages) {
                Mage::getModel('ThemeOptionsUnique/restore')->setupPages();
            }

            if ($setup_blocks) {
                Mage::getModel('ThemeOptionsUnique/restore')->setupBlocks();
            }

            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('ThemeOptionsUnique')->__('Default settings has been restored. Please clear all the cache (System > Cache management)'));
        
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ThemeOptionsUnique')->__('An error occurred while restoring theme. '.$e->getMessage()));
        }

        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
        }
    }

    private function _restoreSettings($items, $path)
    {
        $websites = Mage::app()->getWebsites();
        $stores = Mage::app()->getStores();
        foreach ($items as $item) {
            if ($item->hasChildren()) {
                $this->_restoreSettings($item->children(), $path.'/'.$item->getName());
            } else {
                if ($this->_clear) {
                    Mage::getConfig()->deleteConfig($path.'/'.$item->getName());
                    foreach ($websites as $website) {
                        Mage::getConfig()->deleteConfig($path.'/'.$item->getName(), 'websites', $website->getId());
                    }
                    foreach ($stores as $store) {
                        Mage::getConfig()->deleteConfig($path.'/'.$item->getName(), 'stores', $store->getId());
                    }
                }
                foreach ($this->_stores as $store) {
                    $scope = ($store ? 'stores' : 'default');
                    Mage::getConfig()->saveConfig($path.'/'.$item->getName(), (string)$item, $scope, $store);
                }
            }
        }
    }
}