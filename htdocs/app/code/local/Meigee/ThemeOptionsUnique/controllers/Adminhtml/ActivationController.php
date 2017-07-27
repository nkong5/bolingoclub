<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsUnique_Adminhtml_ActivationController extends Mage_Adminhtml_Controller_Action
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
		$this->_addContent($this->getLayout()->createBlock('themeoptionsunique/adminhtml_activation_edit'));
        $block = $this->getLayout()->createBlock('core/text')->setText('<strong style="color:red;">Activation feature is provided only for testing. Please do not use it on real stores! Make backup of your database every time when you use activation. </strong><br /><strong>Note:</strong> Please make sure you have at least 8 products marked as new to display homepage widgets correctly.');
        $this->getLayout()->getBlock('content')->append($block);
		$this->renderLayout();
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
        	
        $stores = $this->getRequest()->getParam('stores', array(0));
        $setup_pages = $this->getRequest()->getParam('setup_pages', 0);
        $setup_blocks = $this->getRequest()->getParam('setup_blocks', 0);

        try {

            foreach ($stores as $store) {
                $scope = ($store ? 'stores' : 'default');

                Mage::getConfig()->saveConfig('design/package/name', 'unique', $scope, $store);
                Mage::getConfig()->saveConfig('design/header/logo_src', 'images/logo.png', $scope, $store);
                Mage::getConfig()->saveConfig('design/footer/copyright', 'Meigee &copy; 2013 <a href="http://meigeeteam.com" >Premium Magento Themes</a>', $scope, $store);
				/*Mage::getConfig()->saveConfig('meigee_unique_headerslider/coin/slides', 'meigeetheme', $scope, $store);*/
            }

            if ($setup_pages) {
                Mage::getModel('ThemeOptionsUnique/activation')->setupPages();
            }

            if ($setup_blocks) {
                Mage::getModel('ThemeOptionsUnique/activation')->setupBlocks();
            }

            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('ThemeOptionsUnique')->__('<div class="meigee-please"><a target="_blank" href="http://themeforest.net/downloads"><img src="' . Mage::getBaseUrl('skin') . '/adminhtml/default/unique/images/rating.png" /></a><h2>Like us</h2>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, "script", "facebook-jssdk"));</script>
<div class="fb-like" data-href="http://facebook.com/meigeeteam" data-layout="button_count" data-action="like" data-show-faces="false" data-width="200" data-share="true"></div>&nbsp;
<a href="https://twitter.com/meigeeteam" class="twitter-follow-button" data-show-count="false" data-lang="en">Follow @meigeeteam</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
</div>'));

			Mage::app()->cleanCache();
			$model = Mage::getModel('core/cache');
			$options = $model->canUse();
			foreach($options as $option=>$value) {
				$options[$option] = 0;
			}
			$model->saveOptions($options);

			$adminSession = Mage::getSingleton('admin/session');
			$adminSession->unsetAll();
			$adminSession->getCookie()->delete($adminSession->getSessionName());
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ThemeOptionsUnique')->__('An error occurred while activating theme. '.$e->getMessage()));
        }

        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
        }
    }
}