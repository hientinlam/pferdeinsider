<?php
require_once '../app/Mage.php';
Mage::app();
$collection       = Mage::getModel('brst_experts/amount')->getCollection()
                    //
                    //->addFieldToFilter(array(array('created_at'=>array('gt'=>'18/04/2013','lt'=>'23/05/2013'))));
                    //->addFieldToFilter('created_at',array("gt" =>'18/04/2013'));
                    ->addFieldToFilter('created_at',array("gt" =>'23/05/2013'));
                    //->addFieldToFilter('affiliate_name', array('eq'=>''))
                    //->addFieldToFilter('expert_name', array('like'=>$expertname))->setPageSize($pageSize)
                    //->setCurPage($currentPage);

                    echo "<pre>";print_r($collection->getData());
                    ?>