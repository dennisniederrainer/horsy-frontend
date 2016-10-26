<?php
require_once(Mage::getModuleDir('controllers','Mage_Checkout').DS.'OnepageController.php');
// require_once 'Mage/Checkout/controllers/OnepageController.php';

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
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }

            //save address to current checkout
            Mage::log('ja', null, 'opcController.log');
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);
            Mage::dispatchEvent('cart_updateShipping', array());

            if (!isset($result['error'])) {
                /* check quote for virtual */
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );

                /*
                 * use_for_shipping ist nun eine checkbox, die nur daten mitsendet, wenn sie "checked" ist.
                 * Deswegen nur abfrage, ob use_for_shipping nicht gesetzt
                 */
                } elseif (!isset($data['use_for_shipping'])) {
                    $this->getOnepage()->saveShipping($data, $customerAddressId);
                    Mage::dispatchEvent('cart_updateShipping', array());

                    $method = Mage::getSingleton('checkout/cart')->getQuote()->getShippingAddress()->getShippingMethod();

                    if($method) {
                        $this->getOnepage()->saveShippingMethod($method);
                        $this->getOnepage()->getQuote()->collectTotals()->save();
                        $result['goto_section'] = 'payment';
                        $result['update_section'] = array(
                            'name' => 'payment-method',
                            'html' => $this->_getPaymentMethodsHtml()
                        );

                    } else {
                        $result['goto_section'] = 'shipping_method';
                        $result['update_section'] = array(
                            'name' => 'shipping-method',
                            'html' => $this->_getShippingMethodsHtml()
                        );

                        $result['allow_sections'] = array('billing');
                    }

                    // $result['duplicateBillingInfo'] = 'true';

                } else {
                    //wenn billing != shipping wird von hier die saveShipping aufgerufen.
                    return $this->_saveShipping();
                }

                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            } else {
              Mage::log($result['message'], null, 'opcController.log');
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
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            Mage::dispatchEvent('cart_updateShipping', array());

            if (!isset($result['error'])) {

                $method = Mage::getSingleton('checkout/cart')->getQuote()->getShippingAddress()->getShippingMethod();
                if($method) {
                    $this->getOnepage()->saveShippingMethod($method);
                    $this->getOnepage()->getQuote()->collectTotals()->save();

                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } else {
                    $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );
                }
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
