<?php
/**
 * Call actions after configuration is saved
 */
class Meigee_ThemeOptionsUnique_Model_Observer
{
	/**
     * After any system config is saved
     */
	public function cssgenerate()
	{
		$section = Mage::app()->getRequest()->getParam('section');

		if ($section == 'meigee_unique_design' || $section == 'meigee_unique_design_skin1' || $section == 'meigee_unique_design_skin2' || $section == 'meigee_unique_design_skin3' || $section == 'meigee_unique_design_skin4')
		{
			Mage::getSingleton('ThemeOptionsUnique/Cssgenerate')->saveCss();
			$response = Mage::app()->getFrontController()->getResponse();
			$response->sendResponse();
		}
		
	}
}
