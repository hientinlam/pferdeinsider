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
 
class Simple_Forum_Model_Sitemap extends Mage_Core_Model_Abstract
{
	private $limit         = 5;
	
	private $collection    = false;
	
	/**
     * Init model
     */
    protected function _construct()
    {
        $this->_init('forum/topic');
    }

	public function ___initialize()
	{
		$this->___generateXml();
	}	
	
    public function ___generateXml()
    {
    	if(!$this->getAllowedSitemap() || !$this->checkUpdate())
    	{
			return;
		}
		try
		{
			$this->initCollection();
	        $io = new Varien_Io_File();
	        $io->setAllowCreateFolders(true);
	        $io->open(array('path' => $this->getPathToSiteMap()));
	        
	        $io->streamOpen($this->getFileSName());
	        $io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
	        $io->streamWrite('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
	        $date    = Mage::getSingleton('core/date')->gmtDate('Y-m-d');
			
	        $baseUrl = Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
	
	        /**
	         * Generate forum pages sitemap
	         */
	        $changefreq = (string)Mage::getStoreConfig('sitemap/forum/changefreq');
	        $priority   = (string)Mage::getStoreConfig('sitemap/forum/priority');
	        
			if(!is_array($this->collection))
	        {
				$io->streamWrite('</urlset>');
				$io->streamClose();
				return;
			}
	        
			foreach ($this->collection as $item) 
			{
	            $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
	                htmlspecialchars($item->getData('url')),
	                $date,
	                $changefreq,
	                $priority
	            );
	            $io->streamWrite($xml);
	        }
	        unset($collection);
	
	        $io->streamWrite('</urlset>');
	        $io->streamClose();
	
	        $this->___setSitemapTime();
	        //$this->save();
	    }
	    catch (Exception $e) 
		{
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        return $this;
    }
    
    private function checkUpdate()
    {
    	if (intval($this->___getSitemapTime() +  $this->getPeriod()) > time()) 
		{
            return false;
        }
        return true;
	}
    
    private function ___setSitemapTime()
    {
    	if(!Mage::getStoreConfig('forum/sitemap/update'))
    	{
			$c =   Mage::getModel('core/config_data');
			$c
				->setScope('default')
				->setPath('forum/sitemap/update')
				->setValue(time())
				->save();
		}
		else
		{
			Mage::getModel('core/config')->saveConfig('forum/sitemap/update', time() ); 
		}
	}
	
	private function ___getSitemapTime()
	{
		return Mage::getStoreConfig('forum/sitemap/update');
	}
	
	private function getPeriod()
	{
		return Mage::getStoreConfig('forum/sitemap/periodcreation') + 3600;
	}
    
    private function getFileSName()
    {
		return Mage::getStoreConfig('forum/sitemap/sitemapfilename');
	}
	
	private function getPathToSiteMap()
	{
		return Mage::getStoreConfig('forum/sitemap/path_to_sitemapfilename');
	}
	
	private function getAllowedSitemap()
	{
		return Mage::getStoreConfig('forum/sitemap/allowcreation');
	}
    
    private function initCollection()
    {
		$forums = $this->getObjectsByParentId(0);
		
		if($forums)
		{
			foreach($forums as $val)
			{
				$topics  = $this->getObjectsByParentId($val->getId());
				$this->setTopics($topics);
			}
		}
	}
	
	private function setTopics($collection)
	{
		if($collection)
		{
			foreach($collection as $val)
			{
				$all = $this->getPagesQuantity($val->getId());
				if($all)
				{
					while($all)
					{
						$this->collection[$val->getId() . '_' . $all] = new Varien_Object;
						$data = array('url' => Mage::helper('forum/sitemap')->__getUrl($val, $this->limit, $all));
						$this->collection[$val->getId() . '_' . $all]->setData($data);
						$all--;
					}
				}
			}
		}
	}
	
	private function getObjectsByParentId($_id)
	{
		$c = Mage::getModel('forum/topic')->getCollection();
		$c->getSelect()->where('parent_id=?', $_id)->where('status=1');
		if($c->getSize())
		{
			return $c;
		}
		else
		{
			return false;
		}
	}
	
	private function getPagesQuantity($topic_id)
	{
		$c = Mage::getModel('forum/post')->getCollection();
		$c->getSelect()->where('parent_id=?', $topic_id)->where('status=1');
		if($c->getSize())
		{
			$size  = $c->getSize();
			$total = ceil($size/$this->limit);
			return $total;
		}
	}
}
