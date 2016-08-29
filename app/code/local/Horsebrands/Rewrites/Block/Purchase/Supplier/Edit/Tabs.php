<?php

class Horsebrands_Rewrites_Block_Purchase_Supplier_Edit_Tabs extends MDN_Purchase_Block_Supplier_Edit_Tabs {

    protected function _beforeToHtml() {
      // parent::_beforeToHtml();

      $this->addTab('tab_info_zwei', array(
          'label'     => Mage::helper('purchase')->__('Pimmel'),
          'content'   => $this->getLayout()->createBlock('horsebrands_rewrites/purchase_supplier_edit_tabs_info')->toHtml(),
      ));
      // $this->getTab('tab_info')->setContent($this->getLayout()->createBlock('horsebrands_rewrites/purchase_supplier_edit_tabs_info')->toHtml(),);
      // , array(
      //     'label'     => Mage::helper('purchase')->__('Summary'),
      //     'content'   => $this->getLayout()->createBlock('Purchase/Supplier_Edit_Tabs_Info')->toHtml(),
      // ));

      return $this;
    }

}
