<?php
require_once(Mage::getModuleDir('controllers','Mage_Contacts').DS.'IndexController.php');

class Horsebrands_Deinverein_RegisterController extends Mage_Contacts_IndexController {

  const EMAIL_TEMPLATE_REGISTER_ID = 22;

  public function postAction() {
    $post = $this->getRequest()->getPost();
    if ( $post ) {
      $translate = Mage::getSingleton('core/translate');
      /* @var $translate Mage_Core_Model_Translate */
      $translate->setTranslateInline(false);
      try {
          $postObject = new Varien_Object();
          $postObject->setData($post);

          $error = false;

          if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
              $error = true;
          }

          if (!Zend_Validate::is(trim($post['message']) , 'NotEmpty')) {
              $error = true;
          }

          if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
              $error = true;
          }

          $fileName = '';
          if (isset($_FILES['logo']['name']) && $_FILES['logo']['name'] != '') {
            try {
              $fileName       = $_FILES['logo']['name'];
              $fileExt        = strtolower(substr(strrchr($fileName, ".") ,1));
              $fileNamewoe    = rtrim($fileName, $fileExt);
              $fileName = preg_replace('/\s+/', '', $fileNamewoe) . time() . '.' . $fileExt;

              $uploader       = new Varien_File_Uploader('logo');
              // $uploader->setAllowedExtensions(array('pdf', 'jpg', 'png', 'psd', 'ai')); //add more file types you want to allow
              $uploader->setAllowRenameFiles(false);
              $uploader->setFilesDispersion(false);
              $path = Mage::getBaseDir('media') . DS . 'contacts';
              if(!is_dir($path)){
                  mkdir($path, 0777, true);
              }
              $uploader->save($path . DS, $fileName );

            } catch (Exception $e) {
              Mage::getSingleton('customer/session')->addError($e->getMessage());
              $error = true;
            }
          }

          if ($error) {
              throw new Exception();
          }
          $mailTemplate = Mage::getModel('core/email_template');

          $attachmentFilePath = Mage::getBaseDir('media'). DS . 'contacts' . DS . $fileName;
          if(file_exists($attachmentFilePath)) {
            $fileContents = file_get_contents($attachmentFilePath);
            $mailTemplate->getMail()->createAttachment($fileContents,
                  Zend_Mime::TYPE_OCTETSTREAM,
                  Zend_Mime::DISPOSITION_ATTACHMENT,
                  Zend_Mime::ENCODING_BASE64,
                  $fileName);
          }

          $mailTemplate->setDesignConfig(array('area' => 'frontend'))
              ->setReplyTo($post['email'])
              ->sendTransactional(
                  self::EMAIL_TEMPLATE_REGISTER_ID,
                  Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                  Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
                  null,
                  array('data' => $postObject)
              );

          if (!$mailTemplate->getSentSuccess()) {
              throw new Exception();
          }

          $translate->setTranslateInline(true);

          Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
          $this->_redirect('anmeldung');

          return;
      } catch (Exception $e) {
          $translate->setTranslateInline(true);

          // Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
          Mage::getSingleton('customer/session')->addError($e);
          $this->_redirect('anmeldung');
          return;
      }

    } else {
        $this->_redirect('kontakt');
    }
}

}
