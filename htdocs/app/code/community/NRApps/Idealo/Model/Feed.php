<?php
class NRApps_Idealo_Model_Feed extends Varien_Object
{


    public function getIncludeHeader()
    {
        return true;
    }
    
    public function getCharset()
    {
        return 'utf-8';
    }
    
    public function getHeader()
    {
        $date = Zend_Date::now();
        $date->setTimezone(Mage::getStoreConfig('general/locale/timezone'));
        $timestamp = $date->get('YYYY-MM-ddTHH:mm:ss');
        return '<?xml version="1.0" encoding="UTF-8"?>
<offers>
    <updateTimestamp>' . $timestamp . '</updateTimestamp>';

    }
    
    public function getBody()
    {
        return '
    <offer>
        <command>InsertOrReplace</command>
        <sku>{{var sku}}</sku>
        <title><![CDATA[{{var offer_name}}]]></title>
        <url><![CDATA[{{var url}}]]></url>
        {{foreach delivery_times as row}}<delivery{{if row.context}} context="{{var row.context}}"{{/if}}><![CDATA[{{var row.delivery_time}}]]></delivery>
        {{/foreach}}<price>{{if special_price}}{{var special_price}}{{else}}{{var price}}{{/if}}</price>
        {{if image}}<image>{{var image}}</image>
        {{/if}}{{foreach shipping_costs as row}}<shipping{{if row.context}} context="{{var row.context}}"{{/if}} type="{{var row.payment_method}}">{{var row.shipping_cost}}</shipping>
        {{/foreach}}{{foreach shipping_comments as row}}<shippingComment{{if row.context}} context="{{var row.context}}"{{/if}}><![CDATA[{{var row.shipping_comment}}]]></shippingComment>
        {{/foreach}}<category><![CDATA[{{var category_path}}]]></category>
        {{if manufacturer}}<brand><![CDATA[{{var manufacturer}}]]></brand>
        {{/if}}{{if oen}}<oen><![CDATA[{{var oen}}]]></oen>
        {{/if}}{{if kba}}<kba><![CDATA[{{var kba}}]]></kba>
        {{/if}}{{if ean}}<ean><![CDATA[{{var ean}}]]></ean>
        {{/if}}{{if han}}<han><![CDATA[{{var han}}]]></han>
        {{/if}}{{if pzn}}<pzn><![CDATA[{{var pzn}}]]></pzn>
        {{/if}}<description><![CDATA[{{var description:970}}]]></description>
        {{if is_used}}<used>true</used>
        {{/if}}{{if is_rebuild}}<rebuild>true</rebuild>
        {{/if}}{{if has_contract}}<contract>true</contract>
        {{/if}}{{if is_downloadable}}<downloadable>true</downloadable>
        {{/if}}{{if special_price}}<formerPrices>
            <formerPrice>{{var price}}</formerPrice>
        </formerPrices>
        {{/if}}{{if base_price}}<basePrice measure="{{var base_price_base_amount}}" unit="{{var base_price_base_unit}}">{{var base_price_raw}}</basePrice>
        {{/if}}{{if merchant}}<merchant><![CDATA[{{var merchant}}]]></merchant>
        {{/if}}<attributes>
        {{foreach additional_attributes as row}}{{if row.attribute_value}}    <attribute name="{{var row.attribute_code}}">
                <value><![CDATA[{{var row.attribute_value}}]]></value>
            </attribute>
        {{/if}}{{/foreach}}</attributes>
        {{if is_ecommerce_checkout_approved}}<eCommerce>
            <checkoutApproved>true</checkoutApproved>
        </eCommerce>{{/if}}
    </offer>';
    }
    
    public function getFooter()
    {
        return '</offers>';
    }
    
    public function getEol()
    {
        return PHP_EOL;
    }
    
    public function getType()
    {
        return 'xml';
    }
    
    public function getFilename()
    {
        return '';
    }
}