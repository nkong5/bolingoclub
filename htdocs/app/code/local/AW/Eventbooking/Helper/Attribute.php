<?php
class AW_Eventbooking_Helper_Attribute extends Mage_Core_Helper_Data
{
    const CONFIGURATION_FOLDER = 'etc';
    const CONFIGURATION_FILE   = 'attribute.xml';

    const ATTRIBUTE_GROUP_EVENT  = 'event';
    const ATTRIBUTE_GROUP_TICKET = 'ticket';

    protected $_attributes = array();

    public function __construct()
    {
        $this->_initXmlConfig();
    }

    protected function _initXmlConfig()
    {
        $configFilePath =  Mage::getModuleDir(self::CONFIGURATION_FOLDER, $this->_getModuleName()) . '/' . self::CONFIGURATION_FILE;
        $config = new Varien_Simplexml_Config($configFilePath);

        foreach ($config->getNode('group')->children() as $groupName => $groupNode) {
            foreach ($groupNode->children() as $attribute) {
                $this->_attributes[$groupName][] = (string) $attribute->children();
            }
        }
    }

    public function getAttributesByGroup($groupName)
    {
        if (isset($this->_attributes[$groupName])) {
            return $this->_attributes[$groupName];
        }
        return array();
    }

    public function getEventAttributes()
    {
        return $this->getAttributesByGroup(self::ATTRIBUTE_GROUP_EVENT);
    }

    public function getTicketAttributes()
    {
        return $this->getAttributesByGroup(self::ATTRIBUTE_GROUP_TICKET);
    }
}