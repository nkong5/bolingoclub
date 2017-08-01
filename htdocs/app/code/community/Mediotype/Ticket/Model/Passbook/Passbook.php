<?php
/**
 * Magento / Mediotype Module
 * 
 *
 * @desc        
 * @category    Mediotype
 * @package     Mediotype_Ticket
 * @class       Mediotype_Ticket_Model_Passbook_Passbook
 * @copyright   Copyright (c) 2013 Mediotype (http://www.mediotype.com)
 *              Copyright, 2013, Mediotype, LLC - US license
 * @license     http://mediotype.com/LICENSE.txt
 * @author      Mediotype (SZ,JH) <diveinto@mediotype.com>
 */
class Mediotype_Ticket_Model_Passbook_Passbook extends Mage_Core_Model_Abstract{

    protected $x509Cert;

    protected $x509PrivateKey;

    protected $keyPass = '';

    /*
     * Name of the downloaded file.
     */
    protected $generatedFileName;

    /*
     * @var Array of [key] => [imgString] where key is standard passbook contents
     * !important   logo.png is a requirement
     */
    protected $images = array();

    protected $json;

    protected $SHAs;

    protected $WWDRPath = '';

    protected $tmpDir = '/tmp/';

    /*
	 * Holds error info if an error occured
	 */
    private $errMsg = '';

    private $tmpUniqId = null;

    public function _construct(){
        $this->tmpDir = Mage::getBaseDir('tmp');
    }

    /**
     * Sets the path to a x509 cert
     *
     * @param $certString
     * @return bool
     */
    public function setXCertificate($certString) {
            $this->x509Cert = $certString;
            return true;
    }

    /**
     * @param $keyString
     * @return bool
     */
    public function setXPrivateKey($keyString) {
            $this->x509PrivateKey = $keyString;
            return true;
    }

    /**
     * @param $p
     * @return bool
     */
    public function setCertificatePassword($p) {
        $this->keyPass = $p;
        return true;
    }

    /**
     * @param $path
     * @return bool
     */
    public function setAppleBaseCertPath($path) {
        $this->WWDRPath = $path;
        return true;
    }

    /**
     * @param string $json
     * @return bool
     */
    public function setJson($json) {
        if(json_decode($json) !== false) {
            $this->json = $json;
            return true;
        }
        $this->errMsg = 'This is not a JSON string.';
        return false;
    }

    /**
     * @param string $imgPath
     * @param string|null $name
     * @return bool
     */
    public function addImage($imgPath, $name = null){
        if(file_exists($imgPath)){
            $name = ($name === NULL) ? basename($imgPath) : $name;
            $this->images[$name] = $imgPath;
            return true;
        }
        $this->errMsg = 'File does not exist.';
        return false;
    }

    /**
     * @param bool $output
     * @return bool|string
     */
    public function create($output = false) {

        $tmpFiles = $this->_paths();

        //Creates and saves the json manifest
        if(!($manifest = $this->_createManifest())){
            $this->_clean();
            return false;
        }

        //Create signature
        if($this->_createSignature($manifest) == false) {
            $this->_clean();
            return false;
        }

        if($this->_createZip($manifest) == false) {
            $this->_clean();
            return false;
        }

        // Check if pass is created and valid
        if(!file_exists($tmpFiles['pkpass']) || filesize($tmpFiles['pkpass']) < 1) {
            $this->errMsg = 'Error while creating pass.pkpass. Check your Zip extension.';
            $this->_clean();
            return false;
        }

        //TODO remove output from this class, return file only
        // Output pass
        if($output == true) {
            $fileName = ($this->getGeneratedFileName()) ? $this->getGeneratedFileName() : basename($tmpFiles['pkpass']);
            header('Pragma: no-cache');
            header('Content-type: application/vnd.apple.pkpass');
            header('Content-length: '.filesize($tmpFiles['pkpass']));
            header('Content-Disposition: attachment; filename="'.$fileName.'"');
            echo file_get_contents($tmpFiles['pkpass']);
            $this->_clean();
        } else {
            $file = file_get_contents($tmpFiles['pkpass']);
            $this->_clean();

            return $file;
        }
    }

    /**
     * @return string
     */
    public function getGeneratedFileName()
    {
        return $this->generatedFileName;
    }

    /**
     * @param string $name
     */
    public function setGeneratedFileName($name)
    {
        $this->generatedFileName = $name;
    }

