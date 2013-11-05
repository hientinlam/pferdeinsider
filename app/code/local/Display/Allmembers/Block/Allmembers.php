<?php
class Display_Allmembers_Block_Allmembers extends Mage_Core_Block_Template
{
  // necessary methods
    public function __construct()
	{   
		parent::__construct();
                
                 $groupId=4;
                 $memberIds=array();
                 $collection = Mage::getModel('customer/customer')->getCollection()->addAttributeToFilter('group_id', 4);
                 //echo "<pre>";print_r($collection);die('shsh');
                 $this->setCollection($collection);
	}
    public function _prepareLayout()
        {
          if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
               $breadcrumbsBlock->addCrumb('home', array(
                   'label'=>Mage::helper('catalog')->__('Home'),
                   'title'=>Mage::helper('catalog')->__('Home Page'), 
                   'link'=>Mage::getBaseUrl()))
                 ->addCrumb('horseexpert', array(
                'label'=>Mage::helper('catalog')->__('Horse Experts'),
                'title'=>Mage::helper('catalog')->__('Horse Experts'),
                'link'=>Mage::getBaseUrl().'allmembers/index/index'
            ) );
            
           
            
        }


            
     
          
         $toolbar = $this->getToolbarBlock();
		// called prepare sortable parameters
		$collection = $this->getCollection();
                
		$toolbar->setCollection($collection);
                $this->setChild('toolbar', $toolbar);
		$this->getCollection()->load();
		return $this;
        }
	public function getDefaultDirection(){
		return 'asc';
	}
	public function getAvailableOrders(){
		return array('name'=> 'Name','position'=>'Position','children_count'=>'Sub Category Count');
	}
	public function getSortBy(){
		return 'name';
	}
	public function getToolbarBlock()
	{
		$block = $this->getLayout()->createBlock('allmembers/toolbar', microtime());
		return $block;
	}
	public function getMode()
	{
		return $this->getChild('toolbar')->getCurrentMode();
	}

	public function getToolbarHtml()
	{
		return $this->getChildHtml('toolbar');
	}


 
}
?>