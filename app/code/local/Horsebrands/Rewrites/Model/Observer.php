<?php

class Horsebrands_Rewrites_Model_Observer {

  public function paymentMethodIsActive(Varien_Event_Observer $observer) {
    $event = $observer->getEvent();
    $method = $event->getMethodInstance();
    $result = $event->getResult();
    // $result->isAvailable = true;
    $hidePaymentMethods = array('bankpayment');
    if (!empty($hidePaymentMethods)) {
      if (in_array($method->getCode(), $hidePaymentMethods)) {
        $result->isAvailable = false;
      }
    }
  }
}
