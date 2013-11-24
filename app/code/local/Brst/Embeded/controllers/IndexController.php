<?php
class Brst_Embeded_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $helper = Mage::helper('embeded');
        $code = $this->getRequest()->getParam('code');
        switch($code) {
            case 'search':
                $queryString=$this->getRequest()->getParam('q');
                $collection = Mage::getModel('catalog/product')->getCollection();
                $collection->addAttributeToSelect('*')
                           ->addAttributeToFilter('name',array('like' => '%' . mysql_real_escape_string($queryString) . '%'));

                $this->loadLayout(array('default', 'EMBEDED_SEARCH'));
                if($collection->count() == 0) {
                    $emptyTextBlock = $this->getLayout()->createBlock('core/text');
                    $emptyTextBlock->setText($helper->__('There is no product found.'));
                    $this->getLayout()->getBlock('embeded-products-lists')->append($emptyTextBlock, 'empty.text');
                }
                $productListBlock = $this->getLayout()->getBlock('affiliate-products-search-result');
                $productListBlock->setProductCollection($collection);
                Mage::register('all_ids', $collection->getAllIds());

                $productListBlock->setListTitle($helper->__('Search Results For "%s"', $queryString));
                $productListBlock->setListType('search');
                break;
            case 'category':
                $optionId = $this->getRequest()->getParam('q');
                $collection = Mage::getModel('catalog/product')->getCollection();
                $collection->addAttributeToSelect('*')
                           ->addAttributeToFilter('video_category',$optionId);
                $this->loadLayout(array('default', 'EMBEDED_SEARCH'));
                if($collection->count() == 0) {
                    $emptyTextBlock = $this->getLayout()->createBlock('core/text');
                    $emptyTextBlock->setText($helper->__('There is no product found.'));
                    $this->getLayout()->getBlock('embeded-products-lists')->append($emptyTextBlock, 'empty.text');
                }
                $productListBlock = $this->getLayout()->getBlock('affiliate-products-search-result');
                $productListBlock->setProductCollection($collection);
                Mage::register('all_ids', $collection->getAllIds());

                $videoCategoryOptions = $helper->getVideoCategoryOptions();
                $title = '';
                foreach($videoCategoryOptions as $option) {
                    if($option['value'] == $optionId) {
                        $title = $option['label'];
                        break;
                    }
                }
                $productListBlock->setListTitle($title);
                $productListBlock->setListType('category');
                break;
            default:
                $resource = Mage::getSingleton('core/resource');
                $connection= $resource->getConnection('core_read');
                $query="select * from tbl_embedcode where embed_code='{$code}'";
                $result = $connection->query($query);
                $alldata = $result->fetch();
                $allProducts = unserialize($alldata['product_id']);
                Mage::register('embeded_products', $allProducts);
                Mage::getSingleton('core/session')->setAffilicateUrl($alldata['join_id']);

                $allIds = array();
                foreach($allProducts as $subList) {
                    if(is_array($subList)) {
                        $allIds = array_merge($allIds, $subList);
                    }
                }
                Mage::register('all_ids', $allIds);
                $this->loadLayout(array('default', 'EMBEDED_CODE'));
        }

		$this->renderLayout();
    }
    public function codeAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }
 
     public function findAction()
    {
               echo  $this->getRequest()->getParam('q');
               die('helo');
		$this->loadLayout();     
		$this->renderLayout();
    }
}