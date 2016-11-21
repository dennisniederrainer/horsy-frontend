<?php
require_once(Mage::getModuleDir('controllers','Mage_Checkout').DS.'OnepageController.php');

class Horsebrands_Rewrites_Checkout_OnepageController extends Mage_Checkout_OnepageController {
    private $_quote = null;
    public function getQuote() {
        if (empty($this->_quote)) {
            $this->_quote = Mage::getSingleton('checkout/session')->getQuote();
        }
        return $this->_quote;
    }

    public function indexAction()
    {
        if (!Mage::helper('checkout')->canOnepageCheckout()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));
            $this->_redirect('checkout/cart');
            return;
        }

        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }

        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }

        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure'=>true)));

        $this->getOnepage()->initCheckout();
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Checkout'));

        $method = Mage_Checkout_Model_Type_Onepage::METHOD_GUEST;
        $this->getOnepage()->saveCheckoutMethod($method);

        $this->renderLayout();
    }

    /**
     * horsebrands: neue saveBillingAction Methode, die Billing und Shipping-Daten beachtet
     */
    public function saveAddressAction() {
    // public function saveBillingAction() {
      //Ajax-Session expired?
      if ($this->_expireAjax()) {
          return;
      }
      //Post-Data available
      if ($this->getRequest()->isPost()) {
        //get billing data
        $data = $this->getRequest()->getPost('billing', array());
        $billingAddressId = $this->getRequest()->getPost('billing_address_id', false); //$data['address_id'];

        if (isset($data['email'])) {
            $data['email'] = trim($data['email']);
        }

        if (!isset($result['error'])) {
          $shippingdata = $this->getRequest()->getPost('shipping', array());
          $shippingAddressId = $this->getRequest()->getPost('shipping_address_id', false); //$shippingdata['address_id'];
          $result = $this->getOnepage()->saveShipping($shippingdata, $shippingAddressId);

          $result = $this->getOnepage()->saveBilling($data, $billingAddressId);

          // $method = 'tablerate_bestway';
          $method = 'freeshipping_freeshipping';
          $result = $this->getOnepage()->saveShippingMethod($method);

          if (!isset($result['error'])) {
            $result['goto_section'] = 'payment';
            $result['update_section'] = array(
                'name' => 'payment-method',
                'html' => $this->_getPaymentMethodsHtml()
            );

            $result['allow_sections'] = array('payment');
          }

          $this->getOnepage()->getQuote()->collectTotals()->save();
          $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        } else {
          Mage::log($result['error'], null, 'opcController.log');
        }
      }
    }

    protected function _getShippingMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_shippingmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
    public function updateGrandtotalAction() {
        if ($this->_expireAjax()) {
            return;
        }
        $this->getQuote()->collectTotals()->save();
        $grandtotal = $this->getQuote()->getGrandTotal();
        //Populate Resultarray
        $result = array("grandbasetotal"=>$grandtotal);
        $result['goto_section'] = 'payment';
        //Encode Result in JSON
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        return $result;
    }

    public function saveOrderAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*');
            return;
        }

        if ($this->_expireAjax()) {
            return;
        }

        $result = array();
        try {
            $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
            if ($requiredAgreements) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                $diff = array_diff($requiredAgreements, $postedAgreements);
                if ($diff) {
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = $this->__('Please agree to all the terms and conditions before placing the order.');
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
            }

            $data = $this->getRequest()->getPost('payment', array());
            if ($data) {
                $data['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT
                    | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
                    | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
                    | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
                    | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;
                $this->getOnepage()->getQuote()->getPayment()->importData($data);
            }

            $orders = $this->getOnepage()->saveOrder();
            // foreach ($orders as $order) {
            //   echo count($order->getAllItems()) . '<br /><br />';
            // }
            // // var_dump($order->getPayment()->getMethodInstance());
            // die('PLACED kazong.');

            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
            $result['success'] = true;
            $result['error']   = false;
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $message = $e->getMessage();
            if (!empty($message)) {
                $result['error_messages'] = $message;
            }
            $result['goto_section'] = 'payment';
            $result['update_section'] = array(
                'name' => 'payment-method',
                'html' => $this->_getPaymentMethodsHtml()
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $e->getMessage();

            $gotoSection = $this->getOnepage()->getCheckout()->getGotoSection();
            if ($gotoSection) {
                $result['goto_section'] = $gotoSection;
                $this->getOnepage()->getCheckout()->setGotoSection(null);
            }
            $updateSection = $this->getOnepage()->getCheckout()->getUpdateSection();
            if ($updateSection) {
                if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                    $result['update_section'] = array(
                        'name' => $updateSection,
                        'html' => $this->$updateSectionFunction()
                    );
                }
                $this->getOnepage()->getCheckout()->setUpdateSection(null);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success']  = false;
            $result['error']    = true;
            $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
        }
        $this->getOnepage()->getQuote()->save();
        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function applyCouponAction() {
      $this->loadLayout('checkout_onepage_review');

      $this->couponCode = (string) $this->getRequest()->getParam('coupon_code');
      $codeLength = strlen($this->couponCode);
      $isCodeLengthValid = $codeLength && $codeLength <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;

      Mage::getSingleton('checkout/cart')->getQuote()->getShippingAddress()->setCollectShippingRates(true);
      Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($isCodeLengthValid ? $this->couponCode : '')->collectTotals()->save();

      // if ($codeLength) {
      //     if ($isCodeLengthValid && $this->couponCode == $this->_getQuote()->getCouponCode()) {
      //         Mage::getSingleton('checkout/session')->addSuccess(
      //             $this->__('Coupon code "%s" was applied.', Mage::helper('core')->escapeHtml($this->couponCode))
      //         );
      //     } else {
      //         Mage::getSingleton('checkout/session')->addError(
      //             $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($this->couponCode))
      //         );
      //     }
      // } else {
      //     Mage::getSingleton('checkout/session')->addSuccess($this->__('Coupon code was canceled.'));
      // }
      //
      // $this->_initLayoutMessages('checkout/session');

      $result['goto_section'] = 'review';
      $result['update_section'] = array( 'name' => 'review', 'html' => $this->_getReviewHtml() );

      $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}
