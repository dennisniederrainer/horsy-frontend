<?php

class Horsebrands_Rewrites_Block_Purchase_Supplier_Edit_Tabs_Info extends MDN_Purchase_Block_Supplier_Edit_Tabs_Info {

  public function __construct() {
      parent::__construct();

      $this->setTemplate('horsebrands/purchase/supplier/edit/tab/info.phtml');
  }
}
