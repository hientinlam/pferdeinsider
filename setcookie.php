<?php 
require_once 'app/Mage.php';
Mage::app();
Mage::getModel('core/cookie')->delete('awaffiliate-client');
?>