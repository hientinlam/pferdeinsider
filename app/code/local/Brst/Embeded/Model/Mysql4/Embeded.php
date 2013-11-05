<?php
class Brst_Embeded_Model_Mysql4_Embeded extends Mage_Core_Model_Mysql4_Abstract
{
     public function _construct()
     {
         $this->_init('embeded/embeded', 'id');
     }
}