<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()
          ->addColumn($this->getTable('purchase_supplier'),'logo_image', array(
              'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
              'comment'   => 'cell holds the manufacturer logos filename'
              ));

$installer->endSetup();

?>
