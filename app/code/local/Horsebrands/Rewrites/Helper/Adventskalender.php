<?php

class Horsebrands_Rewrites_Helper_Adventskalender extends Mage_Core_Helper_Abstract {

  public function isAdvEnabled($categoryId) {
    $configEnabled = true; //Mage::getStoreConfig('');
    $configCategoryId = 115; //Mage::getStoreConfig('');

    return ($configEnabled && $configCategoryId == $categoryId);
  }

  public function isAdvDoorClosed($position) {
    $date = new DateTime("now", new DateTimeZone('CET') );
    $month = $date->format('m');
    $day = $date->format('d');

    if($month == 12) {
      if($position > $day) {
        return true;
      }
    }

    return false;
  }

  public function getDoorClosedStyle($index) {
    $filename = 'ADV01_'.$index.'.jpg';
  	$path = '/media/adv/'.$filename;

  	return 'style="background-image: url(\''.$path.'\');background-repeat: no-repeat; background-position: center; z-index: 5; min-height: 375px;"';
  }

}
