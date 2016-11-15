<?php

class Horsebrands_Rewrites_Model_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage {

  public function saveOrder() {

    if(!Mage::getSingleton('core/session')->getData('is_multishipping_checkout')) {
      parent::saveOrder();
    }

    $orderIds = array();
    $this->validate();
    $shippingAddress = $this->getQuote()->getShippingAddress();
    $orders = array();

    try {
      // per store
      // foreach ($shippingAddresses as $address) {
        $order = $this->_prepareOrderByStore($shippingAddress, 1);

        if(count($order->getAllItems()) > 0) {
          $orders[] = $order;
        }

        $order = $this->_prepareOrderByStore($shippingAddress, 2);

        if(count($order->getAllItems()) > 0) {
          $orders[] = $order;
        }
        // Mage::dispatchEvent(
        //     'checkout_type_multishipping_create_orders_single',
        //     array('order'=>$order, 'address'=>$address)
        // );
      // }

      // return $orders;

      die('here');
      foreach ($orders as $order) {
        $order->place();
        $order->save();
        if ($order->getCanSendNewEmailFlag()){
            $order->queueNewOrderEmail();
        }
        $orderIds[$order->getId()] = $order->getIncrementId();
      }

      Mage::getSingleton('core/session')->setOrderIds($orderIds);
      Mage::getSingleton('checkout/session')->setLastQuoteId($this->getQuote()->getId());

      $this->getQuote()
          ->setIsActive(false)
          ->save();

      Mage::dispatchEvent('checkout_submit_all_after', array('orders' => $orders, 'quote' => $this->getQuote()));

      return $this;
    } catch (Exception $e) {
        Mage::dispatchEvent('checkout_multishipping_refund_all', array('orders' => $orders));
        throw $e;
    }
  }

  protected function _prepareOrderByStore(Mage_Sales_Model_Quote_Address $address, $storeId)
  {
      $quote = $this->getQuote();
      $quote->unsReservedOrderId();
      $quote->reserveOrderId();
      $quote->collectTotals();

      $convertQuote = Mage::getSingleton('sales/convert_quote');
      $order = $convertQuote->addressToOrder($address);
      $order->setQuote($quote);
      $order->setBillingAddress(
          $convertQuote->addressToOrderAddress($quote->getBillingAddress())
      );

      if ($address->getAddressType() == 'billing') {
          $order->setIsVirtual(1);
      } else {
          $order->setShippingAddress($convertQuote->addressToOrderAddress($address));
      }

      $order->setPayment($convertQuote->paymentToOrderPayment($quote->getPayment()));
      if (Mage::app()->getStore()->roundPrice($address->getGrandTotal()) == 0) {
          $order->getPayment()->setMethod('free');
      }

      $items = Mage::helper('horsebrands_rewrites/checkout')->getItemsByStoreId($address->getAllItems(), $storeId);
      foreach ($items as $item) {
        // $_quoteItem = $item->getQuoteItem();
        // $item->setProductType($_quoteItem->getProductType())
        //     ->setProductOptions(
        //         $_quoteItem->getProduct()->getTypeInstance(true)->getOrderOptions($_quoteItem->getProduct())
        //     );

        $orderItem = $convertQuote->itemToOrderItem($item);
        if ($item->getParentItem()) {
            $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
        }

        $order->addItem($orderItem);
      }

      return $order;
  }

}
