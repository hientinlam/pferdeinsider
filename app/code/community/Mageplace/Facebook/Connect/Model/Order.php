<?php
/**
 * Mageplace Facebook Connect
 *
 * @category    Mageplace_Facebook
 * @package     Mageplace_Facebook_Connect
 * @copyright   Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Facebook_Connect_Model_Order extends Varien_Object
{
	const BUNDLE = 'bundle';

	protected $_facebook;

	function post($order)
	{
		try {
#			Mage::log('POST ORDER 1:'.$order->getId());

			if(Mage::registry('facebookconnect_post')) {
				return $this;
			}
			
			$total_item = $order->getTotalItemCount();
			$order_items = $order->getItemsCollection();

#			Mage::log('POST ORDER 2:'.$order->getId());

			if(!count($order_items) || !$total_item) {
				return $this;
			}
			
#			Mage::log('POST ORDER 3:'.$order->getId());
			
			$items = array();
			foreach($order_items as $order_item) {
				if ($order_item->isDeleted() || $order_item->getData('parent_item_id')) {
					continue;
				}
				
#				Mage::log('POST ORDER 3_1:');Mage::log($order_item->getProductId());
				$product = $order_item->getProduct();
				if(!($product instanceof Mage_Catalog_Model_Product)) {
					$product = Mage::getModel('catalog/product')->load($order_item->getProductId());
				}

				$item = array();
				$item['name']	= $order_item->getData('name');
				if($qty = $order_item->getData('qty_ordered')) {}
				else if($qty = $order_item->getData('qty')) {}
				else $qty = 0;
				$item['qty']	= $qty;
				$item['url']	= is_object($product) ? Mage::helper('catalog/product')->getProductUrl($product) : null;
				//$item['thumb']	= $product->getThumbnailUrl(75,75);
				$item['thumb']	= is_object($product) ? $product->getThumbnailUrl(75,75) : null;
				//$item['price']	= Mage::helper('core')->currency($product->getPrice());
				$item['price']	= is_object($product) ? Mage::helper('core')->currency($product->getPrice()) : null;
				
				$items[] = $item;
			}

			$order_to_facebook				= new stdClass();
			$order_to_facebook->orderId		= $order->getId();
			$order_to_facebook->totalCount	= $total_item;
			$order_to_facebook->storeName	= Mage::app()->getStore()->getName();
			$order_to_facebook->storeUrl	= Mage::getBaseUrl();
			$order_to_facebook->products	= $items;


#			Mage::log('POST ORDER 4:'.$order->getId());Mage::log($order_to_facebook);

			$this->getFacebook()->facebookPost($order_to_facebook);
			
#			Mage::log('POST ORDER 5:'.$order->getId());

			Mage::register('facebookconnect_post', true);

		} catch(Exception $e) {
			Mage::logException($e);
		}
	}

    public function setFacebook($facebook)
    {
    	$this->_facebook = $facebook;

    	return $this;
    }

    public function getFacebook()
    {
    	return $this->_facebook;
    }
}
