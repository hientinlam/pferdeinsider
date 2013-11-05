<?php
class Brst_Embeded_Model_Embeded extends Mage_Core_Model_Abstract
{
     public function _construct()
     {
         parent::_construct();
         $this->_init('embeded/embeded');
     }
}