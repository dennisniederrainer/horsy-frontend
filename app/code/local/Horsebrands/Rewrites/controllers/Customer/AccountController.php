<?php

require_once 'Mage/Customer/controllers/AccountController.php';

class Horsebrands_Rewrites_Customer_AccountController extends Mage_Customer_AccountController {

  public function setProcessingAction() {
    die('done.');
    $order = Mage::getModel('sales/order')->loadByIncrementId('100000496');
    $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING)
        ->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING);

    $order->setBaseDiscountCanceled(0);
    $order->setBaseShippingCanceled(0);
    $order->setBaseSubtotalCanceled(0);
    $order->setBaseTaxCanceled(0);
    $order->setBaseTotalCanceled(0);
    $order->setDiscountCanceled(0);
    $order->setShippingCanceled(0);
    $order->setSubtotalCanceled(0);
    $order->setTaxCanceled(0);
    $order->setTotalCanceled(0);

    // uncancel items
    foreach ($order->getAllItems() as $item) {
      $item->setQtyCanceled(0);
      $item->setTaxCanceled(0);
      $item->setHiddenTaxCanceled(0);
      $item->save();
    }

    $order->save();

    die('done.');
  }

  public function assignCustomerToOrderAction() {
    // $order = Mage::getModel('sales/order')->loadByIncrementId('200002232');
    // $customer = Mage::getModel('customer/customer')->load(29319);
    //
    // $order->setCustomerId($customer->getId());
    // $order->setCustomerFirstname($customer->getFirstname());
    // $order->setCustomerLastname($customer->getLastname());
    // $order->setCustomerEmail($customer->getEmail());
    //
    // $order->save();

    die('done.');
  }


  public function loginPostAction() {
    if (!$this->_validateFormKey()) {
      $this->_redirect('*/*/');
      return;
    }

    if ($this->_getSession()->isLoggedIn()) {
      $this->_redirect('*/*/');
      return;
    }
    $session = $this->_getSession();
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
          Mage::getSingleton('core/session')->addError($message);
          $session->setUsername($login['username']);
        } catch (Exception $e) {
          // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
        }
      } else {
        $session->addError($this->__('Login and password are required.'));
      }
    }

    if($this->getRequest()->getPost('aktionenlogin') == null) {
      $this->_loginPostRedirect();
    } else {
      $this->_redirect('aktionen');
    }
  }

  public function editPostAction()
  {
      if (!$this->_validateFormKey()) {
          return $this->_redirect('*/*/edit');
      }

      if ($this->getRequest()->isPost()) {
          /** @var $customer Mage_Customer_Model_Customer */
          $customer = $this->_getSession()->getCustomer();

          /** @var $customerForm Mage_Customer_Model_Form */
          $customerForm = $this->_getModel('customer/form');
          $customerForm->setFormCode('customer_account_edit')
              ->setEntity($customer);

          $customerData = $customerForm->extractData($this->getRequest());

          $errors = array();
          $customerErrors = $customerForm->validateData($customerData);
          if ($customerErrors !== true) {
              $errors = array_merge($customerErrors, $errors);
          } else {
              $customerForm->compactData($customerData);
              $errors = array();

              // If password change was requested then add it to common validation scheme
              $currPass   = $this->getRequest()->getPost('current_password', false);
              $newPass    = $this->getRequest()->getPost('password', false);
              $confPass   = $this->getRequest()->getPost('confirmation', false);
              if ($currPass && $newPass && $confPass) {
                  $oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
                  if ( $this->_getHelper('core/string')->strpos($oldPass, ':')) {
                      list($_salt, $salt) = explode(':', $oldPass);
                  } else {
                      $salt = false;
                  }

                  if ($customer->hashPassword($currPass, $salt) == $oldPass) {
                      if (strlen($newPass)) {
                          /**
                           * Set entered password and its confirmation - they
                           * will be validated later to match each other and be of right length
                           */
                          $customer->setPassword($newPass);
                          $customer->setPasswordConfirmation($confPass);
                      } else {
                          $errors[] = $this->__('New password field cannot be empty.');
                      }
                  } else {
                      $errors[] = $this->__('Invalid current password');
                  }
              }

              // Validate account and compose list of errors if any
              $customerErrors = $customer->validate();
              if (is_array($customerErrors)) {
                  $errors = array_merge($errors, $customerErrors);
              }
          }

          if (!empty($errors)) {
              $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
              foreach ($errors as $message) {
                  $this->_getSession()->addError($message);
              }
              $this->_redirect('*/*/edit');
              return $this;
          }

          try {
              $customer->cleanPasswordsValidationData();
              $customer->save();
              $this->_getSession()->setCustomer($customer)
                  ->addSuccess($this->__('The account information has been saved.'));

              $this->_redirect('customer/account');
              return;
          } catch (Mage_Core_Exception $e) {
              $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                  ->addError($e->getMessage());
          } catch (Exception $e) {
              $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                  ->addException($e, $this->__('Cannot save the customer.'));
          }
      }

      $this->_redirect('*/*/edit');
  }

  public function confirmAction()
  {
    $session = $this->_getSession();
    if ($session->isLoggedIn()) {
      $this->_getSession()->logout()->regenerateSessionId();
    }
    try {
      $id      = $this->getRequest()->getParam('id', false);
      $key     = $this->getRequest()->getParam('key', false);
      $backUrl = $this->getRequest()->getParam('back_url', false);
      if (empty($id) || empty($key)) {
        throw new Exception($this->__('Bad request.'));
      }

      // load customer by id (try/catch in case if it throws exceptions)
      try {
        $customer = $this->_getModel('customer/customer')->load($id);
        if ((!$customer) || (!$customer->getId())) {
          throw new Exception('Failed to load customer by id.');
        }
      }
      catch (Exception $e) {
        throw new Exception($this->__('Wrong customer account specified.'));
      }

      // check if it is inactive
      if ($customer->getConfirmation()) {
        if ($customer->getConfirmation() !== $key) {
          throw new Exception($this->__('Wrong confirmation key.'));
        }

        // activate customer
        try {
          $customer->setConfirmation(null);
          $customer->save();
          Mage::dispatchEvent(
            'customer_confirmation_successful',
            array('customer' => $customer)
          );
        }
        catch (Exception $e) {
          throw new Exception($this->__('Failed to confirm customer account.'));
        }

        // log in and send greeting email, then die happy
        $session->setCustomerAsLoggedIn($customer);
        $successUrl = $this->_welcomeCustomer($customer, true);
        $this->_redirectSuccess($backUrl ? $backUrl : $successUrl);
        return;
      }

      // die happy
      $this->_redirectSuccess($this->_getUrl('*/*/index', array('_secure' => true)));
      return;
    }
    catch (Exception $e) {
      // die unhappy
      $this->_getSession()->addError($e->getMessage());
      $this->_redirectError($this->_getUrl('*/*/index', array('_secure' => true)));
      return;
    }
  }

  protected function _getCustomer()
  {
      $customer = $this->_getFromRegistry('current_customer');
      if (!$customer) {
          $customer = $this->_getModel('customer/customer')->setId(null);
          // automatically subscribe new customer to default subscriber list.
          $customer->setIsSubscribed(1);
      }
      /**
       * Initialize customer group id
       */
      $customer->getGroupId();

      return $customer;
  }
}
