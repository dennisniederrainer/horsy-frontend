<?php
require_once(Mage::getModuleDir('controllers','Mage_Checkout').DS.'MultishippingController.php');

class Horsebrands_Rewrites_Checkout_MultishippingController extends Mage_Checkout_MultishippingController {

  public function addressesPostAction() {
    try {
      if ($this->getRequest()->isPost()) {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $billingdata = $this->getRequest()->getPost('billing', array());
        $billingAddressId = $billingdata['address_id'];

        if (isset($data['email'])) {
            $data['email'] = trim($data['email']);
        }

        $address = $customer->getAddressById($billingAddressId);
        $address = Mage::helper('horsebrands_rewrites/checkout')->updateAddress($address, $billingdata);

        $shopAddressId = Mage::helper('horsebrands_rewrites/checkout')->duplicateCustomerAddress($address);
        $dealsAddressId = Mage::helper('horsebrands_rewrites/checkout')->duplicateCustomerAddress($address);
        $this->_getCheckout()->setShippingItemsInformation($address->getId(), $shopAddressId, $dealsAddressId);

        // foreach ($this->_getCheckout()->getQuote()->getAllShippingAddresses() as $address) {
        //   echo $address->getPostcode();
        //   echo '<br />';
        // }
        //
        // die('fini.');

        $this->_getCheckout()->setCollectRatesFlag(true);
        $this->_getState()->setActiveStep(
          Mage_Checkout_Model_Type_Multishipping_State::STEP_SHIPPING
        );
        $this->_getState()->setCompleteStep(
          Mage_Checkout_Model_Type_Multishipping_State::STEP_SELECT_ADDRESSES
        );
        $this->_redirect('*/*/shipping');

        return;
      }

      if ($this->getRequest()->getParam('continue', false)) {
        $this->_getCheckout()->setCollectRatesFlag(true);
        $this->_getState()->setActiveStep(
          Mage_Checkout_Model_Type_Multishipping_State::STEP_SHIPPING
        );
        $this->_getState()->setCompleteStep(
          Mage_Checkout_Model_Type_Multishipping_State::STEP_SELECT_ADDRESSES
        );
        $this->_redirect('*/*/shipping');
      }
          elseif ($this->getRequest()->getParam('new_address')) {
              $this->_redirect('*/multishipping_address/newShipping');
          }
          else {
              $this->_redirect('*/*/addresses');
          }
          if ($shipToInfo = $this->getRequest()->getPost('ship')) {
              $this->_getCheckout()->setShippingItemsInformation($shipToInfo);
          }
      }
      catch (Mage_Core_Exception $e) {
          $this->_getCheckoutSession()->addError($e->getMessage());
          $this->_redirect('*/*/addresses');
      }
      catch (Exception $e) {
          $this->_getCheckoutSession()->addException(
              $e,
              Mage::helper('checkout')->__('Data saving problem')
          );
          $this->_redirect('*/*/addresses');
      }
  }

  public function overviewAction()
  {
      if (!$this->_validateMinimumAmount()) {
          return $this;
      }

      $this->_getState()->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_OVERVIEW);

      try {
          $payment = $this->getRequest()->getPost('payment', array());
          $payment['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_MULTISHIPPING
              | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
              | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
              | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
              | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;
          $this->_getCheckout()->setPaymentMethod($payment);

          $this->_getState()->setCompleteStep(
              Mage_Checkout_Model_Type_Multishipping_State::STEP_BILLING
          );

          $this->loadLayout();
          $this->_initLayoutMessages('checkout/session');
          $this->_initLayoutMessages('customer/session');
          $this->renderLayout();
      }
      catch (Mage_Core_Exception $e) {
          $this->_getCheckoutSession()->addError($e->getMessage());
          $this->_redirect('*/*/billing');
      }
      catch (Exception $e) {
          Mage::logException($e);
          $this->_getCheckoutSession()->addException($e, $this->__('Cannot open the overview page'));
          $this->_redirect('*/*/billing');
      }
  }

