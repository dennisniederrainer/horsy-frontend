<?php
class Horsebrands_Rewrites_Helper_Data extends Mage_Core_Helper_Abstract {

  public function isProductInCategory($product, $categoryId) {
    // $currentcategory = mage::registry('current_category');

    // try {
    //   if($currentcategory->getId()) {
    //     return ($currentcategory->getId() == $categoryId);
    //   } else {
    //     throw new Exception("no current category");
    //   }
    // } catch (Exception $e) {
      $categoryIds = $product->getCategoryIds();
      return in_array($categoryId, $categoryIds);
    // }

    return false;
  }

  public function getShortProductname($productname) {
    if(strlen($productname) > 20) {
      $html = '<span class="productname-short">';
        $html .= substr($productname, 0, 18).'...';
      $html .= '</span>';
      $html .= '<span class="productname-long hide">';
        $html .= $productname;
      $html .= '</span>';

      return $html;
    } else {
      return $productname;
    }
  }
}