    /**
     * @return bool
     */
    public function checkError() {
        if(trim($this->errMsg) == '') {
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getError() {
        return $this->errMsg;
    }

    /**
     * @return bool|string
     */
    protected function _createManifest() {
        // Creates SHA hashes for all files in package
        $this->SHAs['pass.json'] = sha1($this->json);
        $hasicon = false;
        foreach($this->images as $name => $imgPath) {
            if(strtolower($name) == 'icon.png'){
                $hasicon = true;
            }
            $this->SHAs[$name] = sha1(file_get_contents($imgPath));
        }

        if(!$hasicon){
            $this->errMsg = 'Missing required icon.png file.';
            $this->_clean();
            return false;
        }

        $manifest = json_encode((object)$this->SHAs);
        return $manifest;
    }

    /**
     * @param $signature
     * @return string
     */
    protected function _convertPEMtoDER($signature) {
        $begin = 'filename="smime.p7s"';
        $end = '------';
        $signature = substr($signature, strpos($signature, $begin)+strlen($begin));

        $signature = substr($signature, 0, strpos($signature, $end));
        $signature = trim($signature);
        $signature = base64_decode($signature);

        return $signature;
    }

    protected $_passtypeCert;
    public function setPasstypeCert($filePath){
        $this->_passtypeCert = $filePath;
    }

    public function getPasstypeCert()
    {
        return $this->_passtypeCert;
    }


    /**
     * @param $manifest
     * @return bool
     */
    protected function _createSignature($manifest) {
        $tmpFiles = $this->_paths();
        file_put_contents($tmpFiles['manifest'], $manifest);
        $pkcs12 = file_get_contents($this->getPasstypeCert());
        $certs = array();
        if(openssl_pkcs12_read($pkcs12, $certs, $this->keyPass) == true) {
            $cert = openssl_x509_read($certs['cert']);
            $key = openssl_pkey_get_private($certs['pkey'], $this->keyPass );
            if($cert && $key){
                if(!empty($this->WWDRPath)){
                    if(!file_exists($this->WWDRPath)){
                        $this->errMsg = 'WWDR Intermediate Certificate does not exist';
                        return false;
                    }
                    openssl_pkcs7_sign($tmpFiles['manifest'], $tmpFiles['signature'], $cert, $key, array(), PKCS7_BINARY | PKCS7_DETACHED, $this->WWDRPath);
                }else{
                    openssl_pkcs7_sign($tmpFiles['manifest'], $tmpFiles['signature'], $cert, $key, array(), PKCS7_BINARY | PKCS7_DETACHED);
                }

                $signature = file_get_contents($tmpFiles['signature']);
                $signature = $this->_convertPEMtoDER($signature);
                file_put_contents($tmpFiles['signature'], $signature);

                return true;
            } else {
                $this->errMsg = 'Could not read the certificate';
                return false;
            }

        } else {
            $this->errMsg = 'Could not read the certificate';
            return false;
        }
    }

    /**
     * @param $manifest
     * @return bool
     */
    protected function _createZip($manifest) {
        $tmpFiles = $this->_paths();

        // Package file in Zip (as .pkpass)
        $zip = new ZipArchive();
        if(!$zip->open($tmpFiles['pkpass'], ZipArchive::CREATE)) {
            $this->errMsg = 'Could not open '.basename($tmpFiles['pkpass']).' with ZipArchive extension.';
            return false;
        }

        $zip->addFile($tmpFiles['signature'],'signature');
        $zip->addFromString('manifest.json',$manifest);
        $zip->addFromString('pass.json',$this->json);
        foreach($this->images as $name => $path){
            $zip->addFile($path, $name);
        }
        $zip->close();

        return true;
    }

    /**
     * Declares all paths used for temporary files.
     *
     * @return array
     */
    protected function _paths() {
        //Declare base paths
        $paths = array(
            'pkpass' 	=> 'pass.pkpass',
            'signature' => 'signature',
            'manifest' 	=> 'manifest.json'
        );

        //If trailing slash is missing, add it
        if(substr($this->tmpDir, -1) != '/') {
            $this->tmpDir = $this->tmpDir.'/';
        }

        if (empty($this->tmpUniqId)) {
            $this->tmpUniqId = uniqid('PKPass', true);

            if (!is_dir($this->tmpDir.$this->tmpUniqId)) {
                mkdir($this->tmpDir.$this->tmpUniqId);
            }
        }

        //Add temp folder path
        foreach($paths AS $pathName => $path) {
            $paths[$pathName] = $this->tmpDir.$this->tmpUniqId.'/'.$path;
        }

        return $paths;
    }

    /**
     * Removes all temporary files
     *
     * @return bool
     */
    protected function _clean() {
        $paths = $this->_paths();

        foreach($paths AS $path) {
            if(file_exists($path)) {
                unlink($path);
            }
        }

        //Remove our unique temporary folder
        if (is_dir($this->tmpDir.$this->tmpUniqId)) {
            rmdir($this->tmpDir.$this->tmpUniqId);
        }

        return true;
    }

}