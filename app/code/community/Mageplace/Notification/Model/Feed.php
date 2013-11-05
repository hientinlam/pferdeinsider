<?php

class Mageplace_Notification_Model_Feed extends Mage_Core_Model_Abstract
{

	const XML_USE_HTTPS_PATH		= 'mageplace_notification/feed/use_https';
	const XML_FEED_URL_PATH			= 'mageplace_notification/feed/url';
	const XML_FREQUENCY_PATH		= 'mageplace_notification/feed/check_frequency';
	const XML_FREQUENCY_ENABLE		= 'mageplace_notification/feed/enabled';
	const XML_LAST_UPDATE_PATH		= 'mageplace_notification/feed/last_update';
	const XML_LIST_SOCIAL_BUNDLE	= 'mageplace_notification/feed/socialbundle';

	public static function check()
	{
		if (!Mage::getStoreConfig(self::XML_FREQUENCY_ENABLE)) {
			return;
		}
		return Mage::getModel('mageplace_notification/feed')->checkUpdate();
	}

	public function getFrequency()
	{
		return Mage::getStoreConfig(self::XML_FREQUENCY_PATH) * 3600;
	}

	public function getLastUpdate()
	{
		return Mage::app()->loadCache('mageplace_notifications_lastcheck');
	}

	public function setLastUpdate()
	{
		Mage::app()->saveCache(time(), 'mageplace_notifications_lastcheck');
		return $this;
	}

	public function getFeedData($replace)
	{
		$url = $this->getFeedUrl($replace);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 2);
		$data = curl_exec($ch);
		curl_close($ch);

		if ($data === false) {
			return false;
		}

		try {
			$xml = new SimpleXMLElement($data);
		} catch (Exception $e) {
			return false;
		}
		return $xml;
	}

	public function getDate($rssDate)
	{
		return gmdate('Y-m-d H:i:s', strtotime($rssDate));
	}

	public function getFeedUrl($replace)
	{
		$this->_feedUrl = (Mage::getStoreConfigFlag(self::XML_USE_HTTPS_PATH) ? 'https://' : 'http://')
				. Mage::getStoreConfig(self::XML_FEED_URL_PATH);
		$this->_feedUrl = str_replace('xxx', $replace, $this->_feedUrl);
		return $this->_feedUrl;
	}

	public function checkUpdate()
	{
		if (($this->getFrequency() + $this->getLastUpdate()) > time()) {
			return $this;
		}
		$this->checkModules();
	}

	public function checkModules()
	{

		$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array) $modules;
		$no_bundle = array();
		$bundle = array();
		$socialbundle = @explode(',', Mage::getStoreConfig(self::XML_LIST_SOCIAL_BUNDLE));
		foreach ($socialbundle as $key) {
			if (Mage::getStoreConfig('mageplace_notification/feed/' . $key)) {
				$modul_list = explode(',', Mage::getStoreConfig('mageplace_notification/feed/' . $key));
				$bundle[$key] = false;
				$intersect = array_intersect_key(array_flip($modul_list), $modulesArray);
				if (count($modul_list) == count($intersect))
					$bundle[$key] = true;
				else
					$$key = $intersect;
			}else {
				$no_bundle[$key] = false;
				if (array_key_exists($key, $modulesArray))
					$no_bundle[$key] = true;
			}
		}

		$socialbundle = true;
		foreach ($bundle as $key => $value) {
			if (!$value)
				$socialbundle = false;
		}

		foreach ($no_bundle as $key => $value) {
			if (!$value)
				$socialbundle = false;
		}

		if ($socialbundle)
			$this->updateNoties('socialbundle');
		else {
			foreach ($no_bundle as $key => $value) {
				if ($value) {
					$aux = explode('_', $key);
					$count = count($aux);
					$link = '';
					for ($i = 1; $i <= $count - 1; $i++) {
						$link = $link . strtolower($aux[$i]);
					}
					$this->updateNoties($link);
				}
			}

			foreach ($bundle as $key => $value) {
				if ($value)
					$this->updateNoties($key);
				else {
					foreach ($$key as $keys => $val) {
						$aux = explode('_', $keys);
						$count = count($aux);
						$link = '';
						for ($i = 1; $i <= $count - 1; $i++) {
							$link = $link . strtolower($aux[$i]);
						}
						$this->updateNoties($link);
					}
				}
			}
		}
	}

	public function updateNoties($replace)
	{
		$feedData = array();
		$feedXml = $this->getFeedData($replace);

		if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
			foreach ($feedXml->channel->item as $item) {
				$feedData[] = array(
					'severity' => (int) $item->severity ? (int) $item->severity : 3,
					'date_added' => $this->getDate((string) $item->pubDate),
					'title' => (string) $item->title,
					'description' => (string) $item->description,
					'url' => (string) $item->link,
				);
			}

			if ($feedData) {
				Mage::getModel('adminnotification/inbox')->parse(array_reverse($feedData));
			}
		}
		$this->setLastUpdate();
		return $this;
	}

}
