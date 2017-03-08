<?php

class Horsebrands_Rewrites_Model_SalesRule_Validator extends Mage_SalesRule_Model_Validator {

  private function _hasChildInCart($product){
    $quote = Mage::getSingleton('checkout/session')->getQuote();
    $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
    $childrenIds = $this->_getChildrenIds($childProducts);

    foreach($quote->getAllItems() as $item){
        if(in_array($item->getProductId(),$childrenIds)){
            $registeredItem = Mage::registry('rule_config_product_'.$product->getId());
            if($registeredItem != null && $registeredItem->getId() != $item->getId()){
                Mage::unregister('rule_config_product_'.$product->getId());
            }
            if($registeredItem == null){
                Mage::register('rule_config_product_'.$product->getId(),$item);
            }
            return true;
        }
    }
    return false;
  }

  private function _getChildrenIds($childProducts){
      $childrenIds = array();
      foreach($childProducts as $child){
          $childrenIds[] = $child->getId();
      }

      return $childrenIds;
  }
}
