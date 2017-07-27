<?php
class NRApps_Idealo_Model_Csv_Feed extends NRApps_Idealo_Model_Feed
{
    public function getIncludeHeader()
    {
        return true;
    }
    
    public function getCharset()
    {
        return 'utf-8';
    }
    
    public function getHeader($testMode = false)
    {
        $headerLine = 'Artikelnummer im Shop$EAN / Barcodenummer$Original Herstellerartikelnummer$Herstellername$Produktname$Produktgruppe im Shop (möglichst als Pfad ausgehend von der Wurzelkategorie)$Preis (Brutto)$Lieferzeit DE$Lieferzeit AT$Lieferzeit UK$Lieferzeit FR$Lieferzeit IT$Lieferzeit ES$Lieferzeit PL$Lieferzeit IN$ProduktURL$BildURL_1 (großes Bild)$BildURL_2 (großes Bild)$BildURL_3 (großes Bild)$BildURL_4$BildURL_5$Vorkasse DE$Nachnahme DE$Kreditkarte DE$Paypal DE$Sofortüberweisung DE$Bankeinzug DE$Rechnung DE$Moneybookers DE$Click & Buy DE$Giropay DE$Versandkosten Kommentar DE (max. 150 Zeichen)$Vorkasse AT$Nachnahme AT$Kreditkarte AT$Paypal AT$Sofortüberweisung AT$Bankeinzug AT$Rechnung AT$Moneybookers AT$Click & Buy AT$Giropay AT$Versandkosten Kommentar AT (max. 150 Zeichen)$Vorkasse UK$Nachnahme UK$Kreditkarte UK$Paypal UK$Sofortüberweisung UK$Bankeinzug UK$Rechnung UK$Moneybookers UK$Click & Buy UK$Giropay UK$Versandkosten Kommentar UK (max. 150 Zeichen)$Vorkasse FR$Nachnahme FR$Kreditkarte FR$Paypal FR$Sofortüberweisung FR$Bankeinzug FR$Rechnung FR$Moneybookers FR$Click & Buy FR$Giropay FR$Versandkosten Kommentar FR (max. 150 Zeichen)$Vorkasse IT$Nachnahme IT$Kreditkarte IT$Paypal IT$Sofortüberweisung IT$Bankeinzug IT$Rechnung IT$Moneybookers IT$Click & Buy IT$Giropay IT$Versandkosten Kommentar IT (max. 150 Zeichen)$Vorkasse ES$Nachnahme ES$Kreditkarte ES$Paypal ES$Sofortüberweisung ES$Bankeinzug ES$Rechnung ES$Moneybookers ES$Click & Buy ES$Giropay ES$Versandkosten Kommentar ES (max. 150 Zeichen)$Vorkasse PL$Nachnahme PL$Kreditkarte PL$Paypal PL$Sofortüberweisung PL$Bankeinzug PL$Rechnung PL$Moneybookers PL$Click & Buy PL$Giropay PL$Versandkosten Kommentar PL (max. 150 Zeichen)$Vorkasse IN$Nachnahme IN$Kreditkarte IN$Paypal IN$Sofortüberweisung IN$Bankeinzug IN$Rechnung IN$Moneybookers IN$Click & Buy IN$Giropay IN$Versandkosten Kommentar IN (max. 150 Zeichen)$Produktbeschreibung (max. 1000 Zeichen)$Grundpreis (Produktabhängig)$Direktkauf';
        if (is_array($this->getAdditionalAttributeCodes())) {
            foreach($this->getAdditionalAttributeCodes() as $attributeCode) {
                $headerLine .= '$' . Mage::getResourceSingleton('catalog/product')->getAttribute($attributeCode)->getFrontendLabel();
            }
        }
        return $headerLine;
    }
    
