<?php
class MW_Dailydeal_Block_Sidebar_Calendar extends Mage_Core_Block_Template
{
	public function getWeeklydeal()
	{
		$tblCatalogStockItem = Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item');
		$weeklydeal = array();
		$consecutive7days = array();
		$i = 0;
		$m = date('m',Mage::getModel('core/date')->timestamp(time()));
		$d = date('d',Mage::getModel('core/date')->timestamp(time()));
		$Y = date('Y',Mage::getModel('core/date')->timestamp(time()));
		while ($i <7){
			
			array_push($consecutive7days, date('Y-m-d',mktime(0,0,0,$m,$d+$i,$Y)));
			$i++;
		}
		$weeklyCollection = Mage::getModel('dailydeal/dailydeal')->getCollection()
										->addFieldToFilter('status',1);
		$weeklyCollection->getSelect()->where("deal_qty > sold_qty");	
		$weeklyCollection->getSelect()->joinLeft(      
	       array('stock'=>$tblCatalogStockItem),     
	       'stock.product_id = main_table.product_id',      
	       array('stock.qty', 'stock.is_in_stock')      
     	);						
		//$weeklyCollection->getSelect()->where("stock.qty > 0");        
		$weeklyCollection->getSelect()->where("stock.is_in_stock = 1");																			
		//print_r($weeklyCollection->getSelect()->__toString());die;
										//->addFieldToFilter('start_date_time',array('to' => $currenttime));
		if (count($weeklyCollection) > 0){										
			foreach ($weeklyCollection as $weekly){ //echo date('Y-m-d',strtotime($weekly->getStartDateTime()));
				$Ystart = date('Y',strtotime($weekly->getStartDateTime()));
				$mstart = date('m',strtotime($weekly->getStartDateTime()));
				$dstart = date('d',strtotime($weekly->getStartDateTime()));
				$daysdeal = (strtotime($weekly->getEndDateTime()) - strtotime($weekly->getStartDateTime()))/86400;
				
				$formatdealend = date('Y-m-d',strtotime($weekly->getEndDateTime())); //echo $formatdealend.'<br/>';
				//$j = 0;
				$seekday = '';
				//Neu trong vong 7 ngay ma ko co ngay nao trung thi tat nhien se quit vong lap
				for ($j = 0;$j < $daysdeal+1;$j++ ){
					
						$seekday = date('Y-m-d',mktime(0,0,0,$mstart,$dstart+$j,$Ystart));
						if (in_array($seekday,$consecutive7days) && !in_array($seekday, $weeklydeal)){						
							array_push($weeklydeal, $seekday);
						}	
						//$j++;
						if ($seekday == $formatdealend){break;}
				}//die;
				
			}									
		}			
		return $weeklydeal;
	}
}