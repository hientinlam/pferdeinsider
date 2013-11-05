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


class Simple_Forum_Block_View extends Mage_Core_Block_Template
{
		private $_current_object         = false;
		
		const PAGE_VAR_NAME             = 'p';
		const LIMIT_VAR_NAME            = 'limit';
		const SORT_VAR_NAME             = 'sort';
		
		private $sort_type              = array(1 => 'desc', 2=> 'asc');
		private $default_sort_type     = 1;
		
		protected $_objectsCollection = false;
		
	    protected function _prepareLayout()
	    {
	    	$this->limits = Mage::helper('forum/topic')->getPagesLimits();
	        parent::_prepareLayout();
	        $root = $this->getLayout()->getBlock('root');
			$root->setTemplate(Mage::helper('forum/data')->getLayout());
	        $this->initCollection();
	    }
	    
	    protected function getAllObjects()
	    {
			if(!$this->_objectsCollection)
			{
				return $this->initCollection();
			}
			return $this->_objectsCollection; 
		}
	    
	    protected function getControls($show_fast_replay_button = 0)
	    {
	    	if($show_fast_replay_button && $this->getAllowFastReply())
	    	{		
	    		return $this->getChildHtml('controls_bottom');
			}
			return $this->getChildHtml('controls');
		}
		
		public function getAllowFastReply()
		{
			return ( Mage::getStoreConfig('forum/forumconfiguration/allowfastreply') ? Mage::getStoreConfig('forum/forumconfiguration/allowfastreply') : 0 );	
		}
	    
	    protected function getTitleContent()
	    {
	    	if(!$this->getIsCategory())
			{
				if($this->getSearchValue())
				{
					$title = Mage::helper('forum/post')->preparePostBySearchValue( $this->_current_object->getTitle() );
					return Mage::helper('forum/topic')->__('Search in Topic') . ' "' . $title . '"' . ' ' . Mage::helper('forum/topic')->__('search value') . ': "' . $this->getSearchValue( )  . '"';
				}
				else
				{
					$title = $this->_current_object->getTitle();
					return Mage::helper('forum/topic')->__('View Topic') . ' "' . $title . '"';
				}
			}
			else
			{
				return Mage::helper('forum/topic')->__('View Forum') . ' "' . $this->_current_object->getTitle() . '"';
			}
		}
	    
	    protected function getHeadHtml()
	    {
			return $this->getChildHtml('head');
		}
		
		protected function getToolbarHtml()
		{
			return $this->getChildHtml('toolbar');
		}
		
		public function getTopicId()
		{
			if(Mage::registry('current_object'))
			{
				return Mage::registry('current_object')->getId();
			}
		}
		
		public function getSaveUrl()
		{
			if(Mage::registry('current_object'))	
			{
				return $this->getUrl('*/*/save/id/' . Mage::registry('current_object')->getId());
			}
		}
	    
	    public function getIsCategory()
	    {
	    	if(Mage::registry('current_object'))
				return Mage::registry('current_object')->getIs_category();
			else 
				return false;
		}
		
		public function getSearchValue()
		{
			$sess = Mage::getSingleton('forum/session');
			if($sess->getSearchValue())
			{
				return $sess->getSearchValue();
			}
			return false;
		}
		
		public function getHaveEditableRights($_id)
		{
			$customer = Mage::registry('current_customer');
			if($customer)
			{
				if($customer->getId() == $_id)
				{
					return true;
				}
			}
			if(Mage::helper('forum/topic')->getAllowedModerateWebsites($customer->getId()))
			{
				return true;
			}
		}
		
		public function getParentRetUrl($id)
		{
			return urldecode($this->getParentUrlText($id));
		}
		
		public function getParentId()
		{
			return Mage::registry('current_object')->getParent_id();
		}
		
		public function getRetUrl($obj)
		{
			return urlencode($obj->getUrl_text());
		}
		
		public function getEditTopicUrl($_id, $_obj)
		{
			$firstPost = $this->getFirstPost($_obj);
			return $this->getBaseUrl() . 'forum/topic/edit/id/' . $_id . '/parent_id/' . $_obj->getParent_id() . '/post_id/' . ($firstPost ? $firstPost->getId() : '0') .'?ret=' . $this->getParentRetUrl($_obj->getParent_id());
		}
		
		public function getFirstPost($_obj)
		{
			$c = Mage::getModel('forum/post')->getCollection()
			->setPageSize(1)
			->setOrder('created_time', 'ASC');
			$c->getSelect()->where('parent_id=?', $_obj->getId())->where('system_user_id=?', $_obj->getSystem_user_id());
			foreach($c as $val)
			{
				return $val;
			}
		}
		
