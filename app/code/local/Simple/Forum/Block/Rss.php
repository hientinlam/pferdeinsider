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
 
class Simple_Forum_Block_Rss extends Mage_Rss_Block_Abstract
{
	protected $_objectsCollection   = false;
	private   $treeObject           = false;
	private   $parent_object        = false;
	private   $display_posts        = 15;
	private   $parent_topic         = false;
	
	private $loaded_by_parent       = false;//flag
	
	const PAGE_VAR_NAME             = 'p';
	const LIMIT_VAR_NAME            = 'limit';
	const SORT_VAR_NAME             = 'sort';
    
    protected function _toHtml()
    {
    	$this->initCollection();
		$rssObj = Mage::getModel('rss/rss');
	
		$title = Mage::helper('forum/topic')->__('Forum');
		if($this->parent_object)
		{
			$title .= ' ' . $this->parent_object->getTitle();
		}
		if($this->parent_topic)
		{
			$title .= ' ' . Mage::helper('forum/topic')->__('Topic') . ' ' .  $this->parent_topic->getTitle();
		}
		$data = array('title' => $title,
			'description' => $title,
			'link'        => $this->getUrl('forum'),
			'charset'     => 'UTF-8'
		);
				
		$rssObj->_addHeader($data);			
		
		if ($this->_objectsCollection) {
			
			foreach ($this->_objectsCollection as $key=>$object) 
			{
				$data = array(
								'title'         => $object->getData('title'),
								'link'          => htmlentities($object->getData('url')),
								'description'   => $object->getData('postContent'),
								'createdTime' 	=> strtotime($object->getData('createdTime')),
							);
							
				$rssObj->_addEntry($data);
			}
		}

		try
		{
			return $rssObj->createRssXml();
		}
		catch(Exception $e)
		{
			echo $e->getMessage($this->__('Error create RSS!') . $e->getMessage());
		}
    }
    
    private function initCollection()
    {
    	if(!$this->loaded_by_parent)
    	{
			$parent = Mage::registry('rssParent');
			if($parent)
			{
				$object = $this->getObjectById($parent);
				if(!$object) return;	

				if($this->getIsCategory($object)) //forum
				{
					$object->setChilds($this->getChildsByIdParent($object->getId()));
					$this->parent_object  = $object;
					$this->treeObject[]   = $object;
				}
				else //topic
				{
					$forum = $this->getObjectById($object->getParent_id());
					if(!$forum)
					{
						return;
					}
					$this->parent_object  = $forum;
					$this->parent_topic   = $object;
					$set = array('ch' => $object);
					$forum->setChilds($set);
					$this->treeObject[]   = $forum;
				}
			}
			else
			{
				$forums = $this->getChildsByIdParent(0);
				if($forums->getSize())
				{
					foreach($forums as $forum)
					{
						$forum->setChilds($this->getChildsByIdParent($forum->getId()));
						$this->treeObject[] = $forum;
					}
				}
				else
				{
					return;
				}
			}
			
			$this->loaded_by_parent = true;
			$this->initCollection();
		}	
		else
		{
			if($this->treeObject)
			{
				for($a=0; count($this->treeObject) > $a; $a++)
				{
					$this->setTopics($this->treeObject[$a]);			
				}
			}
		}
	}
	
	private function setTopics($object_forum)
	{
		if($topics = $object_forum->getChilds())
		{
			foreach($topics as $val)
			{
				$this->setPosts($object_forum, $val);
			}
		}
	}
	
	private function setPosts($object_forum, $object_topic)
	{
		$posts = $this->getPostsByParentId($object_topic->getId());
		if($posts)
		{
			foreach($posts as $val)
			{
				$this->_objectsCollection[$object_topic->getId() . '_' .$val->getId()] = new Varien_Object;
				$data = array
				(
					'title'       => $this->__getTitle($object_forum, $object_topic, $val),
					'url'   	  => $this->__getUrl($val),
					'postContent' => $this->__getPostContent($val),
					'createdTime' => $val->getCreated_time()
				);
				$this->_objectsCollection[$object_topic->getId() . '_' .$val->getId()]->setData($data);
			}
		}
	}
	
	private function getChildsByIdParent($_parent_id)
	{
		$c = Mage::getModel('forum/topic')->getCollection();
		$c->getSelect()->where('parent_id=?', $_parent_id)->where('status=1');
		if($c->getSize())
		{
			return $c;
		}
		else
		{
			return false;
		}
	}
	
	private function getObjectById($_id)
	{
		$o = Mage::getModel('forum/topic')->load($_id);
		if($o->getStatus() == 1 && $o->getId())
		{
			return $o;	
		}
	}
	
	private function getIsCategory($obj)
	{
		return $obj->getIs_category();
	}
	
	private function getPostsByParentId($_parent_id)
	{
		$c = Mage::getModel('forum/post')->getCollection();
		$c->setCurPage(1);
		$c->setPageSize($this->display_posts);
		$c->setOrder('created_time', 'desc');
		$c->getSelect()->where('parent_id=?', $_parent_id)->where('status=1');
		if($c->getSize())
		{
			return $c;
		}
	}
	
	private function __getTitle($parent_forum, $parent_topic, $object_post)
	{
		return $this->__('Forum') . ' "' . $parent_forum->getTitle() . '", ' . $this->__('Topic') . ' "' . $parent_topic->getTitle() . 
		'", ' . $this->__('Post') . ' ' . $this->__('ID') . ' ' . $object_post->getId();
	}
	
	private function __getUrl( $post )
	{
		return Mage::helper('forum/rss')->__getUrl( $post );	
	}
	
	private function __getPostContent($object_post)
	{
		return $object_post->getPost();
	}
}

?>