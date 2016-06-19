<?php

class Horsebrands_Rewrites_Block_Checkout_Cart extends Mage_Checkout_Block_Cart {

  public function getItemsForStoreId($storeid) {
    // return $this->getQuote()->getItemsCollection();
    // return $items;

    return $this->getQuote()->getAllVisibleItems();
    //   ->getSelect()->addFieldToFilter('store_id', $storeid);
  }

}
