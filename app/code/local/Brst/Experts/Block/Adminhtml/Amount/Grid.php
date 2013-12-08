<?php
class Brst_Experts_Block_Adminhtml_Amount_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        // Set some defaults for our grid
        $this->setDefaultSort('id');
        $this->setId('brst_experts_amount_grid');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _getCollectionClass()
    {
        // This is the model we are using for the grid
        return 'brst_experts/amount_collection';
    }

    protected function _prepareCollection()
    {
        // Get and set our collection for the grid
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        // Add the columns that should appear in the grid
        $mod = Mage::getModel('brst_experts/amount')->getCollection()->getData();
        foreach ($mod as $ks => $name)
        {
            $expert[$name['expert_name']] = $name['expert_name'];
            $affiliate[$name['affiliate_name']] = $name['affiliate_name'];
        }
        $this->addColumn('id',
            array(
                'header' => $this->__('ID'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'id'
            )
        );
        $this->addColumn('order_id',
            array(
                'header' => $this->__('Order ID'),
                'index' => 'order_id',
            )
        );
        $this->addColumn('product_id',
            array(
                'header' => $this->__('Product Name'),
                'index' => 'product_id',
            )
        );

        $this->addColumn('expert_name',
            array(
                'header' => $this->__('Expert Name'),
                'index' => 'expert_name',
                'type' => 'options',
                'options' => $expert,
            )
        );

        $this->addColumn('raw_price',
            array(
                'header' => $this->__('Raw Sales'),
                'index' => 'raw_price',
            )
        );
        $this->addColumn('gross_price',
            array(
                'header' => $this->__('Gross Sales'),
                'index' => 'gross_price',
            )
        );

        $this->addColumn('affiliate_name',
            array(
                'header' => $this->__('Affiliate Name'),
                'index' => 'affiliate_name',
                'type' => 'options',
                'options' => $affiliate,
            )
        );
        $this->addColumn('share_type',
            array(
                'header' => $this->__('Earning Type'),
                'index' => 'share_type',
            )
        );
        $this->addColumn('affiliate_ratio',
            array(
                'header' => $this->__('Affiliate Ratio'),
                'index' => 'affiliate_ratio',
            )
        );
        $this->addColumn('affiliate_pay',
            array(
                'header' => $this->__('Paid to Affiliate'),
                'index' => 'affiliate_pay'
            )
        );

        $this->addColumn('share_ratio',
            array(
                'header' => $this->__('Pferde Ratio'),
                'index' => 'share_ratio',
            )
        );
        $this->addColumn('admin_pay',
            array(
                'header' => $this->__('Paid to Pferde'),
                'index' => 'admin_pay'
            )
        );

//        $this->addColumn('tax',
//            array(
//                'header' => $this->__('Tax Rate'),
//                'index' => 'tax',
//            )
//        );
//        $this->addColumn('tax_pay',
//            array(
//                'header' => $this->__('Tax Amount'),
//                'index' => 'tax_pay'
//            )
//        );

        $this->addColumn('getyoupaid',
            array(
                'header' => $this->__('Paid to Expert'),
                'index' => 'getyoupaid'
            )
        );

        $this->addColumn('created_at',
            array(
                'header' => $this->__('Purchased On'),
                'index' => 'created_at',
                'type' => 'datetime'
            )
        );

        $this->addExportType('*/*/exportCsv', $this->__('CSV'));
        $this->addExportType('*/*/exportExcel', $this->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        // This is where our row data will link to
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}