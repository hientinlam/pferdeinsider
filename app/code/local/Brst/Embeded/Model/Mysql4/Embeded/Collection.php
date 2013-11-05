<?php
class Brst_Embeded_Model_Mysql4_Embeded_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
 {
     public function _construct()
     {
         parent::_construct();
         $this->_init('embeded/embeded');
     }
}