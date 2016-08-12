<?php

class Horsebrands_Rewrites_Block_Catalog_Navigation extends Mage_Catalog_Block_Navigation {

  public function getNextLayerHtml($parent) {
    $categories = $parent->getChildrenCategories();
    $html = '';

    if(!$categories || count($categories) <= 0) {
      return false;
    }

    foreach ($categories as $category) {
      $category = Mage::getModel('catalog/category')->load($category->getId());

      if(!$category->getIsActive()){
        continue;
      }
      $html .= '<li>';
      $html .= '<a href="'.$this->getCategoryUrl($category).'">'.$this->escapeHtml($category->getName()).'</a>';
      // $html .= '<a href="'.$this->getCategoryUrl($category).'">'.$this->escapeHtml($category->getName()).' ('.$category->getProductCount().')<i class="fa fa-chevron-right"></i></a>';
      $html .= '</li>';
    }

    return $html;
  }
}