		public function getParentUrlText($_id)
		{
			return Mage::getModel('forum/topic')->load($_id)->getUrl_text();
		}
		
		public function getDeleteTopicUrl($_id)
		{
			return $this->getBaseUrl() . 'forum/topic/deleteTopic/topic_id/' . $_id . '?ret=' . $this->getRetUrl(Mage::registry('current_object'));
		}
	    
	    public function getEditPostUrl($_id, $obj)
	    {
			return $this->getBaseUrl() . 'forum/topic/edit/id/' . $obj->getParent_id() . '/parent_id/' . Mage::getModel('forum/topic')->load($obj->getParent_id())->getParent_id() . '/post_id/' . $_id .'?ret=' . $this->getRetUrl(Mage::registry('current_object'));
		}
	    
	    public function getDeletePostUrl($_id)
	    {
			return $this->getBaseUrl() . 'forum/topic/deletePost/post_id/' . $_id . '?ret=' . $this->getRetUrl(Mage::registry('current_object'));
		}
		
		public function getDeactivatePostUrl($_id)
		{
			return $this->getBaseUrl() . 'forum/topic/disablePost/post_id/' . $_id . '?ret=' . $this->getRetUrl(Mage::registry('current_object'));
		}
		
		public function getDeactivateTopicUrl($_id)
		{
			return $this->getBaseUrl() . 'forum/topic/disableTopic/topic_id/' . $_id . '?ret=' . $this->getRetUrl(Mage::registry('current_object'));
		}
		
		public function getActivatePostUrl($_id)
		{
			return $this->getBaseUrl() . 'forum/topic/enablePost/post_id/' . $_id . '?ret=' . $this->getRetUrl(Mage::registry('current_object'));
		}
		
		public function getActivateTopicUrl($_id)
		{
			return $this->getBaseUrl() . 'forum/topic/enableTopic/topic_id/' . $_id . '?ret=' . $this->getRetUrl(Mage::registry('current_object'));
		}
	    
	    public function initCollection()
	    {
	    	$this->_current_object = Mage::registry('current_object');
	    	$sort = $this->getSort(); 
	    	$isModerator       = Mage::helper('forum/topic')->isModerator();
			if(!$this->_objectsCollection && !$this->getIsCategory())
			{
				$this->_objectsCollection  =  Mage::getModel('forum/post')->getCollection()
				->setPageSize($this->_getLimit())
				->setOrder('created_time', $sort)
				->setCurPage($this->_getCurPage());
				$this->_objectsCollection->getSelect()->where('parent_id=?', $this->_current_object->getId());
				if(!$isModerator)
				{
					$this->_objectsCollection->getSelect()->where('status=1');	
				}
				if($search = $this->getSearchValue())
				{
					$this->_objectsCollection->getSelect()->where('post_orig LIKE ?', '%' . $search . '%');	
					$this->_updatePostSearch();
				}
				$this->setAdditionalDataPost();
			}
			elseif(!$this->_objectsCollection)
			{
				$this->_objectsCollection  =  Mage::getModel('forum/topic')->getCollection()
				->setPageSize($this->_getLimit())
				->setOrder('created_time', $sort)
				->setCurPage($this->_getCurPage());
				$this->_objectsCollection->getSelect()->where('parent_id=?', $this->_current_object->getId());
				if(!$isModerator)
				{
					$this->_objectsCollection->getSelect()->where('status=1');	
				}
				$this->setAdditionalData();
			}
			return $this->_objectsCollection;
		}
		
		public function isModerator()
		{
			return Mage::helper('forum/topic')->isModerator();
		}
		
		public function getSort()
		{
			$sort = (int) $this->getRequest()->getParam($this->getSortVarName(), false);
			if($sort && array_key_exists($sort, $this->sort_type))
			{
				return $this->_setSort($sort);
			}
			$default_sort_post  = Mage::getStoreConfig('forum/forumconfiguration/defaultshowsnewestpost') ? 1 : 2;
			$default_sort_topic = Mage::getStoreConfig('forum/forumconfiguration/defaultshowsnewesttopic') ? 1 : 2;	
			if(!$this->getIsCategory())
			{
				$sort =  Mage::getSingleton('forum/session')->getPageSortPost() 
					? Mage::getSingleton('forum/session')->getPageSortPost() 
						: $default_sort_post;
			}
			else
			{
				$sort =  Mage::getSingleton('forum/session')->getPageSortForum() 
					? Mage::getSingleton('forum/session')->getPageSortForum() 
						: $default_sort_topic;
			}
			if(array_key_exists($sort, $this->sort_type))
			{
				return $this->sort_type[$sort];
			}
			return $this->sort_type[1];
		}
		
