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
    ->newTable($installer->getTable('brst_member/payment'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID')
    ->addColumn('total_earned', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
    'nullable'  => false,
    ), 'total_earned')
    ->addColumn('total_paid', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
    'nullable'  => false,
    ), 'total_paid')
    ->addColumn('pending_amount', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
    'nullable'  => false,
    ), 'pending_amount')
    ->addColumn('inprogress', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
    'nullable'  => false,
    ), 'inprogress')
    ->addColumn('last_invoice_date', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
            'nullable'  => false,
            ), 'last_invoice_date')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
    'nullable'  => false,
    ), 'status')
    ->addColumn('send_invoice', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
    'nullable'  => false,
    ), 'send_invoice');
$installer->getConnection()->createTable($table);

$installer->endSetup();