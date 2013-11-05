<?php
/**
 * Mageplace Facebook Connect
 *
 * @category    Mageplace_Facebook
 * @package     Mageplace_Facebook_Connect
 * @copyright   Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

 if(Mage::helper('facebookconnect/version')->isEE()) {
     class Mageplace_Facebook_Connect_Helper_Data extends Mageplace_Facebook_Connect_Helper_Enterprise
    {	
    }
} else {	
    class Mageplace_Facebook_Connect_Helper_Data extends Mageplace_Facebook_Connect_Helper_Community
    {	
    }
}
 
 

