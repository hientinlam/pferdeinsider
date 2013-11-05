<?php

class MW_Dailydeal_Helper_Sidebar extends Mage_Core_Helper_Abstract
{
    public function displayTodaydealLeft(){
    	if(Mage::getStoreConfig('dailydeal/general/sidebardeal')==1)
    		return "mw_dailydeal/sidebar/todaydeal.phtml";
    	else
    		return "";
    }
    
	public function displayActivedealLeft(){
    	if(Mage::getStoreConfig('dailydeal/general/sidebaractive')==1)
    		return "mw_dailydeal/sidebar/activedeal.phtml";
    	else
    		return "";
    }
    
	public function displayCalendarLeft(){
    	if(Mage::getStoreConfig('dailydeal/general/calendar')==1)
    		return "mw_dailydeal/sidebar/calendar.phtml";
    	else
    		return "";
    }
    
	public function displayTodaydealRight(){
    	if(Mage::getStoreConfig('dailydeal/general/sidebardeal',Mage::app()->getStore()->getStoreId())==2)
    		return "mw_dailydeal/sidebar/todaydeal.phtml";
    	else
    		return "";
    }
    
	public function displayActivedealRight(){
    	if(Mage::getStoreConfig('dailydeal/general/sidebaractive',Mage::app()->getStore()->getStoreId())==2)
    		return "mw_dailydeal/sidebar/activedeal.phtml";
    	else
    		return "";
    }
    
	public function displayCalendarRight(){
    	if(Mage::getStoreConfig('dailydeal/general/calendar',Mage::app()->getStore()->getStoreId())==2)
    		return "mw_dailydeal/sidebar/calendar.phtml";
    	else
    		return "";
    }
    
}
