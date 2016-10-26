<?php

class Horsebrands_Rewrites_Block_Purchase_Supplier_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm() {
      // prepare form for file upload
      $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post', 'enctype'=>'multipart/form-data'));
      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
    }

}
