<?php
class Brst_Embeded_Block_Embeded extends Mage_Core_Block_Template
{

	public function __construct()
	{
		parent::__construct();
                
                $result= $this->allQuery();
                if($result=='search')
                {
                    
                    $search_id=$this->getRequest()->getParam('search');
                    $collection = Mage::getModel('catalog/product')->getCollection();
                    //$attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
                    //$collection->addAttributeToSelect($attributes);
                    $collection->addAttributeToSelect('*')
                               ->addAttributeToFilter('video_category',$search_id);
                    
                    $this->setCollection($collection);
                }
                elseif($result=='search1')
                {
                    $searchdata=$this->getRequest()->getParam('searchdata');
                    $collection = Mage::getModel('catalog/product')->getCollection();
                    //$attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
                    //$collection->addAttributeToSelect($attributes);
                    $collection->addAttributeToSelect('*')
                               ->addAttributeToFilter('name',array('like' => '%' .$searchdata. '%'));

                    $this->setCollection($collection);
                }
                else
                {
                
                    $prodIds=explode(',',$result['product_id']);
                    $collection = Mage::getModel('catalog/product')->getCollection();
                    $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
                    $collection->addAttributeToSelect($attributes);
                    $collection->addAttributeToSelect('*')->addAttributeToFilter('entity_id', array('in'=>$prodIds));
                    $this->setCollection($collection);
                              
                }
               
		
	
        }
        public function allQuery()
        {
            
              $data=$this->getRequest()->getParam('code'); 
              $data1=$this->getRequest()->getParam('search'); 
              $data2=$this->getRequest()->getParam('searchdata'); 
              if($data1==NULL && $data2==NULL)
              {
               $dataid= Mage::getSingleton('core/session')->setCodevalue($data);
              }
             
              
             
               if($data1==NULL && $data2==NULL )                   
               {    
                    $resource = Mage::getSingleton('core/resource');
                    $connection= $resource->getConnection('core_read');			
                    $query="select * from tbl_embedcode where embed_code='$data'";
                    $result=$connection->query($query);
                    $alldata = $result->fetch(); 
                    return $alldata;
               }
               elseif($data1!=NULL)
               {
                   return 'search';
               }
               else{
                   return 'search1';
        }
        }

	protected function _prepareLayout()
	{
		parent::_prepareLayout();

		$parent_id = Mage::app()->getStore()->getRootCategoryId();
		if($this->getRequest()->getParam('category_id',false)){
			$parent_id = $this->getRequest()->getParam('category_id');
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
		$block = $this->getLayout()->createBlock('embeded/toolbar', microtime());
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