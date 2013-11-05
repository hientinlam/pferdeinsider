<?php
class Deal_Register_Model_Mysql4_Register_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {  
        $this->_init('deal_register/register');
    }  
}