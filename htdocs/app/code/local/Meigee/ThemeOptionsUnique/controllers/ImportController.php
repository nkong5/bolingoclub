<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsUnique_ImportController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
     $this->loadLayout(array('default'));

         
		$this->_addContent($this->getLayout()->createBlock('themeoptionsunique/adminhtml_import_edit'));
        $block = $this->getLayout()->createBlock('core/text')->setText('Be aware in case if ID\'s
 of the static blocks will match existing ones the content of existing static blocks will be overridden after using import feature. 
After static content importing you have to configure your store with new content. More detailed about configuration of predefined subthemes please read in the section "Predefined subthemes" of theme documentation.');
        $this->getLayout()->getBlock('content')->append($block);
		$this->renderLayout();
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
        	
        $stores = $this->getRequest()->getParam('stores', array(0));
        $static_block = $this->getRequest()->getParam('static_block', 0);

        try {

                Mage::getModel('ThemeOptionsUnique/import')->getCorrectSet($static_block);

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
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ThemeOptionsUnique')->__('An error occurred while importing blocks. '.$e->getMessage()));
        }
        $this->getResponse()->setRedirect($this->getUrl("*/*/"));

        }
    }
}