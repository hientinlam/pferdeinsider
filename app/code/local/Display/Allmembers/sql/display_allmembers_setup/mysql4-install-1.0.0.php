<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
 
/**
 * Create table 'foo_bar_baz'
 */
$table = $installer->getConnection()
    // The following call to getTable('foo_bar/baz') will lookup the resource for foo_bar (foo_bar_mysql4), and look
    // for a corresponding entity called baz. The table name in the XML is foo_bar_baz, so ths is what is created.
    ->newTable($installer->getTable('display_allmembers/allmembers'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
        ), 'customer_id')
    ->addColumn('customer_email', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
            'nullable'  => false,
            ), 'customer_email')
    ->addColumn('member_id', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
            'nullable'  => false,
            ), 'member_id')
    ->addColumn('createddate', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
            'nullable'  => false,
            ), 'createddate')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
            'nullable'  => false,
            ), 'status')
  ;
$installer->getConnection()->createTable($table);
 
$installer->endSetup();
