<?php

/**
 * Mageplace Facebook Connect
 *
 * @category    Mageplace_Facebook
 * @package     Mageplace_Facebook_Connect
 * @copyright   Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */
class Mageplace_Facebook_Connect_Model_Connect extends Mageplace_Facebook_Core_Model_Facebook
{
	const STREAM_PUBLISH		= 'stream.publish';
	const FQL_QUERY				= 'fql.query';
	const API_ME				= '/me';
	const API_ME_FEED			= '/me/feed';
	const SCOPE					= 'email,publish_stream';
	const CONNECT_URL			= 'https://www.facebook.com/dialog/oauth/?client_id=%1$s&redirect_uri=%2$s&state=%3$s&scope=%4$s&display=%5$s';
	const ACCESS_URL			= 'https://graph.facebook.com/oauth/access_token?client_id=%1$s&client_secret=%2$s&redirect_uri=%3$s&code=%4$s';
	const DISPLAY_MODE_POPUP	= 'popup';
	const FBCONNECTTYPE			= 'fbconnecttype';

	public function init($reset = null, $check_cookie=true, $log=true)
	{
		parent::init($reset = null);		
		if (!$check_cookie || ($key = $this->getAppId()) && array_key_exists('fbsr_' . $key, $_COOKIE)) {
			try {
				$me = $this->getFacebook()->api(self::API_ME);
				$this->setUserInfo($me);
			} catch (FacebookApiException $e) {
				Mage::logException($e);
			}
		}

		return $this;
	}
	
	public function getConnectUrl($mode=self::DISPLAY_MODE_POPUP)
	{
		$facebook = $this->getFacebook();
		
		$redirect = Mage::getUrl('facebookconnect/login', array('_secure' => Mage::getModel('core/url')->getSecure()));
		if($mode != self::DISPLAY_MODE_POPUP) {
			$redirect = $redirect . (strpos($redirect, '?') !== FALSE ? '&' : '?') . self::FBCONNECTTYPE.'=page';
		}

		$params = array(
			'scope'			=> self::SCOPE,
			'redirect_uri'	=> $redirect,
		);
		
		if($mode) {
			$params['display'] = $mode;
		}
		
		return $facebook->getLoginUrl($params);
	}
	
	public function getFacebookAfterLogin()
	{
		$facebook = $this->getFacebook();
		if($user = $facebook->getUser()) {
			$this->init(null, false);
			
			Mage::helper('facebookconnect')->getFacebook($this);
		}
		
		return $this;
	}

	public function facebookQuery($query)
	{
		return $this->getFacebook()
			->api(array(
				'method' => self::FQL_QUERY,
				'query' => $query
			));
	}

	public function facebookPost($order)
	{
		$facebookconnect_session = Mage::getSingleton('facebookconnect/session');

#		Mage::log('FACEBOOK POST ORDER 1:'.$order->orderId);

		if ($facebookconnect_session->checkPost($order->orderId)) {
			return $this;
		}

#		Mage::log('FACEBOOK POST ORDER 2:'.$order->orderId);

		$methodName = '_post';
		if ($order->totalCount > 1) {
			$methodName = '_stream';
		}

		$post = null;
		try {
			$post = $this->$methodName($order);

#			Mage::log('FACEBOOK POST ORDER 3:'.$order->orderId); Mage::log($post);
		} catch (FacebookApiException $e) {
			try {
				//Timeout easy check
				$post = $this->$methodName($order);
			} catch (FacebookApiException $e) {
				Mage::logException($e);
			}
		}

		$this->setData('post_request', $post);

		return $this;
	}

