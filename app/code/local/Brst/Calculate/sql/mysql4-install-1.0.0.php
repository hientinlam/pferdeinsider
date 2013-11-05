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
    ->newTable($installer->getTable('brst_calculate/discount'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID')
        ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
            'nullable'  => false,
            ), 'order_id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 0, array(
        'nullable'  => false,
        ), 'customer_id')
     ->addColumn('customer_name', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
    'nullable'  => false,
    ), 'customer_name')
     ->addColumn('affiliate_name', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
    'nullable'  => false,
    ), 'affiliate_name')
    ->addColumn('affiliate_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 0, array(
            'nullable'  => false,
            ), 'affiliate_id')
    ->addColumn('commission', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
            'nullable'  => false,
            ), 'commission')
    
    ->addColumn('attracted_amount', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
            'nullable'  => false,
            ), 'attracted_amount')
     ->addColumn('member_amount', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
            'nullable'  => false,
            ), 'member_amount')
     ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
            'nullable'  => false,
            ), 'created_at');
$installer->getConnection()->createTable($table);
 
$installer->endSetup();