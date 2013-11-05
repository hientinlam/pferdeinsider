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
        $this->addColumn('id',
            array(
                'header'=> $this->__('ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'id'
            )
        );
        
        /*$this->addColumn('expert_id',
            array(
                'header'=> $this->__('ExpertID'),
                'index' => 'expert_id'
            )
        );*/
        
        $this->addColumn('temp_name',
            array(
                'header'=> $this->__('Temp Name'),
                'index' => 'temp_name'
            )
        );
        
        $this->addColumn('expert_name',
            array(
                'header'=> $this->__('Expert Name'),
                'index' => 'expert_name'
            )
        );
        
        $this->addColumn('temp_orders',
            array(
                'header'=> $this->__('Temp Orders'),
                'index' => 'temp_orders'
            )
        );
        
        $this->addColumn('no_of_orders',
            array(
                'header'=> $this->__('Number of orders'),
                'index' => 'no_of_orders'
            )
        );
        
        $this->addColumn('temp_amount',
            array(
                'header'=> $this->__('Temp Amount'),
                'index' => 'temp_amount'
            )
        );
        
        $this->addColumn('total_amount',
            array(
                'header'=> $this->__('Total Amount'),
                'index' => 'total_amount'
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
                'width' => '100px',
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
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $qry="select * from brst_experts_orders where created_at > '$from_time' & created_at < '$to_time'";
        $data = $connection->fetchAll($qry);
        //echo "<pre>";print_r($rows);die('here');
        //$collect = Mage::getModel('brst_member/discount')->getCollection();
        //$from = '2013-04-05 04:54:18';
        //$to = '2013-04-09 04:54:18';
        //$collect->addfieldtofilter('created_at', array('from'  => $from,'to' => $to,));
        //$data = $collect->getData();
        
        foreach($data as $key => $item)
        {
            $member[] = $data[$key]['member_name'];
            $price[] = $data[$key]['item_price'];
        }
        
        $alldata=array();
        foreach($member as $datakey=>$data1)
        {
            if(!array_key_exists($data1,$alldata))
            {
                $alldata[$data1]=$price[$datakey];
            }
            else
            {
                $prev_val=$alldata[$data1];
                $alldata[$data1]=$prev_val+$price[$datakey];
            }
        }
        foreach($alldata as $key => $value) {
                $model = Mage::getModel('brst_experts/amount')->getCollection()->getData();
                $ifexist=0;
                $adminid='';
                $no_order='';
                $totalamount='';
                $admin_amount='';
                foreach($model as $colldta)
                {
                    if($colldta['temp_name']==$key)
                    {
                        $ifexist=1;
                        $adminid=$colldta['id'];
                        $totalamount=$colldta['temp_amount'];
                        $admin_amount=$colldta['admin_amount'];
                        $no_order=$colldta['temp_orders'];
                        break;
                    }
                }
                if($ifexist==1)
                {
                    $model1 = Mage::getModel('brst_experts/amount')->load($adminid);
                    $model1->settemp_name($key);
                    //$model->settemp_orders($no_order + 1);
                    $model1->settemp_amount($value);
                    //$model->setadmin_amount($admin_amount + $admin_price);
                    $model1->save();
                }
                /*else
                {
                    $model = Mage::getModel('brst_experts/amount');
                    $model->settemp_name($expertname);
                    $model->settemp_orders(1);
                    $model->settemp_amount($expert_price);
                    $model->setadmin_amount($admin_price);
                    $model->save();
                }*/
        }
    }
    
    public function getRowUrl($row)
    {
        // This is where our row data will link to
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}