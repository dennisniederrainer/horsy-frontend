<?php

class Horsebrands_Rewrites_Block_Purchase_Supplier_Edit_Tabs extends MDN_Purchase_Block_Supplier_Edit_Tabs {

    protected function _beforeToHtml() {
      $product = $this->getProduct();

        $this->addTab('tab_info', array(
            'label'     => Mage::helper('purchase')->__('Summary'),
            'content'   => $this->getLayout()->createBlock('horsebrands_rewrites/purchase_supplier_edit_tabs_info')->toHtml(),
        ));

        $this->addTab('tab_misc', array(
            'label'     => Mage::helper('purchase')->__('Miscellaneous'),
            'content'   => $this->getLayout()->createBlock('Purchase/Supplier_Edit_Tabs_Misc')->toHtml(),
        ));

        $this->addTab('tab_products', array(
            'label'     => Mage::helper('purchase')->__('Products'),
            'content'   => $this->getLayout()->createBlock('Purchase/Supplier_Edit_Tabs_Products')->setSupplierId($this->getSupplier()->getId())->toHtml(),
        ));

        $this->addTab('tab_orders', array(
            'label'     => Mage::helper('purchase')->__('Orders'),
            'content'   => $this->getLayout()->createBlock('Purchase/Supplier_Edit_Tabs_Orders')->setSupplierId($this->getSupplier()->getId())->toHtml(),
        ));

        $this->addTab('tab_contacts', array(
            'label'     => Mage::helper('purchase')->__('Contacts'),
            'content'   => $this->getLayout()->createBlock('Purchase/Contact_SubGrid')
            				->setEntityType('supplier')
            				->setEntityId($this->getSupplier()->getId())
            				->setTemplate('Purchase/Contact/SubGrid.phtml')
            				->toHtml(),
        ));

    	$TaskCount = 0;
    	$gridBlock = $this->getLayout()
    				->createBlock('Organizer/Task_Grid')
    				->setEntityType('supplier')
    				->setEntityId($this->getSupplier()->getId())
    				->setShowTarget(false)
    				->setShowEntity(false)
    				->setTemplate('Organizer/Task/List.phtml');

		$content = $gridBlock->toHtml();

		$TaskCount = $gridBlock->getCollection()->getSize();
        $this->addTab('supplier_organizer', array(
            'label'     => Mage::helper('Organizer')->__('Organizer').' ('.$TaskCount.')',
            'title'     => Mage::helper('Organizer')->__('Organizer').' ('.$TaskCount.')',
            'content'   => $content,
        ));

        //set active tab
        $defaultTab = $this->getRequest()->getParam('tab');
        if ($defaultTab == null)
        	$defaultTab = 'tab_info';
        $this->setActiveTab($defaultTab);

        return Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml();
    }

}
