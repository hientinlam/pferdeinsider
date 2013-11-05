<?php
class MW_Dailydeal_Block_Adminhtml_Dealitems_Edit_Conf_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		try {
			$form = new Varien_Data_Form();//Mage::log($form,null,'form.log');
			$this->setForm($form);
			$fieldset = $form->addFieldset('dailydeal_form', array(
				'legend'	=> Mage::helper('dailydeal')->__('Deal Information')
			));
			$dailydeal = Mage::registry('dealitems_data');//var_dump($this->getRequest()->getParam('start'));die;			
			/**
			 * hidden fields
			 */
			$fieldset->addField('product_id', 'hidden', array(
				'name'	 => 'product_id',
				'value'	 => $dailydeal->getProductId()
			));
//			$fieldset->addField('url_key', 'hidden', array(
//				'name'		 => 'url_key',
//				'label'		 => Mage::helper('catalog')->__('url_key'),
//				'readonly'	 => true,
//				'value'		 => $url_key
//			));
//			$fieldset->addField('flag', 'hidden', array(
//				'name' => 'flag',
//				'value'=> $dailydeal->getFlag()
//			));
			/********************************************************
			 * product
			 ********************************************************/
			
			$fieldset->addField('product_sku', 'text', array(
				'name'				 => 'product_sku',
				'label'				 => Mage::helper('catalog')->__('Product Sku'),
				'readonly'			 => true,
				'class'				 => 'textbox-readonly',
			//	'value'				 => $sku,
				
			));
			$fieldset->addField('cur_product', 'text', array(
				'name'		 => 'cur_product',
				'label'		 => Mage::helper('catalog')->__('Product Name'),
				'title'		 => Mage::helper('catalog')->__('Go to Product Selection tab to choose a different product.'),
				'required'	 => true,
				'readonly'	 => true,
				'class'		 => 'textbox-readonly',
			//	'value'		 => $product_data->getName(),
			));

			/********************************************************/
			
			$fieldset->addField('product_price', 'text', array(
				'name'		 => 'product_price',
				'label'		 => Mage::helper('catalog')->__('Product Price') . MW_Dailydeal_Helper_Data::GetCurrencyCodeHtml(),
				'title'		 => Mage::helper('catalog')->__('Standard price before group sale discount'),
				'readonly'	 => true,
				'class'		 => 'textbox-readonly',
			//	'value'		 => round($product_data->getPrice(), 2),
			));

			/********************************************************/
			$fieldset->addField('product_qty', 'text', array(
				'name'		=> 'product_qty',
				'label'		=> Mage::helper('catalog')->__('Product Qty'),
				'readonly'	=> true,
				'class'		=> 'textbox-readonly',
			//	'value'		=> $qty,
				'after_element_html' => '<br /><br />
						</td>
					</tr>
					<tr class="system-fieldset-sub-head">
						<td colspan="2">
							<h4 style="border-bottom: 1px solid #CCCCCC;">Daily Deal Price</h4>'
				
			));
			
			
			/********************************************************
			 * discount
			 ********************************************************/
			
			$params = array(
				'name'		 => 'discount_type',
				'label'		 => Mage::helper('dailydeal')->__('Discount Type'),
				'onchange'	 => "Zizio_Groupsale_OnDiscountChanged();",
				//'value'		 => $dailydeal->getDiscountType(),
				'values'	 => array(
					array(
						'value'	 => 4,
						'label'	 => Mage::helper('dailydeal')->__('TO fixed price'),
					), 
					array(
						'value'	 => 1,
						'label'	 => Mage::helper('dailydeal')->__('BY percentage of the original price'),
					),
					array(
						'value'	 => 2,
						'label'	 => Mage::helper('dailydeal')->__('BY fixed amount'),
					),
					array(
						'value'	 => 3,
						'label'	 => Mage::helper('dailydeal')->__('TO percentage of the original price'),
					),
					
				), 
			);

			// ver 1.3 and down the existence of the disabled params disable the control :(
			if ($dealDisabled)
				$params['disabled'] = $dealDisabled;
			$fieldset->addField('discount_type', 'select', $params);

			/********************************************************/
			//var_dump($discount);die();
			$params = array(
				'name'				 => 'discount',
				'label'				 => Mage::helper('dailydeal')->__('Discount Amount'),
				'class'				 => 'required-entry validate-number validate-greater-than-zero',
				'required'			 => true,
			//	'value'				 => $discount,
				'onchange'			 => "Zizio_Groupsale_OnDiscountChanged();",
				//'after_element_html' => "<p class='note'><span>40% is min. discount recommended for Social Daily Deals</span></p>",
			);

			if ($dealDisabled)
				$params['disabled'] = $dealDisabled;
			$fieldset->addField('discount', 'text', $params);

			/********************************************************/
			//var_dump($dailydeal->getDailydealPrice(null, true));
			$params = array(			
				'name'				 => 'dailydeal_price',
				'label'				 => Mage::helper('dailydeal')->__('Deal Price') . MW_Dailydeal_Helper_Data::GetCurrencyCodeHtml(),
				'title'				 => Mage::helper('dailydeal')->__('Calculated product price during Social Daily Deal'),
				'class'				 => 'validate-greater-than-zero textbox-readonly',
				'readonly'           => 1,
				//'value'				 => $dailydeal->getDailydealPrice(null, true),

			);

			if ($dealDisabled)
				$params['disabled'] = $dealDisabled;

			$fieldset->addField('dailydeal_price', 'text', $params);
			
