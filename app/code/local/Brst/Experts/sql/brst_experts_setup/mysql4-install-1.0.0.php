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
    ->newTable($installer->getTable('brst_experts/amount'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 0, array(
        'nullable'  => false,
        ), 'order_id')
     ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
    'nullable'  => false,
    ), 'product_id')
    ->addColumn('expert_name', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
            'nullable'  => false,
            ), 'expert_name')
    ->addColumn('share_ratio', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
            'nullable'  => false,
            ), 'share_ratio')
    ->addColumn('share_type', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
        ), 'share_type')
    ->addColumn('affiliate_name', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
        ), 'affiliate_name')
    ->addColumn('affiliate_ratio', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
        ), 'affiliate_ratio')
    ->addColumn('tax', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
        ), 'tax')
    ->addColumn('gross_price', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
        ), 'gross_price')
    ->addColumn('affiliate_pay', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
        ), 'affiliate_pay')
     ->addColumn('admin_pay', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
        ), 'admin_pay')
     ->addColumn('tax_pay', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
        ), 'tax_pay')
     ->addColumn('getyoupaid', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
        ), 'getyoupaid')
     ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
        ), 'created_at')
     ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 0, array(
        'nullable'  => false,
        ), 'customer_id')
    ->addColumn('affiliate_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 0, array(
        'nullable'  => false,
        ), 'affiliate_id');
$installer->getConnection()->createTable($table);
$installer->endSetup();