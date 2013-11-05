<?php
class MW_Dailydeal_PastController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
    {
    	$uri = explode('/dailydeal/past',$_SERVER['REQUEST_URI']);
    	$uri1 = explode('/dailydeal/past/index',$_SERVER['REQUEST_URI']);
    	
    	if((sizeof($uri)>1) || (sizeof($uri1)>1)) {
			$this->_redirect(Mage::helper('dailydeal')->getRewriteUrl('dailydeal/past'));
		}else{
    		$this->loadLayout();
                 $this->getLayout()->getBlock('head')->setTitle('Past Deals');
			$this->renderLayout();
		}   	
    }
}