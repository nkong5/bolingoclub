<?php

class Diecrema_Startpage_Adminhtml_StartpageController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('diecrema_startpage/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        return $this;
    }

    public function indexAction() {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('diecrema_startpage/adminhtml_startpage'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $startpageId     = $this->getRequest()->getParam('id');
        $startpageModel  = Mage::getModel('startpage/startpage')->load($startpageId);

        if ($startpageModel->getId() || $startpageId == 0) {

            Mage::register('startpage_data', $startpageModel);

            $this->loadLayout();
            $this->_setActiveMenu('diecrema_startpage/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('diecrema_startpage/adminhtml_startpage_edit'))
                 ->_addLeft($this->getLayout()->createBlock('diecrema_startpage/adminhtml_startpage_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('diecrema_startpage')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) {
            $params = $this->getRequest()->getPost();

            $id = $params['startpage_id'];

            if ($id) {
                $startpageModel = Mage::getModel('diecrema_startpage/startpage')->load($id);

                if (!$startpageModel->getId()) {
                    Mage::getSingleton('adminhtml/session')
                        ->addError($this->__('This user no longer exists.'));
                    $this->_redirect('*/*/');
                    return;
                }
            }else{

                unset($params['startpage_id']);
                $startpageModel = Mage::getModel('diecrema_startpage/startpage');
                $startpageCollectionIds = $startpageModel->getCollection()->getAllIds();
                $idProbable = end($startpageCollectionIds) + 1;
                $id = $idProbable;
            }

            try {
                $filesArr = array(
                    array('id' => 'teaser_1_large', 'name' => 'teaser_1_large'),
                    array('id' => 'teaser_1_small', 'name' => 'teaser_1_small'),
                    array('id' => 'teaser_1_small_txt', 'name' => 'teaser_1_small_txt'),
                    array('id' => 'teaser_2_large', 'name' => 'teaser_2_large'),
                    array('id' => 'teaser_2_small', 'name' => 'teaser_2_small'),
                    array('id' => 'teaser_2_small_txt', 'name' => 'teaser_2_small_txt'),
                    array('id' => 'teaser_3_large', 'name' => 'teaser_3_large'),
                    array('id' => 'teaser_3_small', 'name' => 'teaser_3_small'),
                    array('id' => 'teaser_3_small_txt', 'name' => 'teaser_3_small_txt')
                );

                if (!isset($params['store_code']) || !($storeCode = $params['store_code'])) {
                    throw new Exception('Store code is not set');
                }

                $pathStartpage = Mage::getBaseDir() . DS . 'media' . DS . 'startpage';
                if (!file_exists($pathStartpage)) {
                    mkdir($pathStartpage,0777);
                    chmod($pathStartpage, 0777);
                }

                $savedImages = false;
                foreach ($filesArr as $fileItem) {
                    if (!($files = $_FILES[$fileItem['id']]['name'])) {
                        continue;
                    }

                    # desitnation directory
                    $path = $pathStartpage . DS . "startpage_$storeCode". DS;
                    if (!file_exists($path)) {
                        mkdir($path,0777);
                        chmod($path, 0777);
                    }

                    # file name
                    $fnameOrigArr = explode('.', $files);
                    $ending = end($fnameOrigArr);
                    $fname = $fileItem['name'] . '.' . $ending;

                    # database value
                    $params[$fileItem['name']] = $fname;

                    $uploader = new Varien_File_Uploader($fileItem['id']); //load class
                    $uploader->setAllowedExtensions(array('png')); //Allowed extension for file
                    $uploader->setAllowCreateFolders(true); //for creating the directory if not exists
                    $uploader->setAllowRenameFiles(false); //if true, uploaded file's name will be changed, if file with the same name already exists directory.
                    $uploader->setFilesDispersion(false);
                    $uploader->save($path, $fname);
                    $savedImages = true;
                }

                $params['update_time'] = new Zend_Db_Expr("NOW()");

                // if record for the particular store already exists update it
                // else update already set record
                $resourceModel = Mage::getResourceModel('diecrema_startpage/startpage');
                if($result = $resourceModel->getPoster($storeCode)) {
                    $model = Mage::getModel('diecrema_startpage/startpage')->load($result['store_code']);
                    $model->setData($params);
                    $model->save();
                }else{
                    $startpageModel->setData($params);
                    $startpageModel->save();
                }

                // copy saved images to skin folder
                if ($savedImages) {
                    $helper = Mage::helper('diecrema_startpage/copyrecentimages');
                    $helper->copyrecentimages($storeCode);
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setStartpageData(false);

                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setStartpageData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $startpageModel = Mage::getModel('diecrema_startpage/startpage');

                $startpageModel->setId($this->getRequest()->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
    /**
     * Product grid for AJAX request.
     * Sort and filter result for example.
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('importedit/adminhtml_startpage_grid')->toHtml()
        );
    }
}