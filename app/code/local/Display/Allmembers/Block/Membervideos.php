<?php
class Display_Allmembers_Block_Membervideos extends Mage_Core_Block_Template
{
  // necessary methods
 
     public function __construct()
	{
		parent::__construct();
                
                $Ids=array();
                $fromdate=array();
                $customer=Mage::getModel('customer/session')->getCustomer();
                $customerId=$customer->getId();
                $membercollection=Mage::getModel('allmembers/allmembers')->getCollection()->addFieldToFilter('customer_id',Array('eq'=>$customerId));
                $membercollection->addFieldToFilter('status',Array('eq'=>1));
                foreach($membercollection as $memberInfo)
                {
                    $memberId=$memberInfo['member_id'];
                    $fromdate[]=date('Y-d-m',strtotime($memberInfo['createddate']));
                    //$frmdate=array_unique($fromdate);
                    $customerdata=Mage::getModel('customer/customer')->load($memberId);
                    $getmemberdata=Mage::getModel('admin/user')->getCollection()->getData();
                    foreach($getmemberdata as $memberdata)
                    {
                      if($customerdata->getEmail()==$memberdata['email'])
                        {
                          $data=$memberdata['username'];
                          $productModel = Mage::getModel('catalog/product')->getCollection();
                          $attr = $productModel->getResource()->getAttribute("member_list");
                          if ($attr->usesSource()) {
                             $ids[] = $attr->getSource()->getOptionId($data);
                          }
                        }
                    }
                }
                
                $collection = Mage::getModel('catalog/product')->getCollection();
                $collection->addAttributeToSelect('*')
                                            ->addAttributeToFilter('member_list', array('in'=>$ids));
                
                $collection->addAttributeToSort('created_at', 'desc');
                $this->setCollection($collection);
                 
        }
    public function _prepareLayout()

        {
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