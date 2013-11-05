<?php 
require_once '../app/Mage.php';
Mage::app();
$pid=$_GET['pid'];
 $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
$query="delete from catalog_product_entity where entity_id =".$pid;
$result=$connection->query($query);
echo "successfully deleted";

?>