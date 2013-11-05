<?php
class MW_Dailydeal_Block_Adminhtml_Dealitems_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('dailydeal_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('dailydeal')->__('Daily Deal Information'));	  
	}
	
	protected function _beforeToHtml()
	{
		try {
			//$dailydeal = Mage::getModel('dailydeal/dailydeal');
			$dailydeal = Mage::registry('dealitems_data');
			//$dealDisabled = $dailydeal->isPending() === false;
			$helper_html = $this->getHelperHtml();//var_dump($dealDisabled);die();
			if (!$dealDisabled)
			{
				$this->addTab('list_product', array(
				'label' 	=> Mage::helper('dailydeal')->__('List Product'),
				'title'		=> Mage::helper('dailydeal')->__('List Product'),
				'content'	=> $helper_html.$this->getLayout()->createBlock('dailydeal/adminhtml_dealitems_edit_product_form')->toHtml()
					));
				$helper_html = "";
			}
			$this->addTab('conf_section', array(
			'label'		=> Mage::helper('dailydeal')->__('Deal Information'),
			'title'		=> Mage::helper('dailydeal')->__('Deal Information'),
			'content'	=> $helper_html.$this->getLayout()->createBlock('dailydeal/adminhtml_dealitems_edit_conf_form')->toHtml(),
			));
			
			} catch (Exception $ex){
			MW_Dailydeal_Helper_Data::LogError($ex);
		}
		return parent::_beforeToHtml();
	}
	private function getHelperHtml()
	{
		$selection_script = '<script type="text/javascript">
		String.prototype.trim = function()
		{
			return this.replace(/^\s+|\s+$/g,"");
		}
		
		function zizio_roundNumber(num, dec)
		{
			var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
			return result;
		}
		
		var max_qty_value, min_ppl_value;
		
		function Zizio_Groupsale_OnQtyLimitClicked ()
		{
			if (!$("qty_limit").checked) // no limit
			{
				// Set value to 0 and store previous value in memory in case user switches back later:
				if ($("max_qty").value != "Unlimited")
					max_qty_value = $("max_qty").value;
				$("max_qty").value = "Unlimited";
				// Disable:
				$("max_qty").disable();
				// Validation:
				$("max_qty").className = "validation-passed input-text";
			}
			else
			{
				// Retrieve previous value if user switched back on:
				if (($("max_qty").value == "Unlimited") && (typeof max_qty_value != "undefined"))
					$("max_qty").value = max_qty_value;
				// Enable:
				$("max_qty").enable();
				// Validation:
				$("max_qty").className = "required-entry validate-digits validate-greater-than-zero validate-zizio-max-qty input-text";
			}
		}
		
		function Zizio_Groupsale_OnGroupsaleToggleClicked ()
		{
			if ($("groupsale_toggle").checked) // groupsale mode
			{
				// Change deal type
				$("deal_type").value = "1";
				// Show deal tipping rule
				$("tipping_rule1").up().up().style.display = "";
				// Retrieve previous value if user switched back on:
				if (typeof min_ppl_value != "undefined")
					$("min_ppl").value = min_ppl_value;
				// Enable:
				$("min_ppl").enable();
				// Validation:
				$("min_ppl").className = "required-entry validate-digits validate-greater-than-zero input-text input-text";
			}
			else                                // daily deal mode
			{
				// Change deal type
				$("deal_type").value = "2";
				// Hide deal tipping rule
				$("tipping_rule1").up().up().style.display = "none";
				// Store value in memory in case user switches back later:
				min_ppl_value = $("min_ppl").value;
				$("min_ppl").value = "";
				// Disable:
				$("min_ppl").disable();
				// Validation:
				$("min_ppl").className = "validation-passed input-text";
			}
		}
		//Process price
		function Zizio_Groupsale_OnDiscountChanged ()
		{
			var oDiscountType = document.getElementById("discount_type");
			var oPrice = document.getElementById("product_price");
			var oDis = document.getElementById("discount");				
			var oDgp = document.getElementById("dailydeal_price");
			switch (oDiscountType.value)
			{
				case "2":
				// by fixed amount from the original price
				oDgp.value = oPrice.value-oDis.value;
				/*if(parseFloat(oPrice.value) <= parseFloat(oDgp.value)){
					alert("Please enter Deal price less than product price.");
					oDgp.value = 0;	
				}*/
				break;
				case "3":
				if(oDis.value >= 100){
					alert("Please enter a number less than 100 in field Discount Amount.");
					oDgp.value = 0;	
					}							
				else{
					// to percentage of the original price
					oDgp.value = oPrice.value*(oDis.value/100.0);					
					}						
				break;
				case "4":
				// to fixed price
				oDgp.value = oDis.value;
				if(parseFloat(oDgp.value) >= parseFloat(oPrice.value)){
							alert("Please enter Deal price less than product price.");
							oDgp.value = 0;	
						}
				break;
				case "1":
				default:
				// by percentage of the original price
				if(oDis.value >= 100){
					alert("Please enter a number less than 100 in field Discount Amount.");
					oDgp.value = 0;	
					}							
				else{
					oDgp.value = oPrice.value*(1-(oDis.value/100.0));	
					}						
			}
			oDgp.value = zizio_roundNumber(oDgp.value, 2);
		}
		function Zizio_Groupsale_DealqtyChanged ()
		{
			var productqty = document.getElementById("product_qty").value;
			var dealqty = document.getElementById("deal_qty").value;
			
			
			//alert(parseInt(dealqty));alert(parseInt(productqty));
			if (parseInt(productqty) != 0){
				if (parseInt(dealqty) > parseInt(productqty)) {
					dealqty.value = null;
					document.getElementById("labeldealqty").value = "Deal qty can not be more than product qty!";
					} else if (parseInt(dealqty) < parseInt(productqty))
					document.getElementById("labeldealqty").value = null;
			}
		}
		function Zizio_Groupsale_ActiveChanged()
		{
			var activefrom = document.getElementById("start_date_time").value;//alert(Date.parse(activefrom));
			var activeto = document.getElementById("end_date_time");
			var timefrom = Date.parse(activefrom);
			var timeto = Date.parse(activeto.value);  
			var labelactive = document.getElementById("labelactiveto");
			if (timefrom > timeto) {
				activeto.value = null;
				labelactive.value = "Active to can not earlier  than Active from!";
				} else if (timefrom < timeto){
				labelactive.value = null;
			}
		}
		function Zizio_Groupsale_VirtualProductOnGroupsale ()
		{
			alert("Can\'t use Group Sale option with a Virtual Product\nPlease choose another product or a different Deal Type.");
		}
		
		
		
		function Zizio_Groupsale_OnProductSelectGridCheckboxCheck (grid, event)
			{ 
			var trElement = Event.findElement(event, "tr");
			if (!trElement) return;
			var tds = Element.select(trElement, "td");
			if (!tds[0]) return;
			var selected_product_data = product_extra_data[tds[1].innerHTML.trim()];
			window.zizio_selected_product_data = selected_product_data;
			
			var product_id = document.getElementById("product_id");
			if (product_id)
				product_id.value = selected_product_data["id"];
			
			var cur_product = document.getElementById("cur_product");
			
			if (cur_product)
			{
				var product_name = selected_product_data["name"];
				var meta_keywords = document.getElementById("meta_keywords");
				if (meta_keywords)
				{
					meta_keywords.value = product_name.replace(/\s+/g, " ").replace(/^\s+(.*?)\s+$/, "$1").split(" ").join(",");
				}
				
				cur_product.value = product_name;
				//zizio_mng.FireEvent(cur_product, "change");
			}
			//alert("Zeus");
			// processing ... Van de chuyen tab
			var headerText = document.getElementById("headerText");
			if (headerText)
				headerText.innerHTML = "Social Daily Deal: " + selected_product_data["name"] + " (Pending)";
			
			var product_sku = document.getElementById("product_sku");
			if (product_sku)
				product_sku.value = selected_product_data["sku"];
			
			var product_qty = document.getElementById("product_qty");
			if (product_qty)
				product_qty.value = selected_product_data["qty"];
			
			var price = document.getElementById("product_price");
			if (price)
			{
				price.value = selected_product_data["price"];
				Zizio_Groupsale_OnDiscountChanged();
			}
			
			var url_key = document.getElementById("url_key");
			if (url_key)
				url_key.value = selected_product_data["url_key"];
			//chuyen tab tu List Product sang Deal information
			//dailydeal_tabsJsTabs.showTabContent(document.getElementById("dailydeal_tabs_conf_section"));
		}
		</script>
		
		<style type="text/css">
	input.textbox-readonly { color: #000000; background-color: #FAFAFA; border-color: #FAFAFA; }
	.columns .form-list td.value { width: 350px; }
		</style>
		';
		return $selection_script;
	}
}