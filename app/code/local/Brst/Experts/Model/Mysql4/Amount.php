<?php
class Brst_Experts_Model_Mysql4_Amount extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {  
        $this->_init('brst_experts/amount', 'id');
    }  
}