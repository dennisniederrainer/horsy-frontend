<?php

class Horsebrands_Rewrites_Model_Sales_Quote_Address extends Mage_Sales_Model_Quote_Address {

  public function getAllItems($storeId = false)
  {
      // We calculate item list once and cache it in three arrays - all items, nominal, non-nominal
      $cachedItems = $this->_nominalOnly ? 'nominal' : ($this->_nominalOnly === false ? 'nonnominal' : 'all');
      $key = 'cached_items_' . $cachedItems;
      if (!$this->hasData($key)) {
          // For compatibility  we will use $this->_filterNominal to divide nominal items from non-nominal
          // (because it can be overloaded)
          // So keep current flag $this->_nominalOnly and restore it after cycle
          $wasNominal = $this->_nominalOnly;
          $this->_nominalOnly = true; // Now $this->_filterNominal() will return positive values for nominal items

          $quoteItems = $this->getQuote()->getItemsCollection();
          $addressItems = $this->getItemsCollection();

          $items = array();
          $nominalItems = array();
          $nonNominalItems = array();
          if ($this->getQuote()->getIsMultiShipping() && $addressItems->count() > 0) {
              foreach ($addressItems as $aItem) {
                  if ($aItem->isDeleted()) {
                      continue;
                  }

                  if (!$aItem->getQuoteItemImported()) {
                      $qItem = $this->getQuote()->getItemById($aItem->getQuoteItemId());
                      if ($qItem) {
                          $aItem->importQuoteItem($qItem);
                      }
                  }
                  $items[] = $aItem;
                  if ($this->_filterNominal($aItem)) {
                      $nominalItems[] = $aItem;
                  } else {
                      $nonNominalItems[] = $aItem;
                  }
              }
          } else {
              /*
              * For virtual quote we assign items only to billing address, otherwise - only to shipping address
              */
              $addressType = $this->getAddressType();
              $canAddItems = $this->getQuote()->isVirtual()
                  ? ($addressType == self::TYPE_BILLING)
                  : ($addressType == self::TYPE_SHIPPING);

              if ($canAddItems) {
                  foreach ($quoteItems as $qItem) {
                      if ($qItem->isDeleted()) {
                          continue;
                      }

                      // horsebrands: only items for specific storeId
                      // mage::log('storeid: '.Mage::getSingleton('checkout/session')->getData('totals_for_store'), null, 'SUBTOTAL.log');
                      // if (Mage::getSingleton('checkout/session')->getData('totals_for_store')
                      //   && $qItem->getStoreId() != Mage::getSingleton('checkout/session')->getData('totals_for_store')) {
                      //     continue;
                      // }
                      // mage::log('storeid: '.Mage::getSingleton('checkout/session')->getData('totals_for_store'), null, 'SUBTOTAL.log');
                      if (Mage::getSingleton('checkout/session')->getTotalsForStore()
                        && $qItem->getStoreId() != Mage::getSingleton('checkout/session')->getTotalsForStore()) {
                          continue;
                      }

                      $items[] = $qItem;
                      if ($this->_filterNominal($qItem)) {
                          $nominalItems[] = $qItem;
                      } else {
                          $nonNominalItems[] = $qItem;
                      }
                  }
              }
          }

          // Cache calculated lists
          $this->setData('cached_items_all', $items);
          $this->setData('cached_items_nominal', $nominalItems);
          $this->setData('cached_items_nonnominal', $nonNominalItems);

          $this->_nominalOnly = $wasNominal; // Restore original value before we changed it
      }

      $items = $this->getData($key);
      return $items;
  }
  //
  // /**
  //  * Getter for all non-nominal items
  //  *
  //  * @return array
  //  */
  // public function getAllNonNominalItems($storeId = false)
  // {
  //     $this->_nominalOnly = false;
  //     mage::log('before: '.$storeId, null, 'SUBTOTAL.log');
  //     $result = $this->getAllItems($storeId);
  //     $this->_nominalOnly = null;
  //     return $result;
  // }

}
