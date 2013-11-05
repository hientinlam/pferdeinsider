<?php
class MW_Dailydeal_Model_Dailydealactive extends Mage_Core_Model_Abstract
{
    public function _construct(){
        parent::_construct();
        $this->_init('dailydeal/dailydealactive');
	}	
}