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


class Simple_Forum_Adminhtml_ForumController extends Mage_Adminhtml_Controller_action
{


    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('forum/forum')
            ->_addBreadcrumb(Mage::helper('forum/forum')->__('Forum Manager'), Mage::helper('forum/forum')->__('Forum Manager'));
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
        $forumId     = $this->getRequest()->getParam('id');
        $forumModel  = Mage::getModel('forum/forum')->load($forumId);
        if ($forumModel->getId() || $forumId == 0)
		{
            Mage::register('forum_data', $forumModel);
            $this->loadLayout();
            $this->_setActiveMenu('forum/forum');
            $this->_addBreadcrumb(Mage::helper('forum/forum')->__('Forum Manager'), Mage::helper('forum/forum')->__('Forum Manager'));
            $this->_addBreadcrumb(Mage::helper('forum/forum')->__('Item Forum'), Mage::helper('forum/forum')->__('Item Forum'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('forum/adminhtml_forum_edit'))
                 ->_addLeft($this->getLayout()->createBlock('forum/adminhtml_forum_edit_tabs'));
            $this->renderLayout();
        }
		else
		{
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('forum/forum')->__('Forum does not exist'));
            $this->_redirect('*/*/');
        }
    }

    /**
    * Save action
    */
	public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) {
            try {
                $postData   = $this->getRequest()->getPost();
                $forumModel = Mage::getModel('forum/forum');
                //validate url key
                if($postData['url_text_short'] != '')
                {
                	$notValidUrlKey = Mage::helper('forum/topic')->validateUrlKey('forum/'. $postData['url_text_short'], $this->getRequest()->getParam('id'));
                	if($notValidUrlKey)
	                {
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('forum/forum')->__('Forum URL Key for specified forum already exist in store.'));
		                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
		                Mage::getSingleton('adminhtml/session')->setPostData($this->getRequest()->getPost());
                		return;
					}
				}
				else
				{
					$postData['url_text_short'] = Mage::helper('forum/topic')->buildUrlKeyFromTitle($postData['title'], $this->getRequest()->getParam('id'));
				}
				$description = strip_tags( preg_replace("/[\r\t\n\v]/","",$postData['description']) );
					$forumModel->setId($this->getRequest()->getParam('id'))
                    ->setIs_category(1)
                    ->setStatus($postData['status'])
                    ->setUser_name('admin')
                    ->setUrl_text_short($postData['url_text_short'])
                    ->setUrl_text('forum/' . $postData['url_text_short'])
                    ->setTitle($postData['title'])
                    ->setPriority( $postData['priority'] )
                    ->setDescription( $description )
					;
                if(!$this->getRequest()->getParam('id')) //new item
                {
					$forumModel->setCreated_time(now());
				}
				else
				{
					$forumModel->setUpdate_time(now());
				}
                $forumModel->setMeta_description($postData['meta_description']);
                $forumModel->setMeta_keywords($postData['meta_keywords']);
                $forumModel->save();

                $forumModel->setEntityUserId(10000000);
                $id_path     = $this->buildIdForumPath($forumModel->getId());
                $requestPath = $this->buildRequestPath($forumModel->getId());
                Mage::helper('forum/topic')->updateUrlRewrite($id_path, Mage::app()->getStore()->getId(), 'forum/' . $postData['url_text_short'], $requestPath);
                Mage::helper('forum/topic')->setEntity($forumModel->getId(), $forumModel, true);
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('forum/forum')->__('Forum was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setPostData(false);
                if($this->getRequest()->getParam('back') == 'edit') $this->_redirect('*/*/edit/id/' . $forumModel->getId());
				else $this->_redirect('*/*/');
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
				$this->deleteEntity($this->getRequest()->getParam('id'));
				$this->deleteUrlRewrite($this->getRequest()->getParam('id'), 1);
                $this->deleteAllTopics($this->getRequest()->getParam('id'));
                $forumModel = Mage::getModel('forum/forum');
                $forumModel->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('forum/forum')->__('Forum was successfully deleted.<br>All Forum Topics were successfully deleted!<br>All Topics Posts were successfully deleted!'));
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
		$ids = $params['forum'];
		if(is_array($ids))
		{
			foreach($ids as $id)
			{
				try
				{
					$this->deleteEntity($id);
					$this->deleteUrlRewrite($id, 1);
	                $this->deleteAllTopics($id);
	                $forumModel = Mage::getModel('forum/forum');
	                $forumModel->setId($id)
	                    ->delete();

	            }
				catch (Exception $e)
				{
	                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
	                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
	            }
			}
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('forum/forum')->__('All Forums were successfully deleted.<br>All Forums Topics were successfully deleted!<br>All Topics Posts were successfully deleted!'));
		$this->_redirect('*/*/');
	}

	public function massStatusAction()
	{
		$params = $this->getRequest()->getParams();
		$status = (!empty($params['status']) ? $params['status'] : 0);
		$ids = $params['forum'];
		if(is_array($ids))
		{
			foreach($ids as $id)
			{
				$forum = Mage::getModel('forum/forum')->load($id);
				$forum->setStatus($status==2?0:$status);
				$forum->setUpdate_time(now());
				$forum->save();
			}
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('forum/forum')->__('All Forums statuses were successfully changed.'));
		$this->_redirect('*/*/');
	}

	private function buildRequestPath($id)
	{
		return Mage::helper('forum/topic')->buildRequestForumPath($id);
	}

	private function buildIdForumPath($id)
	{
		return Mage::helper('forum/topic')->buildIdForumPath($id);
	}

	private function buildIdPath($id)
	{
		return Mage::helper('forum/topic')->buildIdPath($id);
	}

	private function deleteUrlRewrite($id, $is_forum = false)
	{
        if(!$is_forum)$id_path = $this->buildIdForumPath($id);
        else $id_path = $this->buildIdPath($id);
		Mage::helper('forum/topic')->deleteUrlRewrite($id_path);
	}

	private function deleteAllTopics($id)
	{
		$collection  = Mage::getModel('forum/topic')->getCollection();
		$collection->getSelect()->where('parent_id=?', $id);
		foreach($collection as $topic)
		{
			try
			{
				$this->deleteEntity($topic->getId());
				$this->deleteUrlRewrite($topic->getId());
				$this->deleteAllPosts($topic->getId());
				Mage::helper('forum/notify')->deleteByTopicId($topic->getId());
				$modelDelete  = Mage::getModel('forum/topic');
				$modelDelete->setId($topic->getId())
                    ->delete();
			}
			catch(Exception $e)
			{

			}
		}
	}


    private function deleteAllPosts($id)
	{
		$collection  = Mage::getModel('forum/post')->getCollection();
		$collection->getSelect()->where('parent_id=?', $id);
		foreach($collection as $post)
		{
			try
			{
				$modelDelete  = Mage::getModel('forum/post');
				$modelDelete->setId($post->getId())
                    ->delete();
			}
			catch(Exception $e)
			{

			}
		}
	}

	private function deleteEntity($id)
	{
		return Mage::helper('forum/topic')->deleteEntity($id);
	}
}
