<?php

/**
 * Mageplace Facebook Core
 *
 * @category    Mageplace_Facebook
 * @package     Mageplace_Facebook_Core
 * @copyright   Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */
class Mageplace_Facebook_Core_Model_Facebook extends Varien_Object
{

	const CONFIG_APPID = 'appId';
	const CONFIG_SECRET = 'secret';
	const CONFIG_COOKIE = 'cookie';
	const CONFIG_PERMS = 'scope';
	const PLUGINS_URL = 'http://www.facebook.com/plugins/';
	const CONNECT_BUTTON_TAG = 'fb:login-button';
	const LIKE_BUTTON_TAG = 'fb:like';
	const COMMENTS_BUTTON_TAG = 'fb:comments';
	const SUBSCRIBE_BUTTON_TAG = 'fb:subscribe';
	const PERMISSIONS_CONNECT = 'permissions_facebookconnect';

	public static $PERMISSIONS = array(
		self::PERMISSIONS_CONNECT => 'email,publish_stream',
	);
	protected $_facebook = null;

	public function init($reset = false)
	{
		if (is_null($this->getFacebook()) || $reset) {
			require_once Mage::getConfig()->getOptions()->getLibDir() . '/facebook/facebook.php';

			$facebook = new Facebook($this->getConfigData());

			$this->setFacebook($facebook);
		}

		return $this;
	}

	public function getConfigData()
	{
		if ($config_data = (array) $this->getData('config_data')) {
			return $config_data;
		} else {
			return array(
				self::CONFIG_APPID => $this->getAppId(),
				self::CONFIG_SECRET => $this->getSecret(),
					/* self::CONFIG_COOKIE		=> $this->getCookie() */
			);
		}
	}

	public function getFacebook()
	{
		return $this->_facebook;
	}

	public function setFacebook($facebook)
	{
		$this->_facebook = $facebook;
	}

	public function insertXmlnsParams($html)
	{
		if (!Mage::registry('mageplace_facebook_xmlns_inserted')) {
			/* $html = preg_replace('/(<html [^\>]*)/ism', '$1 xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"', $html); */
			$html = preg_replace('/(<html [^\>]*)/ism', '$1 xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#"', $html);
			Mage::register('mageplace_facebook_xmlns_inserted', true);
		}
		return $html;
	}

	public function getFacebookLocale()
	{
		$core_helper = new Mageplace_Facebook_Core_Helper_Data();
		//return ($locale = $this->getLocale()) ? $locale : Mage::app()->getLocale()->getDefaultLocale();
		return $core_helper->getCfg(Mageplace_Facebook_Core_Helper_Data::LANGUAGE);
	}	

	public function getFacebookHtml()
	{
		$facebook = $this->getFacebook();
		if (!($facebook instanceof Facebook)) {
			return '';
		}

		$html = '';
		if (!Mage::registry('mageplace_facebook_html')) {
			ob_start();
			?>
			<div id="fb-root"></div>
			<script  type="text/javascript">     
				(function(d){
					var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];     
					if (d.getElementById(id)) {return;}
					js = d.createElement('script'); js.id = id; js.async = true;
					js.src = "https://connect.facebook.net/<?php echo $this->getFacebookLocale(); ?>/all.js";
					ref.parentNode.insertBefore(js, ref);
				}(document));
			    
				window.fbAsyncInit = function() {
					FB.init({
						appId      : '<?php echo $facebook->getAppId(); ?>', 
						status     : true, 
						cookie     : true,
						xfbml      : true  
					}); 
					
										
			     
					Event.observe(window, 'load', function() {
						Event.fire(document, 'fbinit:ready',{});
					});
				};  
			</script>
			<?php
			$html = ob_get_clean();
			Mage::register('mageplace_facebook_html', true);
		}
		/*Event.observe(document, 'fbinit:ready', function(event){
					(function(d){
						var js;
						ref = d.getElementsByTagName('script')[0];     
						js = d.createElement('script');      
						js.async = true;
						js.src = "<?php echo $this->getShareButtonSrc(); ?>";
						ref.parentNode.insertBefore(js, ref);
					}(document));
		}); */
		return $html;
	}

	public function isMetaAdded($meta)
	{
		$metas = (array) Mage::registry('mageplace_facebook_meta');

		return in_array($meta, $metas);
	}

	public function addMeta($meta)
	{
		$metas = (array) Mage::registry('mageplace_facebook_meta');

		array_push($metas, $meta);

		Mage::unregister('mageplace_facebook_meta');

		Mage::register('mageplace_facebook_meta', $metas);
	}

	public function getShareButtonSrc()
	{
		//return 'http' . (Mage::getModel('core/url')->getSecure() ? 's' : '') . '://static.ak.fbcdn.net/connect.php/js/FB.Share';
	}

	public function getPluginsUrl()
	{
		return 'http' . (Mage::getModel('core/url')->getSecure() ? 's' : '') . '://www.facebook.com/plugins/';
	}

}