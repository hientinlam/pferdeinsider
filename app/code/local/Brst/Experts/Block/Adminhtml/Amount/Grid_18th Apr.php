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
        foreach($mod as $ks => $name)
        {
            $expert[$name['expert_name']] = $name['expert_name'];
        }
        $this->addColumn('id',
            array(
                'header'=> $this->__('ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'id'
            )
        );
        $this->addColumn('order_id',
            array(
                'header'=> $this->__('Order ID'),
                'index' => 'order_id',
            )
        );
        $this->addColumn('product_id',
            array(
                'header'=> $this->__('Product ID'),
                'index' => 'product_id',
             )
        );
        
        /*$this->addColumn('temp_name',
            array(
                'header'=> $this->__('Expert Name'),
                'index' => 'temp_name'
            )
        );
        
        $this->addColumn('temp_orders',
            array(
                'header'=> $this->__('Number of orders'),
                'index' => 'temp_orders'
            )
        );
        
        $this->addColumn('temp_amount',
            array(
                'header'=> $this->__('Total Amount'),
                'index' => 'temp_amount'
            )
        );*/
        
        $this->addColumn('expert_name',
            array(
                'header'=> $this->__('Expert Name'),
                'index' => 'expert_name',
                'type'  => 'options',
                'options' => $expert,
            )
        );
      $this->addColumn('share_ratio',
            array(
                'header'=> $this->__('Share Ratio'),
                'index' => 'share_ratio',
             )
        );
      $this->addColumn('share_type',
            array(
                'header'=> $this->__('Share Type'),
                'index' => 'share_type',
             )
        );
      $this->addColumn('affiliate_name',
            array(
                'header'=> $this->__('Affiliate Name'),
                'index' => 'affiliate_name',
             )
        );
      $this->addColumn('affiliate_ratio',
            array(
                'header'=> $this->__('Affiliate Ratio'),
                'index' => 'affiliate_ratio',
             )
        );
       $this->addColumn('tax',
            array(
                'header'=> $this->__('Tax'),
                'index' => 'tax',
             )
        );
        $this->addColumn('gross_price',
            array(
                'header'=> $this->__('Gross Price'),
                'index' => 'gross_price',
             )
        );
          
        
        
        $this->addColumn('affiliate_pay',
            array(
                'header'=> $this->__('Affiliate Pay'),
                'index' => 'affiliate_pay'
            )
        );
        
        $this->addColumn('admin_pay',
            array(
                'header'=> $this->__('Admin Pay'),
                'index' => 'admin_pay'
            )
        );
        $this->addColumn('tax_pay',
            array(
                'header'=> $this->__('Tax Pay'),
                'index' => 'tax_pay'
            )
        );
         $this->addColumn('getyoupaid',
            array(
                'header'=> $this->__('Get You Paid'),
                'index' => 'getyoupaid'
            )
        );
        /*$this->addColumn('admin_amount',
            array(
                'header'=> $this->__('Admin Amount'),
                'index' => 'admin_amount'
            )
        );*/
        
        $this->addColumn('created_at',
            array(
                'header'=> $this->__('Purchased On'),
                'index' => 'created_at',
                'type' => 'datetime',
                //'width' => '200px',
                'filter_condition_callback' => array($this, '_filterDateCondition'),
            )
        );
        
        $this->addExportType('*/*/exportCsv', $this->__('CSV'));
        $this->addExportType('*/*/exportExcel', $this->__('Excel XML'));
        
        return parent::_prepareColumns();
    }
    
    protected function _filterDateCondition($collection, $column)
    {
        $names = $column->getFilter()->getValue();
        $from = $names['orig_from'];
        $to = $names['orig_to'];
        $from_time = strtotime($from);
        $to_time = strtotime($to);
        $curr_date = date('Y-m-d');
        $current_date = strtotime($curr_date);
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $qry="select * from brst_experts_orders where created_at >= '$from_time' AND created_at <= '$to_time'";
        if($from_time == '' && $to_time == '')
        {
            $qry="select * from brst_experts_orders";
        }
        else if($to_time == '')
        {
            $qry="select * from brst_experts_orders where created_at >= '$from_time' AND created_at <= '$current_date'";
        }
        $data = $connection->fetchAll($qry);
        
        foreach($data as $key => $item)
        {
            $member[] = $data[$key]['member_name'];
            $price[] = $data[$key]['item_price'];
        }
        $alldata=array();
        $countorder = array();
        $count = 1;
        foreach($member as $datakey=>$data1)
        {
            if(!array_key_exists($data1,$alldata))
            {
                $alldata[$data1]=$price[$datakey];
                $countorder[$data1] = $count;
            }
            else
            {
                $prev_val=$alldata[$data1];
                $prev_order=$countorder[$data1];
                $alldata[$data1]=$prev_val+$price[$datakey];
                $countorder[$data1] = $prev_order + $count;
            }
        }
        $model = Mage::getModel('brst_experts/amount')->getCollection()->getData();
        foreach($model as $k => $id)
        {
            $id = $model[$k]['id'];
            $model1 = Mage::getModel('brst_experts/amount')->load($id);
            $model1->delete();
        }
        foreach($alldata as $key => $value) {
            $order = $countorder[$key];
            $model2 = Mage::getModel('brst_experts/amount');
            $model2->setexpert_name($key);
            $model2->setno_of_orders($order);
            $model2->settotal_amount($value);
            $model2->save();
        }
    }
    
    public function getRowUrl($row)
    {
        // This is where our row data will link to
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}