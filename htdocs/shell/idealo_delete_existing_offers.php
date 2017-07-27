<?php

require_once 'abstract.php';

class NRApps_Idealo_Shell_DeleteExistingOffers extends Mage_Shell_Abstract
{
    public function run()
    {
        Mage::getSingleton('nrapps_idealo/api')->deleteExistingOffersOnFirstRun();
    }
}

$shell = new NRApps_Idealo_Shell_DeleteExistingOffers();
$shell->run();
