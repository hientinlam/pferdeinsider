<?php
class Brst_Comment_Model_Mysql4_Comment_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
 {
     public function _construct()
     {
         parent::_construct();
         $this->_init('comment/comment');
     }
}