<?php
/**
 * Magento / Mediotype Module
 * 
 *
 * @desc        
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Model_Passbook
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Model_Passbook extends Mage_Core_Model_Abstract{

    /**
     * optional
     * @var string
     */
    protected $certificatePassword;
    /**
     * path to apple WWWDRCA.pem
     * @var string
     */
    protected $appleWWDRCA;

    /**
     * @var
     */
    protected $purchasedData;
    /**
     * @var
     */
    protected $encodedMsg; // Value of encoding in barcode or qr or supported format

    /**
     * @var array
     */
    protected $passData = array();

    /**
     * string
     * @var
     */
    protected $imgBaseDir;
    /**
     * @var Mediotype_Ticket_Model_Passbook_Passbook
     */
    protected $pass;

    /**
     *
     */
    public function _construct(){

        $this->imgBaseDir = Mage::getBaseDir('media') . '/passbook/';
        $this->pass = Mage::getModel('mediotype_ticket/passbook_passbook');

        $this->pass->setPasstypeCert( Mage::getModuleDir('data', "Mediotype_Ticket") . DS . Mage::getStoreConfig("mediotype_ticket/apple_passbook_settings/passbook_certificate") );
        $this->pass->setCertificatePassword( Mage::helper('core')->decrypt(Mage::getStoreConfig("mediotype_ticket/apple_passbook_settings/passbook_certificate_password")) );
        $this->appleWWDRCA = Mage::getModuleDir('etc', "Mediotype_Ticket") . DS . 'cert/AppleWWDRCA.pem';
        $this->pass->setAppleBaseCertPath($this->appleWWDRCA);
    }

    /**
     * @param $object
     */
    public function setPurchasedData($object){
        $this->purchasedData = $object;
    }

    /**
     * @return stdClass
     * @throws Mediotype_Core_Exception
     */
    protected function _eventParameters(){

        /** @var $ticketUniqueDetail Mediotype_Ticket_Model_Order  */
        $ticketUniqueDetail = Mage::getModel('mediotype_ticket/order');
        $ticketUniqueDetail->load($this->purchasedData->getId());
        /** @var $eventProduct Mage_Catalog_Model_Product */
        $eventProduct = Mage::helper('mediotype_ticket')->loadTicketBySku($ticketUniqueDetail->getSku());
        /** @var $typeModel Mediotype_Ticket_Model_Product_Type_Simpleticket */
        $typeModel = $eventProduct->getTypeInstance();

        if(!$eventProduct->getTypeId() == Mediotype_Ticket_Model_Product_Type::TYPE_SIMPLE_TICKET){
            throw new Mediotype_Core_Exception('Event Passbook was called, but associated purchase is not of type Mediotype_Ticket_Model_Product_Type_Simpleticket');
        }

        $this->pass->addImage( $this->imgBaseDir . $eventProduct->getId() . $eventProduct->getPassbookIcon() , 'icon.png');
        $this->pass->addImage( $this->imgBaseDir . $eventProduct->getId() . $eventProduct->getPassbookIcon() , 'icon.png');
        $this->pass->addImage( $this->imgBaseDir . $eventProduct->getId() . $eventProduct->getData('passbook_icon_2_x') , 'icon@2x.png');
        $this->pass->addImage( $this->imgBaseDir . $eventProduct->getId() . $eventProduct->getData('passbook_thumbnail') , 'thumbnail.png');
        $this->pass->addImage( $this->imgBaseDir . $eventProduct->getId() . $eventProduct->getData('passbook_thumbnail_2_x') , 'thumbnail@2x.png');
        $this->pass->addImage( $this->imgBaseDir . $eventProduct->getId() . $eventProduct->getPassbookLogo() , 'logo.png');
        $this->pass->addImage( $this->imgBaseDir . $eventProduct->getId() . $eventProduct->getData('passbook_logo_2_x') , 'logo@2x.png');
        $this->pass->addImage( $this->imgBaseDir . $eventProduct->getId() . $eventProduct->getPassbookBackground() , 'background.png');
        $this->pass->addImage( $this->imgBaseDir . $eventProduct->getId() . $eventProduct->getData('passbook_background_2_x') , 'background@2x.png');

        $this->encodedMsg = $ticketUniqueDetail->getRedeemUrl();

        $eventKeys = new stdClass();

        $eventKeys->headerFields = array();

            $headDate = new stdClass();
            $headDate->key = "date";
            $headDate->label = "DATE";
            $headDate->value = $typeModel->getFormatDateTime('F jS');

        $eventKeys->headerFields[] = $headDate;

        $eventKeys->primaryFields = array();

            $event = new stdClass();
            $event->key = "event";
            $event->label = "EVENT";
            $event->value = $eventProduct->getEventTitle();

            $eventSub = new stdClass();
            $eventSub->key = "event";
            $eventSub->label = "EVENT";
            $eventSub->value = $eventProduct->getEventTitle();



        $eventKeys->primaryFields[] = $event;

        $eventKeys->secondaryFields = array();

            $location = new stdClass();
            $location->key = "loc";
            $location->label = "Location";
            $location->value = $eventProduct->getVenueName();

        $eventKeys->secondaryFields[] = $location;

        $eventKeys->auxiliaryFields = array();
            $time = new stdClass();
            $time->key = "time";
            $time->label = "TIME";
            $time->value = $typeModel->getFormatDateTime('g:i A');

            $contactPhone = new stdClass();
            $contactPhone->key = "phone";
            $contactPhone->label = "CONTACT";
            $contactPhone->value = $eventProduct->getVenuePhone();

        $eventKeys->auxiliaryFields[] = $time;
        $eventKeys->auxiliaryFields[] = $contactPhone;

        if( (string)$eventProduct->getPassBookBack() !== '' && $eventProduct->getPassBookBack()){
            $eventKeys->backFields = array();

                $terms = new stdClass();
                $terms->key = "back";
                $terms->label = $eventProduct->getPassBookBackTitle();
                $terms->value = $eventProduct->getPassBookBack();

            $eventKeys->backFields[] = $terms;
        }

        return $eventKeys;
    }

    /**
     * @param null $type
     * @param null $dev
     * @return string
     * @throws Mediotype_Core_Exception
     */
    public function createPassbook($type = null, $dev = null){

        /** @var $ticketUniqueDetail Mediotype_Ticket_Model_Order  */
        $ticketUniqueDetail = Mage::getModel('mediotype_ticket/order');
        $ticketUniqueDetail->load($this->purchasedData->getId());
        $orderedProduct = Mage::helper('mediotype_ticket')->loadTicketBySku($ticketUniqueDetail->getSku());

        $manifestData = new stdClass();

        $manifestData->description = $orderedProduct->getName();
        $manifestData->formatVersion = 1;
        $manifestData->organizationName = Mage::getStoreConfig('mediotype_ticket/apple_passbook_settings/passbook_organization_name');
        $manifestData->passTypeIdentifier = $orderedProduct->getPassTypeIdentifier();

            $order = Mage::getModel('sales/order')->load($this->purchasedData->getOrderId());
            if(!$order){
                throw new Mediotype_Core_Exception('Passbook cannot be generated due to no valid order exists');
            }

        $manifestData->serialNumber = $order->getIncrementId() . $this->purchasedData->getId();
        $manifestData->teamIdentifier = Mage::helper('core')->decrypt(Mage::getStoreConfig('mediotype_ticket/apple_passbook_settings/passbook_team_identifier'));

        $associatedAppKeys    = array(); //not implemented here...
        $relevanceKeys        = array(); //not implemented here...

        $manifestData->eventTicket = $this->_eventParameters();

        $barCode = new stdClass();
        $barCode->format = "PKBarcodeFormatQR";
        $barCode->message = $this->encodedMsg;
        $barCode->messageEncoding = 'iso-8859-1';

        $manifestData->barcode = $barCode;
        $manifestData->backgroundColor = 'rgb(107,156,196)';

        $webServiceKeys       = array(); //not implemented here...

        $this->pass->setJSON(json_encode($manifestData));

        $file = $this->pass->create(); //optional true for echo'ing output... needs refactoring

        if($this->pass->checkError()){
            throw new Mediotype_Core_Exception( $this->pass->getError() );
        }

        if($dev){
            file_put_contents('event.pkpass', $file); //tmp for dev purpose
        } //

        $path = Mage::getBaseDir('var') . DS . 'export' . DS ;
        $tmpFile = $ticketUniqueDetail->getId() . 'pass.pkpass';
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($tmpFile, 'w+');
        $io->streamLock(true);
        $io->streamWrite($file); //binary safe stream write
        $io->streamUnlock();
        $io->streamClose();

        return $path . $tmpFile ;
    }
}