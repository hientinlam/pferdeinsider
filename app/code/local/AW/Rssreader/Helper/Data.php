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
class AW_Rssreader_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getSettings(){
		return array(
			'articles_number'	  => Mage::getStoreConfig('rssreader/general/articles_number'),
			'show_summary'		  => Mage::getStoreConfig('rssreader/general/show_summary'),
			'summary_max_chars' => Mage::getStoreConfig('rssreader/general/summary_max_chars'),
			'summary_max_words' => Mage::getStoreConfig('rssreader/general/summary_max_words'),
			'show_date'			  => Mage::getStoreConfig('rssreader/general/show_date'),
			'date_format'		  => Mage::getStoreConfig('rssreader/general/date_format'),
			'enable_links'		  => Mage::getStoreConfig('rssreader/general/enable_links'),
			// 'enable_images'     => Mage::getStoreConfig('rssreader/general/enable_images'),
			'enable_cache'		  => Mage::getStoreConfig('rssreader/cache/enable_cache'),
			'cache_lifetime'	  => Mage::getStoreConfig('rssreader/cache/cache_lifetime')
		);
	}

	public function fetchFeedXML($feed_url, $enable_cache, $cache_lifetime){
		clearstatcache();
		try
        {
            if ($enable_cache){
                $cacheDir = Mage::getConfig()->getOptions()->getCacheDir() . '/aw-rssreader-';
                $fileName = $cacheDir . md5($feed_url);
                if (file_exists($fileName)){
                    $fileAge = time() - filectime($fileName);
                    if ($fileAge < $cache_lifetime){
                        $feedXML = $this->readFromCache($fileName);
                    }
                    else{
                        unlink($fileName); //cache is old
                        $feedXML = file_get_contents($feed_url);
                        $this->writeToCache($fileName, $feedXML);
                    }
                }
                else{
                    $feedXML = file_get_contents($feed_url);
                    $this->writeToCache($fileName, $feedXML);
                }
            }
            else{
                $feedXML = file_get_contents($feed_url);
            }
        }
        catch (Exception $ex)
        { return; }
		return $feedXML;
	}


	private function writeToCache($fileName, $fileData){
		$fp = fopen($fileName, 'w+');
		fwrite($fp, serialize($fileData));
		fclose($fp);
	}

	private function readFromCache($fileName){
		return unserialize(file_get_contents($fileName));
	}

}
