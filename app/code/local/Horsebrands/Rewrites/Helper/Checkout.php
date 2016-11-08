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

  protected $shippingIntervals = array(
    "2-4 Tage" => array(2,4),
    "5-8 Tage" => array(5,8),
    "2-3 Wochen nach Aktionsende" => array(14,21)
  );

  public function getEstimatedShippingText($storeId) {
    $items = $this->getQuoteItemsByStoreId($storeId);

    if($items && count($items)) {
      $interval = $this->getLongestShippingInterval($storeId, $items);

      $today = Mage::getModel('core/date')->date('l, d.m.Y');
      $earliest = strtotime(date("Y-m-d", strtotime($today)) . " +".$interval[0]." day");
      $latest = strtotime(date("Y-m-d", strtotime($today)) . " +".$interval[1]." day");

      $earliestText = Mage::getModel('core/date')->date('l, d.m.Y', $earliest);
      $latestText = Mage::getModel('core/date')->date('l, d.m.Y', $latest);

      return $earliestText . ' - ' . $latestText;
    }

    return null;
  }

  protected function getLongestShippingInterval($storeId, $items) {
    $highestIndex = 0;

    foreach ($items as $item) {
      if($item->getStoreId() != $storeId || !$item->getProduct()->getDeliveryTime()) {
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
