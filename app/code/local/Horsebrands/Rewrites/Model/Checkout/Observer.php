<?php

class Horsebrands_Rewrites_Model_Checkout_Observer {
    /**
     * Has shipping been applied to quote?
     *
     * @var bool
     */
    protected $_hasShipping = false;
    /**
     * Set shipping method and rate if they do not exist yet
     */
    public function setQuoteShippingMethod()
    {
        if(!$this->_hasShipping) {
            $this->_hasShipping = true; // This is to avoid loops on totals collecting
            $quote = Mage::helper('checkout/cart')->getQuote();
            if (!$quote->getId()) return;
            $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
            if ($shippingMethod) return;
            $shippingAddress = $quote->getShippingAddress();
            $country = 'DE'; // Some country code
            $postcode = ''; // Some postcode
            $regionId = ''; // Some region id
            $method = 'tablerate_bestway'; // Used shipping method
            $shippingAddress
                ->setCountryId($country)
                ->setRegionId($regionId)
                ->setPostcode($postcode)
                ->setShippingMethod($method)
                ->setCollectShippingRates(true)
            ;
            $shippingAddress->save();
            $quote->save();
        }
    }

    public function updateShipping($params = null) {
      // $quote = Mage::getSingleton('checkout/session')->getQuote();
      $onepage = Mage::getSingleton('checkout/type_onepage');

      // $country = "DE"; /* To test */
      // $address = $quote->getShippingAddress();
      // $address->setCountryId($country)->setCollectShippingRates(true)->collectShippingRates();
      $onepage->saveShippingMethod('flatrate_flatrate');
      Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method');
      $onepage->getQuote()->collectTotals();

      // $quote = Mage::helper('checkout/cart')->getQuote();
      // Mage::log('1', null, 'observer.log');
      // if (!$quote->getId()) return;
      //
      // // $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
      // $shippingAddress = $quote->getShippingAddress();
      // // if($shippingMethod) return;
      //
      // Mage::log('2', null, 'observer.log');
      // $country = 'DE'; // Some country code
      // $method = 'flatrate_flatrate'; // Used shipping method
      // $shippingAddress
      //     ->setCountryId($country)
      //     ->setShippingMethod($method)
      //     ->setCollectShippingRates(true)
      // ;
      // $shippingAddress->save();
      // $quote->save();
  }
}
