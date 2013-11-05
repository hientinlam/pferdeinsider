<?php
class MW_Dailydeal_Block_Adminhtml_Dealitems_Edit_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('product_selection');
		$this->setDefaultSort('id');
		$this->setTemplate('mw_dailydeal/dealitems/product/grid.phtml');
		$this->setUseAjax(true);//cho phep su dung ajax ngoai
		$this->setVarNameFilter('product_filter');
		$this->setRowClickCallback("Zizio_Groupsale_OnProductSelectGridCheckboxCheck");
	}
	protected function _beforeToHtml()
	{
		$this->setId($this->getId().'_'.$this->getIndex());
		$this->getChild('reset_filter_button')->setData('onclick', $this->getJsObjectName().'.resetFilter()');
		$this->getChild('search_button')->setData('onclick', $this->getJsObjectName().'.doFilter()');
		return parent::_beforeToHtml();
	}
	protected function _addColumnFilterToCollection($column)
	{
		if ($this->getCollection()) {
			if ($column->getId() == 'websites') {
				$this->getCollection()->joinField('websites',
					'catalog/product_website',
					'website_id',
					'product_id=entity_id',
				null,
					'left');
			}
		}
		return parent::_addColumnFilterToCollection($column);
	}
	protected function _prepareCollection()
	{
		try
		{
			$prod_types = array();
			$prod_types[] = 'simple';
			$prod_types[] = 'bundle';
			$prod_types[] = 'configurable';
			$prod_types[] = 'virtual'; // valid only for daily deals - client side js validation
			$prod_types[] = 'downloadable';
			$collection = Mage::getModel('catalog/product')->getCollection()
			->setStore($this->_getStore())
			->addAttributeToSelect('name')
			->addAttributeToSelect('small_image')
			->addAttributeToSelect('meta_description')
			->addAttributeToSelect('description')
			->addAttributeToSelect('category_ids')
			->addAttributeToSelect('sku')
			->addAttributeToSelect('qty')
			->addAttributeToSelect('price')
			->addAttributeToSelect('price_type')
			->addAttributeToSelect('url_key')
			->addAttributeToSelect('type_id')
			->addAttributeToSelect('attribute_set_id')
			->addFieldToFilter('visibility', array('gt'=>'1'))
			->addFieldToFilter('type_id', array('in'=>$prod_types))
			->joinField('qty',
				'cataloginventory/stock_item',
				'qty',
				'product_id=entity_id',
				'{{table}}.stock_id=1',
				'left');

			$store = $this->_getStore();
			if ($store->getId()) {
				//$collection->setStoreId($store->getId());
				$adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
				$collection->addStoreFilter($store);
				$collection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore);
				$collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
				$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
				$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
				$collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
			}
			else {
				$collection->addAttributeToSelect('price');
				$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
				$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
			}

			$this->setCollection($collection);

			parent::_prepareCollection();
			$this->getCollection()->addWebsiteNamesToResult();
		}
		catch (Exception $ex)
		{
			//MW_Dailydeal_Helper_Data::LogError($ex);
		}
		return $this;
	}

	protected function _afterLoadCollection()
	{
		// Filter out any bundled products that have a dynamic price (We don't handle those yet):
		// @todo: Do this earlier in the flow so collection paging and totals reflect that we removed products!
		$collection = $this->getCollection();
		$keys_to_remove = array();
		foreach ($collection as $key => $product)
		{
			if (($product->getTypeId() == "bundle") &&
				($product->getPriceType() == 0))
			{
				$keys_to_remove[] = $key;
			}
		}
		foreach ($keys_to_remove as $key)
		{
			$collection->removeItemByKey($key);
		}
	}
	
	public function getHtml()
	{
		try {
			$html = parent::getHtml();
			$collection = $this->getCollection();//var_dump($collection);die();
			$extra_data = array();
			$items = $collection->getItems();
			$types = Mage::getModel('catalog/product_type')->getOptionArray();
			foreach ($items as $item)
			{
				$type_id = $item->getTypeId();
				$extra_data[$item->getEntityId()] = array(
					"id"		=>	$item->getId(),
					"name"		=>	$item->getName(),
					"sku"		=>	$item->getSku(),
					"qty"		=>  round($item->getQty(),0),
					"url_key"	=>	$item->getUrlKey(),
					"desc" 		=>	$item->getDescription(),
					"meta_desc"	=>	$item->getMetaDescription(),
					"small_img"	=>	MW_Dailydeal_Helper_Data::GetProductImage($item, true),
					"price"		=>	round($item->getPrice(),2),
					"category_ids"	=>	implode(',', $item->getCategoryIds()),
					//"url"		=>	Mage::getModel('catalog/product')->load($item->getId())->getUrlPath(),
					"curr_sym"	=>	MW_Dailydeal_Helper_Data::GetBaseCurrencySymbol(),
					"curr_code"	=>	MW_Dailydeal_Helper_Data::GetBaseCurrencyCode(),
					"type_id"	=>	$type_id,
					"type"		=>	isset($types[$type_id]) ? $types[$type_id] : $type_id
				);
			}
			$json = MW_Dailydeal_Helper_Data::json_encode($extra_data);
						// don't add var before the product_extra_data variable, this function is
			// also called in Ajax, so we must overwrite the global variable.
			return sprintf("<script type='text/javascript'>product_extra_data = %s</script>", $json).$html;
		}catch (Exception $ex){
			MW_Dailydeal_Helper_Data::LogError($ex);
		}
	}
	
	protected function _prepareColumns()
	{
		try
		{
			

			$this->addColumn('prd_entity_id', array(
				'header'    => Mage::helper('sales')->__('ID'),
				'sortable'  => true,
//				'width'     => '30px',
				'index'     => 'entity_id'
				));
//o day filter cua product bi day sang cot id nen chinh do rong cua product
				$this->addColumn('prd_name', array(
				'header'    => Mage::helper('sales')->__('Product Name'),
				'index'     => 'name',
				'width'     => '30px',
				'column_css_class'=> 'name'
				));

				$sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
				->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
				->load()
				->toOptionHash();

				$this->addColumn('prd_sku', array(
				'header'    => Mage::helper('sales')->__('SKU'),
				//'width'     => '80px',
				'index'     => 'sku',
				'column_css_class'=> 'sku'
				));

				/*
				 $this->addColumn('type_id', array(
				'header'    => Mage::helper('sales')->__('type_id'),
				'width'     => '80px',
				'index'     => 'type_id',
				'column_css_class'=> 'type_id'
				));
				*/


				$this->addColumn('prd_price', array(
				'header'    => Mage::helper('sales')->__('Price'),
				'align'     => 'center',
				'type'      => 'price',
				'currency_code' => $this->_getStore()->getBaseCurrency()->getCode(),
				'rate'      => $this->_getStore()->getBaseCurrency()->getRate($this->_getStore()->getBaseCurrency()->getCode()),
				'index'     => 'price'
				));

				$this->addColumn('prd_qty',
				array(
					'header'=> Mage::helper('catalog')->__('Qty'),
				//	'width' => '100px',
					'type'  => 'number',
					'index' => 'qty',
				));

				$this->addColumn('prd_type',
				array(
					'header'=> Mage::helper('catalog')->__('Type'),
				//	'width' => '90px',
					'index' => 'type_id',
					'type'  => 'options',
					'options' => Mage::getModel('catalog/product_type')->getOptionArray(),
				));

				$this->addColumn('prd_visibility',
				array(
					'header'=> Mage::helper('catalog')->__('Visibility'),
				//	'width' => '70px',
					'index' => 'visibility',
					'type'  => 'options',
					'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
				));

				$this->addColumn('prd_status',
				array(
					'header'=> Mage::helper('catalog')->__('Status'),
				//	'width' => '70px',
					'index' => 'status',
					'type'  => 'options',
					'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
				));

				if (!Mage::app()->isSingleStoreMode()) {
					$this->addColumn('websites',
					array(
						'header'=> Mage::helper('catalog')->__('Websites'),
					//	'width' => '100px',
						'sortable'  => false,
						'index'     => 'websites',
						'type'      => 'options',
						'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
					));
				}
		}
		catch (Exception $ex)
		{
			MW_Dailydeal_Helper_Data::LogError($ex);
		}
		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		
//		 $this->setMassactionIdField('product_id');
//		 $this->getMassactionBlock()->setFormFieldName('product');
//
//		 $this->getMassactionBlock()->addItem('add', array(
//		 'label'    => $this->__('Select to Groupsale'),
//		 'url'      => $this->getUrl('///massAdd', array('_current'=>true)),
//		 ));
		 
		return $this;
	}
		//Use in variendGrid of file grid.phtml
	
	public function getGridUrl()
	{
		// This will invoke a call to the controller object GroupsaleController.php to
		// function name gridProductAction
		$ret = $this->getUrl('dailydeal/adminhtml_dealitems/gridProduct',
		array(
			'index' => $this->getIndex(),
			'_current'=>true,
		));
		return $ret;
	}
	protected function _getStore()
	{
		return Mage::app()->getStore($this->getRequest()->getParam('store'));
	}
}