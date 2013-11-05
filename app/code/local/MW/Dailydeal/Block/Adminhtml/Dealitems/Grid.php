<?php
class MW_Dailydeal_Block_Adminhtml_Dealitems_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('dealitemsGrid');
		$this->setDefaultSort('dailydeal_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('dailydeal/dailydeal')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	protected function _prepareColumns()
  	{
      $this->addColumn('dailydeal_id', array(
          'header'    => Mage::helper('dailydeal')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'dailydeal_id',
      ));

      $this->addColumn('cur_product', array(
          'header'    => Mage::helper('dailydeal')->__('Product Name'),
          'align'     =>'left',
          'index'     => 'cur_product',
      ));

      $this->addColumn('product_sku', array(
          'header'    => Mage::helper('dailydeal')->__('SKU'),
          'align'     =>'left',
          'index'     => 'product_sku',
      ));
        $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
//$displayFormat = $outputFormat->toString('Y-m-d H:i:s');        
$this->addColumn('start_date_time', array(
          'header'    => Mage::helper('dailydeal')->__('Active From'),
      		'width'		=>	'130px',
      	//'type'	=> 'datetime',
    	 // 'format'			 => $outputFormat,
          'align'     =>'left',
          'index'     => 'start_date_time',
			
      ));
      $this->addColumn('end_date_time', array(
          'header'    => Mage::helper('dailydeal')->__('Active To'),
      		'width'		=>	'130px',
//     		'type'	=> 'datetime',
//      		'format'			 => $outputFormat,
          'align'     =>'left',
          'index'     => 'end_date_time',
      ));
      $store = $this->_getStore();//var_dump($store->getBaseCurrency()->getCode());
	  $this->addColumn('dailydeal_price', array(
          'header'    => Mage::helper('dailydeal')->__('Deal Price'),
	  	  'type'  => 'price',
          'align'     =>'left',
	      'currency_code' => $store->getBaseCurrency()->getCode(),	 
          'index'	=> 'dailydeal_price'
      ));
      $this->addColumn('deal_qty', array(
			'header'    => Mage::helper('dailydeal')->__('Deal Qty'),
			'width'     => '50px',
			'index'     => 'deal_qty',
      ));
       $this->addColumn('sold_qty', array(
			'header'    => Mage::helper('dailydeal')->__('Sold Qty'),
			'width'     => '50px',
			'index'     => 'sold_qty',
      ));
      $this->addColumn('featured', array(
      		'header'	=>	Mage::helper('dailydeal')->__('Featured'),
      		'index'		=>	'featured',
      		'type'		=> 	'options',
      		'options'	=>	array(
      			1 => 'Yes',
              	0 => 'No',
      		)
      ));
	  /*$this->addColumn('impression', array(
          'header'    => Mage::helper('dailydeal')->__('Impression'),
          'align'     =>'left',
          'index'     => 'title',
      ));*/

      $this->addColumn('status', array(
          'header'    => Mage::helper('dailydeal')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('dailydeal')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('dailydeal')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
      $this->addExportType('*/*/exportCsv', Mage::helper('dailydeal')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('dailydeal')->__('XML'));
	  
      return parent::_prepareColumns();
  	}
  protected function _prepareMassaction()
    {
        $this->setMassactionIdField('dailydeal_id');
        $this->getMassactionBlock()->setFormFieldName('dailydeal');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('dailydeal')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('dailydeal')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('dailydeal/status')->getOptionArray();
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('dailydeal')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('dailydeal')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
public function getGridUrl()
	{
		// This will invoke a call to the controller object GroupsaleController.php to
		// function name gridProductAction
		$ret = $this->getUrl('dailydeal/adminhtml_dealitems/index',
		array(
			'index' => $this->getIndex(),
			'_current'=>true,
		));
		return $ret;
	}
}