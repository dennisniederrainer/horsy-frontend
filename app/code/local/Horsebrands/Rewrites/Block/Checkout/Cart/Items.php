<?php

class Horsebrands_Rewrites_Block_Checkout_Cart_Items extends Mage_Checkout_Block_Cart {
  protected $_quoteShop = null;
  protected $_quoteDeals = null;

  // // get shop quote
  // public function getShopQuote() {
  //   if($_quoteShop == null) {
  //     $_quoteShop = Mage::getSingleton('checkout/session')->getQuote();
  //   }
  //   return $_quoteShop;
  // }
  //
  // // get deals quote
  // // TODO
  // // get shopping cart table for specific store
  // public function getCartTableHtml($quote) {
  // }
}
