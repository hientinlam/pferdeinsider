<?php
class Brst_Calculate_Block_Adminhtml_Discount_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
         
        // Set some defaults for our grid
        $this->setDefaultSort('id');
        $this->setId('brst_calculate_discount_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }
     
    protected function _getCollectionClass()
    {
        // This is the model we are using for the grid
        return 'brst_calculate/discount_collection';
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
       $this->addColumn('order_id',
            array(
                'header'=> $this->__('OrderID'),
                'index' => 'order_id'
            )
        );
        $this->addColumn('customer_name',
            array(
                'header'=> $this->__('Customer Name'),
                'index' => 'customer_name'
            )
        );
         $this->addColumn('affiliate_name',
            array(
                'header'=> $this->__('Affialiate Name'),
                'index' => 'affiliate_name'
            )
        );
          $this->addColumn('commission',
            array(
                'header'=> $this->__('Commission'),
                'index' => 'commission'
            )
        );
        
              $this->addColumn('attracted_amount',
            array(
                'header'=> $this->__('Attracted Amount'),
                'index' => 'attracted_amount'
            )
        );
                 $this->addColumn('member_amount',
            array(
                'header'=> $this->__('Member Amount'),
                'index' => 'member_amount'
            )
        );
         
        return parent::_prepareColumns();
    }
     
    public function getRowUrl($row)
    {
        // This is where our row data will link to
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}