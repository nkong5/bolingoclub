<?php
class Diecrema_Startpage_Resource_Mysql4_Startpage_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        //parent::__construct();
        $this->_init('diecrema_startpage/startpage');
    }
}