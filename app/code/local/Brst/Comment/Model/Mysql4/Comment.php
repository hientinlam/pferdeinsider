<?php
class Brst_Comment_Model_Mysql4_Comment extends Mage_Core_Model_Mysql4_Abstract
{
     public function _construct()
     {
         $this->_init('comment/comment', 'id');
     }
}