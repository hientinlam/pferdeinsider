<?php 
require_once '../app/Mage.php';
Mage::app();
$product=array();
$productmodel=Mage::getModel('catalog/product')->load($_GET['pid']);
$productmodel->setName('new product');
$productmodel->setMemberList('59');
$productmodel->save();

?>