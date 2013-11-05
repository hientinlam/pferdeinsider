<?php
class Related_Searchproduct_Block_Searchproduct extends Mage_CatalogSearch_Block_Result
{
     protected function _getProductCollection()
        {
            if (is_null($this->_productCollection)) {
               $this->_productCollection = $this->getListBlock()->getLoadedProductCollection();
               $productdata=$this->getListBlock()->getLoadedProductCollection();
            }
             $login = Mage::getSingleton( 'customer/session' )->isLoggedIn();
             if($login)
             {
                 /*
                  * FETCH ALL CUSTOMERS IDS
                  */
                $readconnection = Mage::getSingleton('core/resource')->getConnection('core_read');
                $readquery="select customer_id from tbl_searchproducts";
                $readresult=$readconnection->query($readquery);
                $readdata=$readresult->fetchAll();
                $cstids=array();
                foreach($readdata as $dataid)
                {
                    $cstids[]=$dataid['customer_id'];
                }

                
                $ids=array();
                $customer_detail=$customer = Mage::getModel('customer/session')->getCustomer();
                $customer_id=$customer_detail->getId();
                $productdata=$this->getListBlock()->getLoadedProductCollection()->getData();

                $count=0;
                foreach($productdata as $prodId)
                {
                    if($count<3)
                    {
                    $ids[]=$prodId['entity_id'];
                    $count++;
                    }

                }
                 $values=count($ids);
                 $implode_ids=implode(",", $ids);


                $query_text=$this->helper('catalogsearch')->getQueryText();
                $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
                 if($values > 0)
                 {
                        if(in_array($customer_id, $cstids))
                        {
                            $query="update tbl_searchproducts set product_id='$implode_ids', querytext='$query_text' where customer_id=$customer_id";
                        }
                        else
                        {
                            $query="insert into tbl_searchproducts(customer_id,product_id,querytext) values('$customer_id','$implode_ids','$query_text')";
                        }
                        $result=$connection->query($query);
                 }
             }
       
        return $this->_productCollection;     
    }
 
}