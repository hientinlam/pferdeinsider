<?php
class MW_Dailydeal_Adminhtml_DealitemsController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction()
	{
		$this->loadLayout();
		return $this;
	}
	public function indexAction()
	{
		$this->_initAction()
			->renderLayout();
	}
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('dailydeal/dailydeal')->load($id);
		
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			if (Mage::getSingleton('dailydeal/dailydeal')->getFlag() == 'dailyschedule'){
				$model->setData('start_date_time',$this->getRequest()->getParam('start'));							
			}
			Mage::register('dealitems_data', $model);
			
			$this->loadLayout();
			$this->_setActiveMenu('dealitems/items');
			
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Deal Manager'), Mage::helper('adminhtml')->__('Deal Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Deal News'), Mage::helper('adminhtml')->__('Deal News'));
			
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			
			$this->_addContent($this->getLayout()->createBlock('dailydeal/adminhtml_dealitems_edit'))
				->_addLeft($this->getLayout()->createBlock('dailydeal/adminhtml_dealitems_edit_tabs'));
			
			$this->renderLayout();
			} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dailydeal')->__('Deal does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	public function newAction() {
		//var_dump(Mage::getSingleton('adminhtml/session')->getFlag() == 'dailyschedule');die();
		Mage::getSingleton('adminhtml/session')->setFlag('dealitems');
		
		$this->_forward('edit');
		
	}
	public function newdailyAction()
	{
		$this->_forward('edit');
	}
	public function saveAction() {
		try {
			if (($this->getRequest() == null) || ($this->getRequest()->getPost() == null)) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dailydeal')->__('Unable to find deal to save'));
				$this->_redirect('*/*/');
				return;
			}
			$id     = $this->getRequest()->getParam('id');
			$data = $this->getRequest()->getPost();	//Zend_Debug::dump($data);die;			
			//var_dump($data['start_date']);die;
			//Kt xem da co deal nao ma voi cung product nay thi khoang thoi gian bi de len nhau
			$dealcompare = Mage::getModel('dailydeal/dailydeal')->getCollection()
				->addFieldToFilter('product_sku',$data['product_sku']);
			$start = strtotime($data['start_date']);
			$end = strtotime($data['end_date']);
			$boolean = true;
			foreach ($dealcompare as $deal){				
				$startcompare = strtotime($deal->getStartDateTime());
				$endcompare = strtotime($deal->getEndDateTime());
				if($deal->getDailydealId() != $id){					
					if ((($start<$startcompare) && ($end >=$startcompare) && ($data['status'] == 1 && $deal->getStatus() == 1))
					|| (($start >= $startcompare) && ($start <= $endcompare) && ($data['status'] == 1 && $deal->getStatus() == 1))){
						$boolean = false;
					}
				}
			}
			//var_dump($boolean);die;
			if($boolean == false) {//echo 'fdsgd';die;
				Mage::getSingleton('adminhtml/session')->addError('Have another deal with this product in time.Please select other deal time!');
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
				}else {
				//Zend_Debug::dump($simpleproduct);die;
				$model = Mage::getModel('dailydeal/dailydeal');
				// Fill model with POST data
				$model->setData($data)
					->setId($this->getRequest()->getParam('id'));
				$start = date('Y-m-d H:i:s',strtotime($data['start_date']));//Zend_Debug::dump($data);die;
				$end = date('Y-m-d H:i:s',strtotime($data['end_date']));
				/**
				* Phan nay phuc vu cho muc dich customize...
				* */
				//				$_product = Mage::getModel('catalog/product')->load($data['product_id']);
				//	  			if ($_product->getTypeId() == 'configurable'){
					//	  				$model->setStartDateTime($start);
					//					$model->setEndDateTime($end);								
					//					$model->save();
					//					//Zend_Debug::dump($data);die();
					// 					//Get child products id (only ids)
						////					1 cach goi khac lay child ids 					
					////					$childIds = Mage::getModel('catalog/product_type_configurable')
						////                    	->getChildrenIds($data['product_id']);
					//					
					//					$childids = $_product->getTypeInstance()->getUsedProductIds();	 
					//
					//					
					//                    foreach ($childids as $childid){
						//                    	$modelconfigurable = Mage::getModel('dailydeal/configproduct');
						//					//Zend_Debug::dump($childids);die;
						//                    	$modelconfigurable->setDailydealId($model->getDailydealId());
						//                    	$childproduct = Mage::getModel('catalog/product')->load($childid);
						//                    	//Zend_Debug::dump($childproduct);die;
						//                    	$modelconfigurable->setProductId($childproduct->getEntityId());
						//                    	$modelconfigurable->setProductSku($model->getProductSku());
						//						$modelconfigurable->setChildSku($childproduct->getSku());
						//						$modelconfigurable->setChildQty($model->getDealQty());						
						//						$modelconfigurable->save(); 
					//                    }
					//	  			} else {
					//Zend_Debug::dump($data);die();
					$model->setStartDateTime($start);
					$model->setEndDateTime($end);								
					$model->save();
					$_product = Mage::getModel('catalog/product')->load($data['product_id']);
					$_product->setData("activedeal", 1);
					$_product->save();
				//	  			}
				// Add notifications
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('dailydeal')->__('Deal was saved successfully!'));
				Mage::getSingleton('adminhtml/session')->setFormData(true);
				
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				//Zend_Debug::dump($data);//die();
				if (Mage::getSingleton('adminhtml/session')->getFlag() == 'dailyschedule')
					$this->_redirect('*/adminhtml_dailyschedule/list/');
				else 
				$this->_redirect('*/adminhtml_dealitems/index/');
				return;
			}
			
			}catch (Exception $ex){
			MW_Dailydeal_Helper_Data::LogError($ex);
			
			Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
			Mage::getSingleton('adminhtml/session')->setFormData($data);
			$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			return;
		}
		$this->_redirect('*/adminhtml_dailyschedule/list/');
	}
	public function exportCsvAction()
	{
		$fileName   = 'dealitems.csv';
		$content    = $this->getLayout()->createBlock('dailydeal/adminhtml_dealitems_grid')
			->getCsv();
		
		$this->_sendUploadResponse($fileName, $content);
	}
	
	public function exportXmlAction()
	{
		$fileName   = 'dealitems.xml';
		$content    = $this->getLayout()->createBlock('dailydeal/adminhtml_dealitems_grid')
			->getXml();
		
		$this->_sendUploadResponse($fileName, $content);
	}
	
	public function deleteAction(){
		$id = $this->getRequest()->getParam('id');
		if($id){
			try {
				$deal = Mage::getModel('dailydeal/dailydeal')->load($id);
				$deal->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
				} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/index');
	}
	
	public function massDeleteAction() {
		$testIds = $this->getRequest()->getParam('dailydeal');
		if(!is_array($testIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
			} else {
			try {
				foreach ($testIds as $testId) {
					$test = Mage::getModel('dailydeal/dailydeal')->load($testId);
					$test->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
				Mage::helper('adminhtml')->__(
				'Total of %d record(s) were successfully deleted', count($testIds)
					)
					);
				} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
	
	public function massStatusAction()
	{
		$testIds = $this->getRequest()->getParam('dailydeal');
		if(!is_array($testIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
			} else {
			try {
				foreach ($testIds as $testId) {
					$test = Mage::getSingleton('dailydeal/dailydeal')
						->load($testId)
						->setStatus($this->getRequest()->getParam('status'))
						->setIsMassupdate(true)
						->save();
				}
				$this->_getSession()->addSuccess(
				$this->__('Total of %d record(s) were successfully updated', count($testIds))
					);
				} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
	protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
	{
		$response = $this->getResponse();
		$response->setHeader('HTTP/1.1 200 OK','');
		$response->setHeader('Pragma', 'public', true);
		$response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
		$response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
		$response->setHeader('Last-Modified', date('r'));
		$response->setHeader('Accept-Ranges', 'bytes');
		$response->setHeader('Content-Length', strlen($content));
		$response->setHeader('Content-type', $contentType);
		$response->setBody($content);
		$response->sendResponse();
		die;
	}
	public function gridProductAction()
	{
		try
		{
			// for ajax call
			$response = $this->getLayout()->createBlock('dailydeal/adminhtml_dealitems_edit_product_grid')->getHtml();
			$this->getResponse()->setBody($response);
		}
		catch (Exception $ex)
		{
			MW_Dailydeal_Helper_Data::LogError($ex);
		}
	}
	
	public function refreshdealAction(){
		$visibility = array(
		Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
		Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
		);		
		$storeId = Mage::app()->getStore()->getId();
        
        $_productCollection = Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect('*')->addAttributeToFilter('visibility', $visibility)->setStoreId($storeId)->addStoreFilter($storeId);
		foreach ($_productCollection as $product) {
			$productDeal = Mage::getModel("dailydeal/observer")->_getProductId($product, true);
			if($productDeal != null){
				$_product = Mage::getModel('catalog/product')->load($product->getId());
				Mage::log($_product->getName());
				$_product->setActivedeal(1);
				$_product->save();
			}
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('dailydeal')->__('Refresh Deal was successfully!'));
		$this->_redirect('*/adminhtml_dealitems/index/');
		return;
	}
}