<?php
class Display_Allmembers_Model_Mysql4_Allmembers extends Mage_Core_Model_Mysql4_Abstract
{
     public function _construct()
     {
         $this->_init('allmembers/allmembers', 'id');
     }
}