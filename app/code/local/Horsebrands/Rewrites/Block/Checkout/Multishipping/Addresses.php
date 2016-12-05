<?php

class Horsebrands_Rewrites_Block_Checkout_Multishipping_Addresses extends Mage_Checkout_Block_Multishipping_Addresses {

  public function getBillingAddress() {
    $customer = Mage::getSingleton('customer/session')->getCustomer();

    if($address = $customer->getDefaultBillingAddress()) {
      return $address;
    }

    $address = Mage::getModel('customer/address')
      ->setCustomerId($customer->getId())
      ->setIsDefaultBilling('1')
      ->setSaveInAddressBook('1');

    return $address;
  }

  public function getShippingAddress() {
    $customer = Mage::getSingleton('customer/session')->getCustomer();

    if($address = $customer->getDefaultShippingAddress()) {
      return $address;
    }

    $address = Mage::getModel('customer/address')
      ->setCustomerId($customer->getId())
      ->setIsDefaultShipping('1')
      ->setSaveInAddressBook('1');

    return $address;
  }
}
