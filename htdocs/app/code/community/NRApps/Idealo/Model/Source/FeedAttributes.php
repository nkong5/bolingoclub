<?php

class NRApps_Idealo_Model_Source_FeedAttributes
{
    protected $_options;

    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                'manufacturer' => new Varien_Object(array(
                    'code' => 'manufacturer',
                    'label' => Mage::helper('nrapps_idealo')->__('Manufacturer'),
                    'type' => array('select', 'text'),
                )),
                'delivery_time' => new Varien_Object(array(
                    'code' => 'delivery_time',
                    'label' => Mage::helper('nrapps_idealo')->__('Delivery Time'),
                    'type' => array('select', 'text'),
                )),
                'shipping_cost' => new Varien_Object(array(
                    'code' => 'shipping_cost',
                    'label' => Mage::helper('nrapps_idealo')->__('Shipping Cost'),
                    'type' => 'price',
                    'note' => Mage::helper('nrapps_idealo')->__('Leave empty to use default'),
                )),
                'shipping_comment' => new Varien_Object(array(
                    'code' => 'shipping_comment',
                    'label' => Mage::helper('nrapps_idealo')->__('Shipping Comment'),
                    'type' => 'text',
                    'note' => Mage::helper('nrapps_idealo')->__('Comment on shipping of article or methods of payment offered'),
                )),
                'oen' => new Varien_Object(array(
                    'code' => 'oen',
                    'label' => Mage::helper('nrapps_idealo')->__('OEN'),
                    'type' => 'text',
                    'note' => Mage::helper('nrapps_idealo')->__('Original replacement part number consisting of manufacturer\'s initials and identifier code, separated by a comma'),
                )),
                'kba' => new Varien_Object(array(
                    'code' => 'kba',
                    'label' => Mage::helper('nrapps_idealo')->__('KBA'),
                    'type' => 'text',
                    'note' => Mage::helper('nrapps_idealo')->__('Kraftfahrt-Bundesamt (KBA) number, i.e. German Federal Motor Transport Authority number, consisting of the 4 digits in the Manufacturer Key Number and the first 3 characters in the Type Key Number'),
                )),
                'ean' => new Varien_Object(array(
                    'code' => 'ean',
                    'label' => Mage::helper('nrapps_idealo')->__('EAN'),
                    'type' => 'text',
                    'note' => Mage::helper('nrapps_idealo')->__('EAN/GTIN of the offer, improves allocation to product catalogue. ISBN numbers can also be given here'),
                )),
                'han' => new Varien_Object(array(
                    'code' => 'han',
                    'label' => Mage::helper('nrapps_idealo')->__('HAN'),
                    'type' => 'text',
                    'note' => Mage::helper('nrapps_idealo')->__('Article number defined by manufacturer, improves allocation to product catalogue'),
                )),
                'pzn' => new Varien_Object(array(
                    'code' => 'pzn',
                    'label' => Mage::helper('nrapps_idealo')->__('PZN'),
                    'type' => 'text',
                    'note' => Mage::helper('nrapps_idealo')->__('Central Pharmaceutical Number of offer (only for medical offers)'),
                )),
                'is_used' => new Varien_Object(array(
                    'code' => 'is_used',
                    'label' => Mage::helper('nrapps_idealo')->__('Is Used'),
                    'type' => 'yesno',
                    'note' => Mage::helper('nrapps_idealo')->__('Marks offers that are not new (e.g. returns, demonstration model, opened packaging, etc.)'),
                )),
                'is_rebuild' => new Varien_Object(array(
                    'code' => 'is_rebuild',
                    'label' => Mage::helper('nrapps_idealo')->__('Is Rebuild'),
                    'type' => 'yesno',
                    'note' => Mage::helper('nrapps_idealo')->__('Marks offers that are a replica of products by another manufacturer. This mark-up is necessary if the original product comes up in the offer title (e.g. "Quality battery compatible with Canon HK-2222")'),
                )),
                'has_contract' => new Varien_Object(array(
                    'code' => 'has_contract',
                    'label' => Mage::helper('nrapps_idealo')->__('Has Contract'),
                    'type' => 'yesno',
                    'note' => Mage::helper('nrapps_idealo')->__('Marks offers that can only be acquired through signing a contract (e.g. mobile phones)'),
                )),
                'is_ecommerce_checkout_approved' => new Varien_Object(array(
                    'code' => 'is_ecommerce_checkout_approved',
                    'label' => Mage::helper('nrapps_idealo')->__('idealo direct sale allowed'),
                    'type' => 'yesno',
                )),
                'merchant' => new Varien_Object(array(
                    'code' => 'merchant',
                    'label' => Mage::helper('nrapps_idealo')->__('Merchant'),
                    'type' => array('select', 'text'),
                    'note' => Mage::helper('nrapps_idealo')->__('Merchant name (for resellers or marketplace merchants)'),
                )),
            );
        }
        return $this->_options;
    }

    public function getOption($value)
    {
        $options = $this->getAllOptions();
        if (isset($options[$value])) {
            return $options[$value];
        }
        return new Varien_Object();
    }
}
