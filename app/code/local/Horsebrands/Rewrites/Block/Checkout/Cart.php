<?php

class Horsebrands_Rewrites_Block_Checkout_Cart extends Mage_Checkout_Block_Cart {

  public function getItemsByStoreId($storeId) {
      $items = array();

      foreach ($this->getQuote()->getItemsCollection() as $item) {
          // quote->getAllVisibleItems with extra store "filter"
          if (!$item->isDeleted() && !$item->getParentItemId() && $item->getStoreId() == $storeId) {
              $items[] =  $item;
          }
      }
      return $items;
  }

  protected $shippingIntervals = array(
    "2-4 Tage" => array(2,4),
    "5-8 Tage" => array(5,8),
    "2-3 Wochen nach Aktionsende" => array(14,21)
  );

  public function getEstimatedShippingText($storeId) {
    $interval = $this->getLongestShippingInterval($storeId);

    $today = Mage::getModel('core/date')->date('l, d.m.Y');
    $earliest = strtotime(date("Y-m-d", strtotime($today)) . " +".$interval[0]." day");
    $latest = strtotime(date("Y-m-d", strtotime($today)) . " +".$interval[1]." day");

    $earliestText = Mage::getModel('core/date')->date('l, d.m.Y', $earliest);
    $latestText = Mage::getModel('core/date')->date('l, d.m.Y', $latest);

    return $earliestText . ' - ' . $latestText;
  }

  protected function getLongestShippingInterval($storeId) {
    $items = $this->getItems();
    $highestIndex = 0;

    foreach ($items as $item) {
      if($item->getStoreId() != $storeId) {
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

}