	protected function _post($order)
	{
		$params = array();
		$params['message'] = Mage::helper('facebookconnect')->__('I\'ve just purchased %1$d item(s) at %2$s - %3$s.', $order->totalCount, $order->storeName, $order->storeUrl);
		if (!empty($order->products[0])) {
			$orderProduct = $order->products[0];

			if (!empty($orderProduct['url'])) {
				$params['link'] = $orderProduct['url'];
			}

			if (!empty($orderProduct['name'])) {
				$params['name'] = $orderProduct['name'];
			}

			if (!empty($orderProduct['thumb'])) {
				$params['picture'] = $orderProduct['thumb'];
			}

			if (!empty($orderProduct['name']) && !empty($orderProduct['url'])) {
				if (!empty($orderProduct['price'])) {
					$params['description'] = Mage::helper('facebookconnect')->__('View %1$s for %2$s - %3$s.', $orderProduct['name'], strip_tags($orderProduct['price']), $orderProduct['url']);
				} else {
					$params['description'] = Mage::helper('facebookconnect')->__('View %1$s - %2$s.', $orderProduct['name'], $orderProduct['url']);
				}
			}
		}

		return $this->getFacebook()->api(self::API_ME_FEED, 'post', $params);
	}

	public function _stream($order)
	{
		$media = $description = array();
		$counter = 1;
		foreach ($order->products as $item) {
			if (!empty($item['name']) && !empty($item['price']) && !empty($item['url'])) {
				$description[] =  ($counter++) . ':' . Mage::helper('facebookconnect')->__('View %1$s for %2$s - %3$s.', $item['name'], strip_tags($item['price']), $item['url']);
			}

			if (!empty($item['thumb'])) {
				$media[] = array(
					'type' => 'image',
					'src' => $item['thumb'],
					'href' => $item['url']
				);
			}
		}

		$attachment = array(
			'name' => Mage::helper('facebookconnect')->__('Purchased items'),
			'caption' => implode('<center></center>', $description),
			'media' => $media
		);

		return $this->getFacebook()
			->api(
				array(
					'method' => self::STREAM_PUBLISH,
					'message' => Mage::helper('facebookconnect')->__('I\'ve just purchased %1$d item(s) at %2$s - %3$s.', $order->totalCount, $order->storeName, $order->storeUrl),
					'attachment' => $attachment
				)
			);
	}

	public function facebookPostSingup($customer)
	{
		$params = array();
		$params['link'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		$params['message'] = Mage::helper('facebookconnect')->getCfg(Mageplace_Facebook_Connect_Helper_Data::VAR_MESSAGE);
		$params['picture'] = Mage::helper('facebookconnect')->getCfg(Mageplace_Facebook_Connect_Helper_Data::VAR_IMAGE);
		$params['name'] = Mage::helper('facebookconnect')->getCfg(Mageplace_Facebook_Connect_Helper_Data::VAR_NAME);
		$params['description'] = Mage::helper('facebookconnect')->getCfg(Mageplace_Facebook_Connect_Helper_Data::VAR_DESCRIPTION);

		return $this->getFacebook()->api(self::API_ME_FEED, 'post', $params);
	}

	public function getFacebookLoginEventHtml()
	{
		$facebook = $this->getFacebook();
		if (!($facebook instanceof Facebook)) {
			return '';
		}

		ob_start();
		?>
		<script type="text/javascript">
			Event.observe(document, 'fbinit:ready', function(event){
				mpFBConnect.init("<?php echo $this->getConnectUrl(); ?>");
			});
			
			<?php if(Mage::getSingleton('facebookconnect/session')->getShowLoginPopup()) : ?>
			mpFBConnect.displayLoginWin = true;
			<?php Mage::getSingleton('facebookconnect/session')->setShowLoginPopup(false); ?>
			<?php endif; ?>
			
			mpFBConnect.facebookconnectform = '<form id="facebookconnectform" method="post" action="<?php echo Mage::getUrl('facebookconnect/login', array('_secure' => Mage::helper('facebookconnect')->isHttps())); ?>">' +
				'<input type="hidden" name="return" value="<?php echo base64_encode(Mage::helper('core/url')->getCurrentUrl()); ?>" />' +
				'<input type="hidden" name="<?php echo self::FBCONNECTTYPE; ?>" value="form" />' +
				'</form>';
			
		</script>
		<?php
		$html = ob_get_clean();

		return $html;
	}

}