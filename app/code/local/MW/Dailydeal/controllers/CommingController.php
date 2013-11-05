<?php
class MW_Dailydeal_CommingController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
    {
    	$uri = explode('/dailydeal/comming',$_SERVER['REQUEST_URI']);
    	$uri1 = explode('/dailydeal/comming/index',$_SERVER['REQUEST_URI']);
    	
    	if((sizeof($uri)>1) || (sizeof($uri1)>1)) {
			$this->_redirect(Mage::helper('dailydeal')->getRewriteUrl('dailydeal/comming'));
		}else{
    		$this->loadLayout();
                 $this->getLayout()->getBlock('head')->setTitle('Comming Deals');
			$this->renderLayout();
		}
		/*$this->loadLayout();     
		$this->renderLayout();*/    	
    }
}