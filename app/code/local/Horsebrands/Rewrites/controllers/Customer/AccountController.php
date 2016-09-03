<?php

require_once 'Mage/Customer/controllers/AccountController.php';

class Horsebrands_Rewrites_Customer_AccountController extends Mage_Customer_AccountController {

    public function preDispatch()
    {
        // a brute-force protection here would be nice
        // parent::preDispatch();

        // if (!$this->getRequest()->isDispatched()) {
        //     return;
        // }

        $action = strtolower($this->getRequest()->getActionName());
        $openActions = array(
            'create',
            'login',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'changeforgotten',
            'resetpassword',
            'resetpasswordpost',
            'confirm',
            'confirmation'
        );
        $pattern = '/^(' . implode('|', $openActions) . ')/i';

        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }

     public function loginPostAction() {
       mage::log('1', null, 'batz.log');
        //  if (!$this->_validateFormKey()) {
        //      $this->_redirect('*/*/');
        //      return;
        //  }
         mage::log('2', null, 'batz.log');
        //  if ($this->_getSession()->isLoggedIn()) {
        //      $this->_redirect('*/*/');
        //      return;
        //  }
         $session = $this->_getSession();
         mage::log('3', null, 'batz.log');
         if ($this->getRequest()->isPost()) {
             $login = $this->getRequest()->getPost('login');
             if (!empty($login['username']) && !empty($login['password'])) {
                 try {
                     $session->login($login['username'], $login['password']);
                     if ($session->getCustomer()->getIsJustConfirmed()) {
                         $this->_welcomeCustomer($session->getCustomer(), true);
                     }
                 } catch (Mage_Core_Exception $e) {
                     switch ($e->getCode()) {
                         case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                             $value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
                             $message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                             break;
                         case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                             $message = $e->getMessage();
                             break;
                         default:
                             $message = $e->getMessage();
                     }
                     $session->addError($message);
                     $session->setUsername($login['username']);
                 } catch (Exception $e) {
                     // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                 }
             } else {
                 $session->addError($this->__('Login and password are required.'));
             }
         }
         mage::log($this->getRequest()->getPost('aktionenlogin'), null, 'batz.log');
         if($this->getRequest()->getPost('aktionenlogin') == null) {
           $this->_loginPostRedirect();
         } else {
           $this->_redirect('aktionen');
         }
     }
}
