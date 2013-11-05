<?php
class Display_Allmembers_Model_Allmembers extends Mage_Core_Model_Abstract
{
     public function _construct()
     {
         parent::_construct();
         $this->_init('allmembers/allmembers');
     }
}