    public function getBody()
    {
        $bodyLine = '{{var sku}}${{var ean}}${{var manufacturer_sku}}${{var manufacturer}}${{var offer_name}}${{var category_path}}${{if special_price}}{{var special_price}}{{else}}{{var price}}{{/if}}${{var delivery_time_DE}}${{var delivery_time_AT}}${{var delivery_time_UK}}${{var delivery_time_FR}}${{var delivery_time_IT}}${{var delivery_time_ES}}${{var delivery_time_PL}}${{var delivery_time_IN}}${{var url}}${{var image}}${{var additional_image_1}}${{var additional_image_2}}${{var additional_image_3}}${{var additional_image_4}}${{var shipping_cost_PREPAID_DE}}${{var shipping_cost_COD_DE}}${{var shipping_cost_CREDITCARD_DE}}${{var shipping_cost_PAYPAL_DE}}${{var shipping_cost_SOFORTUEBERWEISUNG_DE}}${{var shipping_cost_DIRECTDEBIT_DE}}${{var shipping_cost_INVOICE_DE}}${{var shipping_cost_MONEYBOOKERS_DE}}${{var shipping_cost_CLICKANDBUY_DE}}${{var shipping_cost_GIROPAY_DE}}${{var shipping_comment_DE}}${{var shipping_cost_PREPAID_AT}}${{var shipping_cost_COD_AT}}${{var shipping_cost_CREDITCARD_AT}}${{var shipping_cost_PAYPAL_AT}}${{var shipping_cost_SOFORTUEBERWEISUNG_AT}}${{var shipping_cost_DIRECTDEBIT_AT}}${{var shipping_cost_INVOICE_AT}}${{var shipping_cost_MONEYBOOKERS_AT}}${{var shipping_cost_CLICKANDBUY_AT}}${{var shipping_cost_GIROPAY_AT}}${{var shipping_comment_AT}}${{var shipping_cost_PREPAID_UK}}${{var shipping_cost_COD_UK}}${{var shipping_cost_CREDITCARD_UK}}${{var shipping_cost_PAYPAL_UK}}${{var shipping_cost_SOFORTUEBERWEISUNG_UK}}${{var shipping_cost_DIRECTDEBIT_UK}}${{var shipping_cost_INVOICE_UK}}${{var shipping_cost_MONEYBOOKERS_UK}}${{var shipping_cost_CLICKANDBUY_UK}}${{var shipping_cost_GIROPAY_UK}}${{var shipping_comment_UK}}${{var shipping_cost_PREPAID_FR}}${{var shipping_cost_COD_FR}}${{var shipping_cost_CREDITCARD_FR}}${{var shipping_cost_PAYPAL_FR}}${{var shipping_cost_SOFORTUEBERWEISUNG_FR}}${{var shipping_cost_DIRECTDEBIT_FR}}${{var shipping_cost_INVOICE_FR}}${{var shipping_cost_MONEYBOOKERS_FR}}${{var shipping_cost_CLICKANDBUY_FR}}${{var shipping_cost_GIROPAY_FR}}${{var shipping_comment_FR}}${{var shipping_cost_PREPAID_IT}}${{var shipping_cost_COD_IT}}${{var shipping_cost_CREDITCARD_IT}}${{var shipping_cost_PAYPAL_IT}}${{var shipping_cost_SOFORTUEBERWEISUNG_IT}}${{var shipping_cost_DIRECTDEBIT_IT}}${{var shipping_cost_INVOICE_IT}}${{var shipping_cost_MONEYBOOKERS_IT}}${{var shipping_cost_CLICKANDBUY_IT}}${{var shipping_cost_GIROPAY_IT}}${{var shipping_comment_IT}}${{var shipping_cost_PREPAID_ES}}${{var shipping_cost_COD_ES}}${{var shipping_cost_CREDITCARD_ES}}${{var shipping_cost_PAYPAL_ES}}${{var shipping_cost_SOFORTUEBERWEISUNG_ES}}${{var shipping_cost_DIRECTDEBIT_ES}}${{var shipping_cost_INVOICE_ES}}${{var shipping_cost_MONEYBOOKERS_ES}}${{var shipping_cost_CLICKANDBUY_ES}}${{var shipping_cost_GIROPAY_ES}}${{var shipping_comment_ES}}${{var shipping_cost_PREPAID_PL}}${{var shipping_cost_COD_PL}}${{var shipping_cost_CREDITCARD_PL}}${{var shipping_cost_PAYPAL_PL}}${{var shipping_cost_SOFORTUEBERWEISUNG_PL}}${{var shipping_cost_DIRECTDEBIT_PL}}${{var shipping_cost_INVOICE_PL}}${{var shipping_cost_MONEYBOOKERS_PL}}${{var shipping_cost_CLICKANDBUY_PL}}${{var shipping_cost_GIROPAY_PL}}${{var shipping_comment_PL}}${{var shipping_cost_PREPAID_IN}}${{var shipping_cost_COD_IN}}${{var shipping_cost_CREDITCARD_IN}}${{var shipping_cost_PAYPAL_IN}}${{var shipping_cost_SOFORTUEBERWEISUNG_IN}}${{var shipping_cost_DIRECTDEBIT_IN}}${{var shipping_cost_INVOICE_IN}}${{var shipping_cost_MONEYBOOKERS_IN}}${{var shipping_cost_CLICKANDBUY_IN}}${{var shipping_cost_GIROPAY_IN}}${{var shipping_comment_IN}}${{var description:970}}${{if base_price}}{{var base_price}}{{/if}}${{if is_ecommerce_checkout_approved}}true{{/if}}';
        if (is_array($this->getAdditionalAttributeCodes())) {
            foreach($this->getAdditionalAttributeCodes() as $attributeCode) {
                $bodyLine .= '${{var ' . $attributeCode . '}}';
            }
        }
        return $bodyLine;
    }
    
    public function getFooter()
    {
        return '';
    }
    
    public function getEol()
    {
        return PHP_EOL;
    }
    
    public function getType()
    {
        return 'csv';
    }
    
    public function getFilename()
    {
        return '';
    }

    public function getQuoteSymbol()
    {
        return '$';
    }

    public function getQuoteSymbolReplacement()
    {
        return '&#36;';
    }
}