  public function overviewPostAction()
  {
    if (!$this->_validateFormKey()) {
        $this->_forward('backToAddresses');
        return;
    }

    if (!$this->_validateMinimumAmount()) {
        return;
    }

    try {
      if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
          $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
          if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
              $this->_getCheckoutSession()->addError($this->__('Please agree to all Terms and Conditions before placing the order.'));
              $this->_redirect('*/*/billing');
              return;
          }
      }

      $payment = $this->getRequest()->getPost('payment');
      $paymentInstance = $this->_getCheckout()->getQuote()->getPayment();

      if (isset($payment['cc_number'])) {
          $paymentInstance->setCcNumber($payment['cc_number']);
      }
      if (isset($payment['cc_cid'])) {
          $paymentInstance->setCcCid($payment['cc_cid']);
      }
      // $this->_createBundleOrder();
      $this->_getCheckout()->createOrders();

      if( $url = Mage::getSingleton('checkout/session')->getRedirectUrl() ) {
        $this->_redirectUrl( $url );
        return;
      }
      echo 'kein redirect in mscontroller';
      die();

      $this->_getState()->setActiveStep(
          Mage_Checkout_Model_Type_Multishipping_State::STEP_SUCCESS
      );
      $this->_getState()->setCompleteStep(
          Mage_Checkout_Model_Type_Multishipping_State::STEP_OVERVIEW
      );
      $this->_getCheckout()->getCheckoutSession()->clear();
      $this->_getCheckout()->getCheckoutSession()->setDisplaySuccess(true);
      $this->_redirect('*/*/success');
    } catch (Mage_Payment_Model_Info_Exception $e) {
      $message = $e->getMessage();
      if ( !empty($message) ) {
          $this->_getCheckoutSession()->addError($message);
      }
      $this->_redirect('*/*/billing');
    } catch (Mage_Checkout_Exception $e) {
      Mage::helper('checkout')
          ->sendPaymentFailedEmail($this->_getCheckout()->getQuote(), $e->getMessage(), 'multi-shipping');
      $this->_getCheckout()->getCheckoutSession()->clear();
      $this->_getCheckoutSession()->addError($e->getMessage());
      $this->_redirect('*/cart');
    }
    catch (Mage_Core_Exception $e) {
      Mage::helper('checkout')
          ->sendPaymentFailedEmail($this->_getCheckout()->getQuote(), $e->getMessage(), 'multi-shipping');
      $this->_getCheckoutSession()->addError($e->getMessage());
      $this->_redirect('*/*/billing');
    } catch (Exception $e) {
      Mage::logException($e);
      Mage::helper('checkout')
          ->sendPaymentFailedEmail($this->_getCheckout()->getQuote(), $e->getMessage(), 'multi-shipping');
      $this->_getCheckoutSession()->addError($this->__('Order place error.'));
      $this->_redirect('*/*/billing');
    }
  }

  protected function _createBundleOrder() {
    $quote = $this->_getCheckout()->getQuote();

    $quote->getShippingAddress()->setPostcode('10245');
    // $quote->setFeeAmount(4.95);
    $quote->collectTotals()->save();
    // $quote->setInventoryProcessed(true);

    $service = Mage::getModel('sales/service_quote', $quote);
    $service->submitAll();

    $order = $service->getOrder();
    if ($order) {
        Mage::dispatchEvent('checkout_type_onepage_save_order_after',
            array('order'=>$order, 'quote'=>$quote));

        /**
         * a flag to set that there will be redirect to third party after confirmation
         * eg: paypal standard ipn
         */
        $redirectUrl = $quote->getPayment()->getOrderPlaceRedirectUrl();
        /**
         * we only want to send to customer about new order when there is no redirect to third party
         */
        if (!$redirectUrl && $order->getCanSendNewEmailFlag()) {
            try {
                $order->queueNewOrderEmail();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        // add order information to the session
        Mage::getSingleton('checkout/session')->setLastOrderId($order->getId())
            ->setRedirectUrl($redirectUrl)
            ->setLastRealOrderId($order->getIncrementId());

        // as well a billing agreement can be created
        $agreement = $order->getPayment()->getBillingAgreement();
        if ($agreement) {
            $this->_checkoutSession->setLastBillingAgreementId($agreement->getId());
        }
    }

    return $order;
  }
}