			$params = array(
				'name'		=> 'deal_qty',
				'label'		=> Mage::helper('dailydeal')->__('Deal Qty'),
				'title'		=> Mage::helper('dailydeal')->__('Deal Qty'),
				'class'		=> 'required-entry validate-number',
				'required'			 => true,
				'value'				 => $dailydeal->getDealQty(),
				'onchange'			 => "Zizio_Groupsale_DealqtyChanged();",			
				'after_element_html' => '<br /><br />
						</td>
					</tr>
					<tr class="system-fieldset-sub-head">
						<td colspan="2">
							<h4 style="border-bottom: 1px solid #CCCCCC;">Schedule</h4>
						</td>
					</tr>'
			);
			$fieldset->addField('deal_qty', 'text', $params);
			/********************************************************
			 * schedule
			 ********************************************************/
			$outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
			$outputFormat = "yyyy-MM-dd h:mm:ss a";
			$params = array(
				'name'				 => 'start_date',
				'title'				 => Mage::helper('dailydeal')->__('Active From Date'),
				'label'				 => Mage::helper('dailydeal')->__('Active From Date'),
				'image'				 => $this->getSkinUrl('images/grid-cal.gif'),
				'time'				 => true,
				'class'				 => 'required-entry',
				'required'			 => true,
				'format'			 => $outputFormat,
				'readonly'			 => true,
				'disabled'			=> false,
			);
			$fieldset->addField('start_date_time', 'date', $params);

		
			//Active to
			$params = array(
				'name'				 => 'end_date',
				'title'				 => Mage::helper('dailydeal')->__('Active To Date'),
				'label'				 => Mage::helper('dailydeal')->__('Active To Date'),
				'image'				 => $this->getSkinUrl('images/grid-cal.gif'),
				'time'				 => true,
				'class'				 => 'required-entry',
				'required'			 => true,
				//'value'				 => $dailydeal->getEndDateTime(),
				'format'			 => $outputFormat,
				'readonly'			 => true,
			'onchange'			 => "Zizio_Groupsale_ActiveChanged();",
			'disabled'			=> false,
				'after_element_html' => '<br /><br />
						</td>
					</tr>
					<tr class="system-fieldset-sub-head">
						<td colspan="2">
							<h4 style="border-bottom: 1px solid #CCCCCC;">Private Options</h4>
						</td>
					</tr>'
			);

			

			$fieldset->addField('end_date_time', 'date', $params);

		

			if ($dealDisabled)
				$params['disabled'] = $dealDisabled;
		/*****************************************
		 * Private Options
		 */
			$fieldset->addField('featured', 'select', array(
          'label'     => Mage::helper('dailydeal')->__('Featured'),
          'name'      => 'featured',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('dailydeal')->__('Yes'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('dailydeal')->__('No'),
              ),
          ),
          ));
          /*
          $fieldset->addField('disableproduct', 'select', array(
          'label'     => Mage::helper('dailydeal')->__('Disable product after deal ends'),
          'name'      => 'disableproduct',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('dailydeal')->__('Yes'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('dailydeal')->__('No'),
              ),
          ),
          ));
          $fieldset->addField('requiredlogin', 'select', array(
          'label'     => Mage::helper('dailydeal')->__('Required login'),
          'name'      => 'requiredlogin',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('dailydeal')->__('Yes'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('dailydeal')->__('No'),
              ),
          ),
          ));
          */
          $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('dailydeal')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('dailydeal')->__('Disabled'),
              ),
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('dailydeal')->__('Enabled'),
              ),


          ),
          ));
		 if ( Mage::getSingleton('adminhtml/session')->getDealitemsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getDealitemsData());
          Mage::getSingleton('adminhtml/session')->setDealitemsData(null);
      } elseif ( Mage::registry('dealitems_data') ) {
      //	Zend_Debug::dump(Mage::registry('dealitems_data')->getData());die();
      		$form->setValues(Mage::registry('dealitems_data')->getData());
      	
      		$dailydeal = Mage::registry('dealitems_data');//Zend_Debug::dump($dailydeal);die();
			//neu co flag cua dailyschedule thi se co trong $dailydeal
			$product_data = Mage::getModel('catalog/product');
			$dealDisabled = $dailydeal->isPending() === false;
			$sku = '';
			$url_key = '';
			if ($dailydeal->getProductId() > 0)
			{
				$product_data = $product_data->load($dailydeal->getProductId());//Zend_Debug::dump($product_data);die();
				$sku = $product_data->getSku();
				$url_key = $product_data->getUrlKey();
				$price = $product_data->getPrice();				
				//qty phai dung hinh thuc nay moi lay dc
				$stockInfo = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_data);
								$qty = $stockInfo->getQty();//var_dump($qty);die();
			}
			$form->getElement('product_price')->setValue($price);
			$form->getElement('product_qty')->setValue(round($qty,0));
			if ($this->getRequest()->getParam('start')){
				$start = $this->getRequest()->getParam('start');//var_dump(strtotime($start));
				$startendday = date('Y-m-d H:i:s',strtotime($start)+86399);//var_dump($startendday);die;
				//Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
				//var_dump($start);die;
				//var_dump($this->getRequest()->getParam('start')->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM)));die();
			$form->getElement('start_date_time')->setValue($start);
			$form->getElement('end_date_time')->setValue($startendday);
			
			}
			
			if ($dailydeal->getId())
				$expand_fields = true;
			else 
				$expand_fields = false;
			$discount = $dailydeal->getDiscount();
			$product_id = $dailydeal->getProductId();
			if ($discount > 0 && $product_id > 0)
			{
				$dailydeal_price = $dailydeal->calculateDailydealPrice($product_data->getPrice());
				$dailydeal->setDailydealPrice($dailydeal_price);
			}         
      }
		}catch (Exception $ex)
		{
			MW_Dailydeal_Helper_Data::LogError($ex);
		}
		return parent::_prepareForm();
	}
}