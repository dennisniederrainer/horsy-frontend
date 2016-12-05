<?php

class Horsebrands_Rewrites_Model_Checkout_Type_Multishipping_State extends Mage_Checkout_Model_Type_Multishipping_State {

  public function __construct(){
    parent::__construct();
    $this->_steps = array(
        self::STEP_SELECT_ADDRESSES => new Varien_Object(array(
            'label' => Mage::helper('checkout')->__('Billing Information')
        )),
        self::STEP_SHIPPING => new Varien_Object(array(
            'label' => 'Shipping Information'
        )),
        self::STEP_BILLING => new Varien_Object(array(
            'label' => Mage::helper('checkout')->__('Payment Methods')
        )),
        self::STEP_OVERVIEW => new Varien_Object(array(
            'label' => Mage::helper('checkout')->__('Order Review')
        )),
        self::STEP_SUCCESS => new Varien_Object(array(
            'label' => 'Order Success'
        )),
    );

    foreach ($this->_steps as $step) {
        $step->setIsComplete(false);
    }

    $this->_checkout = Mage::getSingleton('checkout/type_multishipping');
    $this->_steps[$this->getActiveStep()]->setIsActive(true);
  }
}
