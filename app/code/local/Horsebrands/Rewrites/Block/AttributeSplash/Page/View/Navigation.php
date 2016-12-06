<?php

class Horsebrands_Rewrites_Block_AttributeSplash_Page_View_Navigation extends Mage_Catalog_Block_Navigation {

  public function getSplashGroup() {
		return Mage::registry('splash_group');
	}

  public function getSplashPage() {
    return Mage::registry('splash_page');
  }

	public function getSplashPages() {
    if($_splashgroup = $this->getSplashGroup()) {
      return $this->_splashPages = $_splashgroup->getSplashPages()->addOrderBySortOrder();
    }

    return null;
	}
}
