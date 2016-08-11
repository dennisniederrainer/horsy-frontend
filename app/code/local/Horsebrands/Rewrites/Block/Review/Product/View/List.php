<?php

class Horsebrands_Rewrites_Block_Review_Product_View_List extends Mage_Review_Block_Product_View_List {

  public function getRatingVotes() {
    $result = array(
      5 => 0,
      4 => 0,
      3 => 0,
      2 => 0,
      1 => 0,
    );

    $_items = $this->getReviewsCollection()->getItems();
    foreach ($_items as $_review) {
      $_votes = $_review->getRatingVotes();
      if(count($_votes)) {
        $_vote = $_votes->getFirstItem();
        $numStars = intval($_vote->getPercent()/20);
        $result[$numStars]++;
      }
    }

    return $result;
  }
  
}
