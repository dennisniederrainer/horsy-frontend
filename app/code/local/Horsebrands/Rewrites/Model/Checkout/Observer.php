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
      $method = 'tablerate_bestway';
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
    $onepage = Mage::getSingleton('checkout/type_onepage');

    $onepage->saveShippingMethod('tablerate_bestway');
    Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method');
    $onepage->getQuote()->collectTotals();
  }
}
