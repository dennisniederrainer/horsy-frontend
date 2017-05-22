<?php

class Horsebrands_Rewrites_Model_Customer_Observer {

  // remove inactive items from quote, when customer successfully logged in
  public function removeInactiveItems(Varien_Event_Observer $observer) {
    Mage::log('obs', null, 'fuqJo.log');
    // parameter: false == do not notify customer
    Mage::helper('horsebrands_rewrites/checkout')->removeInactiveCartItems(false);

    return $this;
  }
}
