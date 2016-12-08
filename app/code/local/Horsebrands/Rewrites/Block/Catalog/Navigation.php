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
      $currentClass = ($this->getCurrentCategory()->getId() == $category->getId() ? ' class="current-category" onclick="return false;"' : '');

      if(!$category->getIsActive()){
        continue;
      }
      $html .= '<li>';
      $html .= '<a href="'.$this->getCategoryUrl($category).'"'.$currentClass.'>'.$this->escapeHtml($category->getName()).'</a>';
      $html .= '</li>';
    }

    return $html;
  }

  public function getRootChildCategories() {
    $path = $this->getCurrentCategoryPath();
    $rootCategoryId = $path[count($path)-1];
    $category = Mage::getModel('catalog/category')->load($rootCategoryId);
    return $category->getChildrenCategories();
  }
}
