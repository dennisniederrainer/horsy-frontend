<?php

class Horsebrands_Rewrites_Model_Checkout_Observer {

    protected $_hasShipping = false;

    public function setQuoteShippingMethod() {
    if(!$this->_hasShipping) {
        $this->_hasShipping = true; // This is to avoid loops on totals collecting
        $quote = Mage::helper('checkout/cart')->getQuote();
        if (!$quote->getId()) return;
        $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
        if ($shippingMethod) return;
        $shippingAddress = $quote->getShippingAddress();

        if(!$shippingAddress->getCountryId()) {
            $customerAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultShipping();
            $address = Mage::getModel('customer/address')->load($customerAddressId);

            if($address->getCountryId()) {
                $shippingAddress->setCountryId($address->getCountryId());
            } else {
                $shippingAddress->setCountryId('DE');
            }
        }

        // if($shippingAddress->getCountryId() == 'DE') {
        $country = 'DE'; // Some country code
        $method = 'flatrate_flatrate'; // Used shipping method
        // } else {
        //     $country = $shippingAddress->getCountryId();
        //     $method = 'tablerate_bestway';
        // }

        // mage::log($shippingAddress->getCountryId(), null, 'opsobserver.log');

        $shippingAddress
            ->setCountryId($country)
            ->setShippingMethod($method)
            ->setCollectShippingRates(true)
        ;
        $shippingAddress->save();
        $quote->save();
    }
  }

  public function updateShipping($params = null) {
      $quote = Mage::helper('checkout/cart')->getQuote();
      if (!$quote->getId()) return;
      $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
      $shippingAddress = $quote->getShippingAddress();

      if($shippingMethod) return;

      // if($shippingAddress->getCountryId() == 'DE') {
      $country = 'DE'; // Some country code
      $method = 'flatrate_flatrate'; // Used shipping method
      // } else {
      //     $country = $shippingAddress->getCountryId();
      //     $method = 'tablerate_bestway';
      // }

      $shippingAddress
          ->setCountryId($country)
          ->setShippingMethod($method)
          ->setCollectShippingRates(true)
      ;
      $shippingAddress->save();
      $quote->save();
  }
}
