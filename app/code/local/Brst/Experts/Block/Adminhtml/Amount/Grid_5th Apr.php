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
    
    public function getRowUrl($row)
    {
        // This is where our row data will link to
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}