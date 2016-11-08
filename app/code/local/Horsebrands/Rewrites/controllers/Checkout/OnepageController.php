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
      //Ajax-Session expired?
      if ($this->_expireAjax()) {
          return;
      }
      //Post-Data available
      if ($this->getRequest()->isPost()) {
        //get billing data
        $data = $this->getRequest()->getPost('billing', array());
        $billingAddressId = $data['address_id']; //$this->getRequest()->getPost('billing[address_id]', false);

        if (isset($data['email'])) {
            $data['email'] = trim($data['email']);
        }

        //save address to current checkout
        // $result = $this->getOnepage()->saveBilling($data, $billingAddressId);

        if (!isset($result['error'])) {
          // if (!isset($data['use_for_shipping'])) {
          //   $result = $this->getOnepage()->saveShipping($data, $billingAddressId);
          //   $result = $this->getOnepage()->saveBilling($data, $billingAddressId);
          // } else {
            $shippingdata = $this->getRequest()->getPost('shipping', array());
            $shippingAddressId = $shippingdata['address_id']; //$this->getRequest()->getPost('shipping_address_id', false);
            // Mage::log('FTW', null, 'zong.log');
            $result = $this->getOnepage()->saveShipping($shippingdata, $shippingAddressId);

            $result = $this->getOnepage()->saveBilling($data, $billingAddressId);
          // }

          $method = 'tablerate_bestway';
          $result = $this->getOnepage()->saveShippingMethod($method);

          if (!isset($result['error'])) {
            $result['goto_section'] = 'payment';
            $result['update_section'] = array(
                'name' => 'payment-method',
                'html' => $this->_getPaymentMethodsHtml()
            );

            $result['allow_sections'] = array('payment');
            // $result['duplicateBillingInfo'] = 'true';
          }

          $this->getOnepage()->getQuote()->collectTotals()->save();
          $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        } else {
          Mage::log($result['error'], null, 'opcController.log');
        }
      }
    }

    protected function _saveShipping()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping[address_id]', false);

            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);
            // Mage::dispatchEvent('cart_updateShipping', array());

            if (!isset($result['error'])) {
                // $method = Mage::getSingleton('checkout/cart')->getQuote()->getShippingAddress()->getShippingMethod();
                // if($method) {
                //     $this->getOnepage()->saveShippingMethod($method);
                //     $this->getOnepage()->getQuote()->collectTotals()->save();
                //     $result['goto_section'] = 'payment';
                //     $result['update_section'] = array(
                //         'name' => 'payment-method',
                //         'html' => $this->_getPaymentMethodsHtml()
                //     );
                // } else {
                    $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );
                // }
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
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
}
