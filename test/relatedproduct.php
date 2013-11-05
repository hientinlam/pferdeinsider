<?php 
require_once '../app/Mage.php';
Mage::app();
 $memberproducts = Mage::getModel('catalog/product')->getCollection()->addFieldToFilter('member_list',Array('eq'=>59));
 $memberdata=$memberproducts->getData();
 $prd = Mage::getModel('catalog/product')->load(144);
 $i=0;

 foreach($memberdata as $memberpro)
 {
  
      $param[$memberpro['entity_id']]=  array('position'=>$i);
     $i++;
 }
/*$prd = Mage::getModel('catalog/product')->load(143);
    $param = array(
           144=>array(
                  'position'=>0
            ),
          145=>array(
                  'position'=>1
            )
        );*/
   //echo "<pre>";print_r($param);die('hello');
      $prd->setRelatedLinkData($param)->save();
?>