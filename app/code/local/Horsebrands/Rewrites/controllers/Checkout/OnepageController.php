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

    /**
     * horsebrands: neue saveBillingAction Methode, die Billing und Shipping-Daten beachtet
     */
    public function saveAddressAction() {
      if ($this->_expireAjax()) {
          return;
      }

      //Post-Data available
      if ($this->getRequest()->isPost()) {
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

          $method = 'tablerate_bestway';
          // $method = 'freeshipping_freeshipping';
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
