<?php
require_once 'MDN/Purchase/controllers/SuppliersController.php';

class Horsebrands_Rewrites_Purchase_SuppliersController extends MDN_Purchase_SuppliersController {

  public function SaveAction() {
    $supplier = Mage::getModel('Purchase/Supplier')->load($this->getRequest()->getParam('sup_id'));
    $currentTab = $this->getRequest()->getParam('current_tab');
    $data = $this->getRequest()->getPost();

    if (isset($data['sup_discount_level'])) {
      $data['sup_discount_level'] = str_replace(',', '.', $data['sup_discount_level']);
    }

    if($_FILES && isset($_FILES['sup_logo']['name']) && $_FILES['sup_logo']['name']!='') {

      try {
        $uploader = new Varien_File_Uploader('sup_logo');
        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything

        $uploader->setAllowRenameFiles(false);
        $uploader->setFilesDispersion(false);

        $path = Mage::getBaseDir('media') . DS ;
        $path .= Mage::getStoreConfig('purchase/misc/media_path');

        if(!file_exists($path)) {
          mkdir($path, 0777, true);
        }

        $filename = $supplier->getId() . '-' . $_FILES['sup_logo']['name'];
        $uploader->save($path, $filename);
        $data['sup_logo'] = $filename;
        
      }catch(Exception $e) {
        mage::log($e->getMessage(), null, 'suppliercontroller.log');
      }
    }

    $supplier->setData($data);
    $supplier->save();

    //confirm & redirect
    Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Supplier Saved'));
    $this->_redirect('Purchase/Suppliers/Edit', array('sup_id' => $supplier->getId(), 'tab' => $currentTab));
  }
}
