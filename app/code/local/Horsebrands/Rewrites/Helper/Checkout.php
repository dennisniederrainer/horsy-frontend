<?php
class Horsebrands_Rewrites_Helper_Checkout extends Mage_Core_Helper_Abstract {

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

  public function getItemsByStoreId($items, $storeId = -1) {
      $storeitems = array();

      foreach ($items as $item) {
          // quote->getAllVisibleItems with extra store "filter"
          if (!$item->isDeleted() && !$item->getParentItemId()) {
            if($storeId > 0 && $item->getStoreId() == $storeId) {
              $storeitems[] =  $item;
            } elseif($storeId == -1) {
              $storeitems[] =  $item;
            }
          }
      }

      return $storeitems;
  }

  public function getItemsExceptStoreId($items, $storeId = -1) {
    $storeitems = array();

    foreach ($items as $item) {
      // quote->getAllVisibleItems with extra store "filter"
      if (!$item->isDeleted() && !$item->getParentItemId()) {
        if($item->getStoreId() != $storeId) {
          $storeitems[] =  $item;
        }
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

      if($earliest->get(Zend_Date::WEEKDAY_DIGIT) == 0) {
        $earliest->addDay('1');
        $latestPlusOne = true;
      }

      if($latest->get(Zend_Date::WEEKDAY_DIGIT) == 0 || $latestPlusOne) {
        $latest->addDay('1');
      }

      $earliest = Mage::getModel('core/date')->date('l, d.m.Y', $earliest);
      $latest = Mage::getModel('core/date')->date('l, d.m.Y', $latest);

      $earliestText = Mage::helper('core')->formatDate($earliest, 'full', false);
      $latestText = Mage::helper('core')->formatDate($latest, 'full', false);

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

  // protected $shippingIntervals = array(
  //   "2-4 Tage" => array(2,4),
  //   "5-8 Tage" => array(5,8),
  //   "2-3 Wochen nach Aktionsende" => array(14,21)
  // );
  //
  // public function getEstimatedShippingText($storeId) {
  //   $items = $this->getQuoteItemsByStoreId($storeId);
  //
  //   if($items && count($items)) {
  //     $interval = $this->getLongestShippingInterval($storeId, $items);
  //
  //     $today = Mage::getModel('core/date')->date('l, d.m.Y');
  //     $earliest = strtotime(date("Y-m-d", strtotime($today)) . " +".$interval[0]." day");
  //     $latest = strtotime(date("Y-m-d", strtotime($today)) . " +".$interval[1]." day");
  //
  //     $earliestText = Mage::getModel('core/date')->date('l, d.m.Y', $earliest);
  //     $latestText = Mage::getModel('core/date')->date('l, d.m.Y', $latest);
  //
  //     return $earliestText . ' - ' . $latestText;
  //   }
  //
  //   return null;
  // }
  //
  // protected function getLongestShippingInterval($storeId, $items) {
  //   $highestIndex = 0;
  //
  //   foreach ($items as $item) {
  //     if($item->getStoreId() != $storeId || !$item->getProduct()->getDeliveryTime()) {
  //       continue;
  //     }
  //     $deliverytime = $item->getProduct()->getAttributeText('delivery_time');
  //
  //     $index = array_search($deliverytime, array_keys($this->shippingIntervals));
  //     if($index > $highestIndex) {
  //       $highestIndex = $index;
  //     }
  //   }
  //
  //   $key = array_keys($this->shippingIntervals)[$highestIndex];
  //   return $this->shippingIntervals[$key];
  // }
}
