<?php
class Horsebrands_Rewrites_Helper_Checkout extends Mage_Core_Helper_Abstract {

  public function getSavedAmount() {
    $savedAmount = 0;
    $items = Mage::getModel('checkout/session')->getQuote()->getItemsCollection();

    foreach ($items as $item) {
      if( ($diff = $item->getPrice() - $item->getFinalPrice()) > 0 ) {
        $savedAmount += $diff;
      }
    }

    if($diff > 0) {
      return $diff;
    }

    return false;
  }

  // deprecated
  public function getQuoteItemsByStoreId($storeId = -1) {
      $items = array();

      foreach (Mage::getModel('checkout/session')->getQuote()->getItemsCollection() as $item) {
          // quote->getAllVisibleItems with extra store "filter"
          if (!$item->isDeleted() && !$item->getParentItemId()) {
            if($storeId > 0 && $item->getStoreId() == $storeId) {
              $items[] =  $item;
            } elseif($storeId == -1) {
              $items[] =  $item;
            }
          }
      }

      return $items;
  }

  // deprecated
  public function getItemsByStoreId($items, $storeId = -1) {
      $storeitems = array();

      foreach ($items as $item) {
          // quote->getAllVisibleItems with extra store "filter"
          try {
            if (!$item->isDeleted() && !$item->getParentItemId()) {
              if($storeId > 0 && $item->getStoreId() == $storeId) {
                $storeitems[] =  $item;
              } elseif($storeId == -1) {
                $storeitems[] =  $item;
              }
            }
          } catch (Exception $e) {
            mage::log($e->getMessage(), null, 'CHECKOUThelper.log');
          }
      }

      return $storeitems;
  }

  public function getItemsExceptStoreId($items, $storeId = -1) {
    $storeitems = array();

    foreach ($items as $item) {
      // quote->getAllVisibleItems with extra store "filter"
      try {
        if (!$item->isDeleted() && !$item->getParentItemId()) {
          if($item->getStoreId() != $storeId) {
            $storeitems[] =  $item;
          }
        }
      } catch (Exception $e) {
        mage::log($e->getMessage(), null, 'CHECKOUThelper.log');
      }
    }

    return $storeitems;
  }

  protected $shippingIntervals = array(
    "2-4 Tage" => array(2,4),
    "5-8 Tage" => array(5,8),
    "2-3 Wochen nach Aktionsende" => array(14,21),
    "2-3 Wochen" => array(14,21)
  );

  public function getEstimatedShippingText($items) {

    if($items && count($items)) {
      $interval = $this->getLongestShippingInterval($items);
      $latestPlusOne = false;

      $earliest = new Zend_Date(Mage::getModel('core/date')->timestamp());
      $earliest->addDay($interval[0]);
      $latest = new Zend_Date(Mage::getModel('core/date')->timestamp());
      $latest->addDay($interval[1]);

      if($earliest->get(Zend_Date::WEEKDAY_DIGIT) == 0 || $earliest->get(Zend_Date::WEEKDAY_DIGIT) == 6) {
        $earliest->addDay('1');
        $latestPlusOne = true;
      }

      if($latest->get(Zend_Date::WEEKDAY_DIGIT) == 0
        || $earliest->get(Zend_Date::WEEKDAY_DIGIT) == 6 || $latestPlusOne) {
        $latest->addDay('1');
      }

      $earliestWeekday = Mage::getModel('core/date')->date('l', $earliest);
      $latestWeekday = Mage::getModel('core/date')->date('l', $latest);

      $earliest = Mage::getModel('core/date')->date('d.m.Y', $earliest);
      $latest = Mage::getModel('core/date')->date('d.m.Y', $latest);

      $earliestText = $this->__($earliestWeekday) .', '. $earliest;
      $latestText = $this->__($latestWeekday) .', '. $latest;

      return $earliestText . ' - ' . $latestText;
    }

    return null;
  }

  protected function getLongestShippingInterval($items) {
    $highestIndex = 0;

    foreach ($items as $item) {
      if(!$item->getProduct()->getDeliveryTime()) {
        continue;
      }
      $deliverytime = $item->getProduct()->getAttributeText('delivery_time');

      $index = array_search($deliverytime, array_keys($this->shippingIntervals));
      if($index > $highestIndex) {
        $highestIndex = $index;
      }
    }

    $key = array_keys($this->shippingIntervals)[$highestIndex];
    return $this->shippingIntervals[$key];
  }

  public function updateAddress($address, $data) {
    $fields = array('prefix','firstname','lastname','postcode','city','country_id','telephone','company', 'street');

    foreach ($fields as $field) {
      if(isset($data[$field])) {
        $address->setData($field, $data[$field]);
      }
    }

    return $address;
  }

  public function duplicateCustomerAddress($address) {
    $duplicate = Mage::getModel('customer/address')
      ->setCustomerId($address->getCustomer()->getId())
      ->setData($address->getData())
      ->setId(null)
      ->setSaveInAddressBook('1')
      ->setIsDuplicate(true)
      ->save();

    return $duplicate->getId();
  }

  public function removeInactiveDealsItems($outputMessage = true) {
    $cartHelper = Mage::helper('checkout/cart');
    $storeId = Mage::app()->getStore('deals_de')->getId();
    $dealsItems = $this->getItemsByStoreId($cartHelper->getCart()->getItems(), $storeId);
    $hasInactiveItems = false;

    foreach ($dealsItems as $item) {
      if(!mage::helper('aktionen')->hasProductCurrentFlashsale($item->getProduct())) {
        $cartHelper->getCart()->removeItem($item->getId());
        $hasInactiveItems = true;

        if($outputMessage) {
          Mage::getSingleton('core/session')->addNotice(
            $this->__('We are sorry, but the campaign of %s has expired. It was removed from your Shopping Cart',
            $item->getProduct()->getName())
          );
        }
      }
    }

    if($hasInactiveItems) {
      $cartHelper->getCart()->save();
    }

    return $hasInactiveItems;
  }

  public function isDuplicateEmailAddress($email) {
    $customer = Mage::getModel('customer/customer')
                  ->setWebsiteId(Mage::app()->getWebsite()->getId())
                  ->loadByEmail($email);

    if($customer->getId()) {
      return true;
    }

    return false;
  }
}
