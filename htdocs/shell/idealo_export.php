<?php

require_once 'abstract.php';

class NRApps_Idealo_Shell_Export extends Mage_Shell_Abstract
{
    public function run()
    {
        Mage::getSingleton('nrapps_idealo/api')->transferAll();
    }
}

$shell = new NRApps_Idealo_Shell_Export();
$shell->run();
