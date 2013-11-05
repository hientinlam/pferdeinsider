<?php
class Brst_Calculate_Model_Mysql4_Discount extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {  
        $this->_init('brst_calculate/discount', 'id');
    }  
}