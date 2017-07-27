<?php
class Diecrema_Startpage_Resource_Mysql4_Startpage extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('diecrema_startpage/startpage', 'startpage_id');
    }

    public function getPoster($storeCode)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getTable('diecrema_startpage/startpage'))
            ->where('status = 1 AND store_code = ?', $storeCode);

        $count = count($read->fetchAll($select));
        if ($count > 1) {
           throw new Exception("There is more than one record pro store");
        }
        if ($count == 0) {
            return false;
        }

        $result = $read->fetchRow($select);

        return $result;
    }
}