<?php

class Horsebrands_Rewrites_Model_Sales_Quote extends Mage_Sales_Model_Quote {
  protected $_collectTotalsForStore = false;

  public function setCollectTotalsForStore($storeId) {
    $this->_collectTotalsForStore = $storeId;
  }
  public function getCollectTotalsForStore() {
    return $this->_collectTotalsForStore;
  }
}
