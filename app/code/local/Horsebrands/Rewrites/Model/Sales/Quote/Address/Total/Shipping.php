<?php

class Horsebrands_Rewrites_Model_Sales_Quote_Address_Total_Shipping extends Mage_Sales_Model_Quote_Address_Total_Shipping {

  public function fetch(Mage_Sales_Model_Quote_Address $address){
    $amount = $address->getShippingAmount();
    if ($amount != 0 || $address->getShippingDescription()) {
      $title = Mage::helper('sales')->__('Shipping & Handling');
      if (false && $address->getShippingDescription()) {
        $title .= ' (' . $address->getShippingDescription() . ')';
      }
      $address->addTotal(array(
        'code' => $this->getCode(),
        'title' => $title,
        'value' => $address->getShippingAmount()
      ));
    }
    return $this;
  }

}
