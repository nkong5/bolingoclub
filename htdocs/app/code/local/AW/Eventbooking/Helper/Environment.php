<?php

class AW_Eventbooking_Helper_Environment
{
    /**
     * @param $storeId int
     *
     * @return Varien_Object
     */
    public function startEmulation($storeId)
    {
        if (version_compare(Mage::getVersion(), '1.5', '>=')) {
            $storeEmulator = Mage::getSingleton('core/app_emulation');
            $initialInfo = $storeEmulator->startEnvironmentEmulation($storeId);
        } else {
            $initialDesign = Mage::getDesign()->setAllGetOld(array(
                'package' => Mage::getStoreConfig('design/package/name', $storeId),
                'store'   => $storeId,
                'area'    => 'frontend'
            ));
            Mage::getDesign()->setTheme('');
            Mage::getDesign()->setPackageName('');
            $initialInfo = new Varien_Object();
            $initialInfo->setInitialStoreId(Mage::app()->getStore()->getId())
                ->setInitialLocaleCode(Mage::app()->getLocale()->getLocaleCode())
                ->setInitialDesign($initialDesign)
            ;
            Mage::app()->setCurrentStore($storeId)
                ->getLocale()
                ->setLocaleCode(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeId))
                ->emulate($storeId)
            ;
        }
        return $initialInfo;
    }

    /**
     * @param $initialInfo Varien_Object
     *
     * @return $this
     */
    public function stopEmulation($initialInfo)
    {
        if (version_compare(Mage::getVersion(), '1.5', '>=')) {
            Mage::getSingleton('core/app_emulation')->stopEnvironmentEmulation($initialInfo);
        } else {
            Mage::app()->setCurrentStore($initialInfo->getInitialStoreId());
            Mage::app()->getLocale()->setLocaleCode($initialInfo->getInitialLocaleCode());
            Mage::app()->getTranslator()->setLocale($initialInfo->getInitialLocaleCode())->init('adminhtml', true);
            Mage::getDesign()->setAllGetOld($initialInfo->getInitialDesign());
            Mage::getDesign()->setTheme('');
            Mage::getDesign()->setPackageName('');
        }
        return $this;
    }
}