		public function _getLimit()
	    {
	    	$limit = (int) $this->getRequest()->getParam($this->getLimitVarName(), false);
			if($limit && in_array($limit, $this->limits))
			{
				return $this->_setLimit($limit);
			}
			else
			{
				if(!$this->getIsCategory())
				{
					return Mage::getSingleton('forum/session')->getPageLimitPost() 
						? Mage::getSingleton('forum/session')->getPageLimitPost() 
							: $this->limits[0];
				}
				else
				{
					return Mage::getSingleton('forum/session')->getPageLimitForum() 
						? Mage::getSingleton('forum/session')->getPageLimitForum() 
							: $this->limits[0];
				}
			}
		}
		
		public function _getCurPage()
		{
			$page = (int) $this->getRequest()->getParam($this->getPageVarName(), null);
			if($page == null)
			{
				if(!$this->getIsCategory())
				{
					return Mage::getSingleton('forum/session')->getPageCurrentPost() 
					? Mage::getSingleton('forum/session')->getPageCurrentPost() 
						: 1;
				}
				else
				{
					return Mage::getSingleton('forum/session')->getPageCurrentForum() 
					? Mage::getSingleton('forum/session')->getPageCurrentForum() 
						: 1;
				}
			}
			else
			{
				return $this->_setCurPage($page);
			}
		}
		
		public function getPageVarName()
		{
			return self::PAGE_VAR_NAME;
		}
		
		public function getLimitVarName()
		{
			return self::LIMIT_VAR_NAME;
		}
		
		public function getSortVarName()
		{
			return self::SORT_VAR_NAME;
		}
		
		public function getFormatedDate($date)
	    {
	        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
	    }
    
    	public function chooseMainTemplate()
		{
			if (!$this->getIsCategory()) 
			{
				$this->setTemplate($this->getPostsTemplate());
			} 
			else 
			{
				$this->setTemplate($this->getTopicsTemplate());
			}
		}
		
		public function getTop()
		{
			return $this->getChildHtml('forum_top');
		}
		
		public function getBreadCrumbs()
		{
			if(Mage::getStoreConfig('forum/forumconfiguration/allowbreadcrumbs'))
			{
				return $this->getChildHtml('util_breadcrumbs');
			}
		}
		
		public function getViewUrl($id, $obj = false)
		{
			if($obj && $obj->getUrl_text() != '' && $obj->getUrl_text())
			{
				return $this->_getUrlrewrited( array(self::PAGE_VAR_NAME => 1), $obj->getUrl_text());	
			}
			return $this->_getUrl( array(self::PAGE_VAR_NAME => 1), '/view/id/' . $id);
		}

		public function getViewUrlLatest($id, $obj = false)
		{
			$urlParams = array();
	        $urlParams['id']             = $id;
	        return $this->getUrl( 'forum/topic/viewreply', $urlParams);
		}
		
		public function getAllowDelete()
		{
			return (Mage::getStoreConfig('forum/forumconfiguration/allowdeleteowntpics') ? Mage::getStoreConfig('forum/forumconfiguration/allowdeleteowntpics') : 0);
		}
		
		public function getRecaptchField()
		{
			if(Mage::getStoreConfig('forum/recaptchasetting/allowrecaptcha') && !Mage::helper('forum/topic')->isModerator())
			{
				return $this->getChildHtml('recaptcha');	
			}
		}
		
		public function getBackFormUrl()
		{
			return 'forum/topic/edit/id/' . Mage::registry('current_object')->getId() . '/parent_id/' . Mage::registry('current_object')->getParent_id() . '/ret/' . $this->getRetUrl(Mage::registry('current_object'));	
		}
		
		public function getUser_name_post($post)
		{ 
			if(Mage::getStoreConfig('forum/forumconfiguration/allowuseingnicknames') && $post->getUser_nick() && $post->getUser_nick() != '')
			{
				return $post->getUser_nick();
			}
			else
			{
				return $post->getUser_name();
			}
		}
		
		public function getCreateTopicUser($topic)
		{
			if(Mage::getStoreConfig('forum/forumconfiguration/allowuseingnicknames') && $topic->getUser_nick() && $topic->getUser_nick() != '')
			{
				return $topic->getUser_nick();
			}
			else
			{
				return $topic->getUser_name();
			}
		}
		
		public function getLatestReply($obj)
		{
			if(Mage::getStoreConfig('forum/forumconfiguration/allowuseingnicknames') && $obj->getLatest_user_nick() && $obj->getLatest_user_nick() != '')
			{
				return $obj->getLatest_user_nick();
			}
			else
			{
				return $obj->getLatest_user_name();
			}
		}
		
