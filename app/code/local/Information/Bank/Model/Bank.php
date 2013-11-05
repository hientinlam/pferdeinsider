<?php
class Information_Bank_Model_Bank extends Mage_Core_Model_Abstract
{
     public function _construct()
     {
         parent::_construct();
         $this->_init('bank/bank');
     }
}