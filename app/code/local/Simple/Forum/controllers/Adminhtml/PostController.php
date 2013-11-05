<?php

/**
 * webideaonline.com.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webideaonline.com/licensing/
 *
 */


class Simple_Forum_Adminhtml_PostController extends Mage_Adminhtml_Controller_action
{
	
	
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('forum/post')
            ->_addBreadcrumb(Mage::helper('forum/topic')->__('Post Manager'), Mage::helper('forum/topic')->__('Post Manager'));
        return $this;
    } 
 
 	/**
     * default action
     */
    public function indexAction()
    {
        $this->_initAction()     
            ->renderLayout();
    }
    
    /**
     * Create new post
     */
    public function newAction()
    {
    	$this->_forward('edit');	
    }
    
    /**
    * Edit action
    */
    public function editAction()
    {
        $postId     = $this->getRequest()->getParam('id');
        $postModel  = Mage::getModel('forum/post')->load($postId);

        if ($postModel->getId() || $postId == 0) 
		{
            Mage::register('post_data', $postModel);
            $this->loadLayout();
            $this->_setActiveMenu('forum/post');
            $this->_addBreadcrumb(Mage::helper('forum/topic')->__('Post Manager'), Mage::helper('adminhtml')->__('Post Manager'));
            $this->_addBreadcrumb(Mage::helper('forum/topic')->__('Item Post'), Mage::helper('adminhtml')->__('Item Post'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('forum/adminhtml_post_edit'))
                 ->_addLeft($this->getLayout()->createBlock('forum/adminhtml_post_edit_tabs'));
            $this->renderLayout();
        } 
		else 
		{
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('forum/topic')->__('Post does not exist'));
            $this->_redirect('*/*/');
        }
    }
    
    /**
    * Save action
    */
	public function saveAction()
    {
    	$set_last_post = false;
        if ( $this->getRequest()->getPost() ) {
            try {
                $postData = $this->getRequest()->getPost();
                $postModel = Mage::getModel('forum/post');
                
					$postModel->setId($this->getRequest()->getParam('id'))
                    ->setStatus($postData['status'])
                    ->setParent_id($postData['parent_id'])
                    ->setPost($postData['post']);
                    $postModel->setPost_orig(strval(strip_tags($postData['post'])));
                if(!$this->getRequest()->getParam('id')) //new item
                {
					$postModel->setCreated_time(now());
					$postModel->setUser_name(__('admin'));
					Mage::helper('forum/topic')->updateTotalPostsUser(10000000);
				}
				else
				{
					$postModel->setUpdate_time(now());
				}
                
                $postModel->save();
				Mage::helper('forum/topic')->setUserForum(10000000, $postModel);	
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Post was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setPostData(false);
				$this->_redirect('*/*/');
                return;
            } 
			catch (Exception $e) 
			{
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPostData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
    
    /**
    * Delete action
    */
    public function deleteAction()
    {
		if( $this->getRequest()->getParam('id') > 0 ) 
		{
            try 
			{
                $postModel = Mage::getModel('forum/post');
                $postModel->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('forum/topic')->__('Post was successfully deleted'));
                $this->_redirect('*/*/');
            } 
			catch (Exception $e) 
			{
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
	}
	
	
	public function massDeleteAction()
	{
		$params = $this->getRequest()->getParams();
		$ids = $params['post'];
		if(is_array($ids))
		{
			foreach($ids as $id)
			{
				try 
				{
	                $postModel = Mage::getModel('forum/post');
	                $postModel->setId($id)
	                    ->delete();
				} 
				catch (Exception $e) 
				{
	                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
	                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
	            }		
			}
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('forum/post')->__('All Posts were successfully deleted!'));
		$this->_redirect('*/*/');
	}
	
	public function massStatusAction()
	{
		$params = $this->getRequest()->getParams();
		$status = (!empty($params['status']) ? $params['status'] : 0);
		$ids = $params['post'];
		if(is_array($ids))
		{
			foreach($ids as $id)
			{
				$post = Mage::getModel('forum/post')->load($id);
				$post->setStatus($status==2?0:$status);
				$post->setUpdate_time(now());
				$post->save();
			}
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('forum/post')->__('All Posts statuses were successfully changed.'));
		$this->_redirect('*/*/');
	}
    
}
