<?php
class Deal_Register_Model_Mysql4_Register extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {  
        $this->_init('deal_register/register', 'id');
    }  
}