		public function getReplyIdBegin()
		{
			return Mage::helper('forum/viewreply')->getReplyIdBegin();
		}
		
		private function _setCurPage($page = 1)
		{
			if(!$this->getIsCategory())
			{
				Mage::getSingleton('forum/session')->setPageCurrentPost($page);
			}
			else
			{
				Mage::getSingleton('forum/session')->setPageCurrentForum($page);
			}
			return $page; 	
		}
		
		private function _setLimit($limit)
		{
			if(!$this->getIsCategory())
			{
				Mage::getSingleton('forum/session')->setPageLimitPost($limit);
			}
			else
			{
				Mage::getSingleton('forum/session')->setPageLimitForum($limit);
			}
			return $limit;
		}
		
		private function _setSort($sort = 1)
		{
			if(!array_key_exists($sort, $this->sort_type))
			{
				$sort = $this->default_sort_type;
			}
			
			if(!$this->getIsCategory())
			{
				Mage::getSingleton('forum/session')->setPageSortPost($sort);
			}
			else
			{
				Mage::getSingleton('forum/session')->setPageSortForum($sort);
			}
			return $this->sort_type[$sort];
		}
		
		private function _setPostsQuantity()
		{
			foreach($this->_objectsCollection as $key=>$val)
			{
				$this->_objectsCollection->getItemById($key)->setTotal_posts( Mage::helper('forum/topic')->getPostsQuantity( $val, 0, $val ) );
			}
		}
		
		private function _setLastPostInfo()
		{
			foreach($this->_objectsCollection as $key=>$val)
			{
				$obj = Mage::helper('forum/topic')->latestPosts;
				if(!empty($obj[$val->getId()]))
				{
					$this->_objectsCollection->getItemById($key)->setLatest_topic($obj[$val->getId()]['topic_obj']);	
					$this->_objectsCollection->getItemById($key)->setLatest_created_time($obj[$val->getId()]['created_time']);	
					$this->_objectsCollection->getItemById($key)->setLatest_user_name($obj[$val->getId()]['user_name']);	
					if(Mage::getStoreConfig('forum/forumconfiguration/allowuseingnicknames') && !empty($obj[$val->getId()]['user_nick']))
					{
						$this->_objectsCollection->getItemById($key)->setLatest_user_nick($obj[$val->getId()]['user_nick']);	
					}
					$this->_objectsCollection->getItemById($key)->setLatest_postId($obj[$val->getId()]['post_id']);
				}	
			}
		}
		
		private function _setTotalViews()
		{
			foreach($this->_objectsCollection as $key=>$val)
			{
				$this->_objectsCollection->getItemById($key)->setTotal_views( Mage::helper('forum/topic')->getTotalViews( $val ) );
			}
		}
		
		private function _updatePostSearch()
		{
			foreach($this->_objectsCollection as $key=>$val)
			{
				$this->_objectsCollection->getItemById($key)->setPost( Mage::helper('forum/post')->preparePostBySearchValue( $val->getPost() ) );
			}
		}
		
		private function setAdditionalDataPost()
		{
			$this->_setPostsUsersData();
		}
		
		private function _setPostsUsersData()
		{
			$userObject = array();
			foreach($this->_objectsCollection as $key=>$val)
			{
				$user_id    = $val->getSystem_user_id() ? $val->getSystem_user_id() : 10000000;
				if(empty($userObject[$user_id]))$userObject[$user_id] = Mage::getModel('forum/user')->load($user_id, 'system_user_id');
				if(!empty($userObject[$user_id]))
				{
					$this->_objectsCollection->getItemById($key)->setUserTotalPosts( $userObject[$user_id]->getTotal_posts());
					$this->_objectsCollection->getItemById($key)->setUserJoined( $userObject[$user_id]->getFirst_post());
				}
			}
		}
		
		private function setAdditionalData()
		{
			$this->_setPostsQuantity();
			$this->_setLastPostInfo();
			$this->_setTotalViews();	
		}
	
		private function _getUrlrewrited($params, $urlAddon = '')
		{
			$urlParams = array();
	        $urlParams['_current']  = false;
	        $urlParams['_escape']   = false;
	        $urlParams['_use_rewrite']   = false;
	        $urlParams['_query']    = $params;
	        return $this->getUrl( $urlAddon, $urlParams);
		}
		
		private function _getUrl($params, $urlAddon = '')
		{
			$urlParams = array();
	        $urlParams['_current']  = true;
	        $urlParams['_escape']   = true;
	        $urlParams['_use_rewrite']   = true;
	        $urlParams['_query']    = $params;
	        return $this->getUrl('*//*//' . $urlAddon, $urlParams);
		}
		
    
}