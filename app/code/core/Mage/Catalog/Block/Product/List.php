<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product list
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_List extends Mage_Catalog_Block_Product_Abstract {

    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';

    /**
     * Product Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_productCollection;

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection() {
        $search = $this->getRequest()->getParam('type');
        $origprice = $this->getRequest()->getParam('price');
        $datevalue = $this->getRequest()->getParam('post');
        $pricevalues = explode('-', $origprice);
        $gtprice = $pricevalues[0];
        $ltprice = $pricevalues[1];
        $popularvalue = $this->getRequest()->getParam('popular');
        $ratevalue = $this->getRequest()->getParam('rate');


        if ($search == NULL) {
            if (is_null($this->_productCollection)) {
                $layer = $this->getLayer();
                /* @var $layer Mage_Catalog_Model_Layer */
                if ($this->getShowRootCategory()) {
                    $this->setCategoryId(Mage::app()->getStore()->getRootCategoryId());
                }

                // if this is a product view page
                if (Mage::registry('product')) {
                    // get collection of categories this product is associated with
                    $categories = Mage::registry('product')->getCategoryCollection()
                            ->setPage(1, 1)
                            ->load();
                    // if the product is associated with any category
                    if ($categories->count()) {
                        // show products from this category
                        $this->setCategoryId(current($categories->getIterator()));
                    }
                }

                $origCategory = null;
                if ($this->getCategoryId()) {
                    $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
                    if ($category->getId()) {
                        $origCategory = $layer->getCurrentCategory();
                        $layer->setCurrentCategory($category);
                    }
                }
				$productFilter = $layer->getProductCollection()->addStoreFilter()
                    ->addAttributeToSelect('*');
				if (!empty($origprice)) {
					
					$productFilter->addAttributeToFilter('price', array('gteq' => (int)$gtprice));
					$productFilter->addAttributeToFilter('price', array('lteq' => (int)$ltprice));
				}
				if (!empty($datevalue))
					$productFilter = $productFilter->addAttributeToSort('created_at', ($datevalue == 'recent')?'DESC':'ASC');

                if(!empty($popularvalue))
                    $productFilter = $productFilter->sortByOrdersQty(($popularvalue == 'less')?'ASC':'DESC');

                if(!empty($ratevalue))
                    $productFilter = $productFilter->sortByReview($ratevalue == 'top'?'DESC':'ASC');

				$this->_productCollection = $productFilter;
                //$this->_productCollection = $layer->getProductCollection();

                $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

                if ($origCategory) {
                    $layer->setCurrentCategory($origCategory);
                }
            }
            print_r((string)$this->_productCollection->getSelect());
            return $this->_productCollection;
        } else if ($search != Null || $search != '') {
            $layer = $this->getLayer();
            if (Mage::registry('product')) {
                // get collection of categories this product is associated with
                $categories = Mage::registry('product')->getCategoryCollection()
                        ->setPage(1, 1)
                        ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
                if ($category->getId()) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                }
            }
            $productFilter = $layer->getProductCollection()->addStoreFilter()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('product_material', array('eq' => $search));
            if ($origprice != NULL || $origprice != '') {
                
				$productFilter->addAttributeToFilter('price', array('gteq' => (int)$gtprice));
                $productFilter->addAttributeToFilter('price', array('lteq' => (int)$ltprice));
//echo $productFilter->getSelect();die;
			}
			if ($datevalue != NULL || $datevalue != ''){
				if ($datevalue == 'recent') {
					$productFilter = $productFilter->addAttributeToSort('price', 'ASC');
				} else {
					$productFilter = $productFilter->addAttributeToSort('price','DESC');
				}
				
			}
			$this->_productCollection = $productFilter;
            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }

            return $this->_productCollection;
        }
    }

    /**
     * Get catalog layer model
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer() {
        $layer = Mage::registry('current_layer');
        if ($layer) {
            return $layer;
        }
        return Mage::getSingleton('catalog/layer');
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection() {
        return $this->_getProductCollection();
    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode() {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml() {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->_getProductCollection();

        // use sortable parameters

        if ($orders = $this->getAvailableOrders()) {
            $toolbar->setAvailableOrders($orders);
        }
        if ($sort = $this->getSortBy()) {
            $toolbar->setDefaultOrder($sort);
        }
        if ($dir = $this->getDefaultDirection()) {
            $toolbar->setDefaultDirection($dir);
        }
        if ($modes = $this->getModes()) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        Mage::dispatchEvent('catalog_block_product_list_collection', array(
            'collection' => $this->_getProductCollection()
        ));

        $this->_getProductCollection()->load();

        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getToolbarBlock() {
        if ($blockName = $this->getToolbarBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
        return $block;
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml() {
        return $this->getChildHtml('additional');
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml() {
        return $this->getChildHtml('toolbar');
    }

    public function setCollection($collection) {
        $this->_productCollection = $collection;
        return $this;
    }

    public function addAttribute($code) {
        $this->_getProductCollection()->addAttributeToSelect($code);
        return $this;
    }

    public function getPriceBlockTemplate() {
        return $this->_getData('price_block_template');
    }

    /**
     * Retrieve Catalog Config object
     *
     * @return Mage_Catalog_Model_Config
     */
    protected function _getConfig() {
        return Mage::getSingleton('catalog/config');
    }

    /**
     * Prepare Sort By fields from Category Data
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Block_Product_List
     */
    public function prepareSortableFieldsByCategory($category) {
        if (!$this->getAvailableOrders()) {
            $this->setAvailableOrders($category->getAvailableSortByOptions());
        }
        $availableOrders = $this->getAvailableOrders();
        if (!$this->getSortBy()) {
            if ($categorySortBy = $category->getDefaultSortBy()) {
                if (!$availableOrders) {
                    $availableOrders = $this->_getConfig()->getAttributeUsedForSortByArray();
                }
                if (isset($availableOrders[$categorySortBy])) {
                    $this->setSortBy($categorySortBy);
                }
            }
        }

        return $this;
    }

}
