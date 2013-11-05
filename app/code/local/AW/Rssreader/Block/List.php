<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Rssreader
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
class AW_Rssreader_Block_List extends Mage_Core_Block_Template {
	public $_articles = array();
	
	public function __construct(){
		parent::__construct();		
		# getting default settings	
	}
	
	public function showSummary(){		
		if (trim(strtolower($this->show_summary)) == 'yes' || $this->show_summary == 1){
			return true;
		}
		else {
			return false;
		}
	}
	
	public function enableLinks(){
		if (trim(strtolower($this->enable_links)) == 'yes' || $this->enable_links == 1){
			return true;
		}
		else {
			return false;
		}
	}
	
	public function showDate(){
		if (trim(strtolower($this->show_date)) == 'yes' || $this->show_date == 1){
			return true;
		}
		else {
			return false;
		}
	}
	
	public function setOption($name, $value){
		$this->$name = $value;
	}
	
	public function getFeed(){
		$settings = Mage::helper('rssreader')->getSettings();
		foreach ($settings as $name => $value){
			if (!$this->$name){
				$this->$name = $value;
			}
		}	
		$feedXML = Mage::helper('rssreader')->fetchFeedXML($this->feed_url, $this->enable_cache, $this->cache_lifitime);

        if (!$feedXML) return;
		return $this->parseFeed($feedXML);
	}
	
	private function parseFeed($feedXML){		
		try
        { $xml = new SimpleXmlElement($feedXML); }
        catch (Exception $ex)
        { return; }
        
		foreach ($xml->channel->item as $item) {
			$article = array();
			$article['title'] = $item->title;
			$article['link'] = $item->link;
			$article['pubDate'] = $item->pubDate;
			$article['timestamp'] = strtotime($item->pubDate);
			$article['dateTime'] = date($this->date_format, strtotime($item->pubDate));
			
			$numwords = $this->summary_max_words;
			if ($numwords > 0) {
				preg_match("/([\S]+\s*){0,$numwords}/", $item->description, $regs);
				$shortdesc = trim($regs[0]);
			}
			else{
				$shortdesc = $item->description;
			}			
			
			$numchars = $this->summary_max_chars;
			if (($numchars > 0) and (strlen($shortdesc)) > $numchars) $shortdesc = preg_replace('|\s+\S+\s*$|', ' ...', substr($shortdesc, 0, $numchars));
			
		   $article['description'] = (string) trim($shortdesc);			
			$this->_articles[] = $article;
			if (count($this->_articles) >= $this->articles_number) {
				break;
			}
		}
		return $this->_articles;
	}
}