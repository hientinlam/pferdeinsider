<?php
class Information_Bank_Model_Mysql4_Bank  extends Mage_Core_Model_Mysql4_Abstract
{
     public function _construct()
     {
         $this->_init('bank/bank', 'id');
     }
}