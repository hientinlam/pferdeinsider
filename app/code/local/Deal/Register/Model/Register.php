<?php
class Deal_Register_Model_Register extends Mage_Core_Model_Abstract
{
     public function _construct()
     {
         parent::_construct();
         $this->_init('register/register');
     }
}