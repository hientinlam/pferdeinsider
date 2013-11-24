<?php
class Brst_Embeded_Block_Embeded extends Mage_Core_Block_Template
{
	public function getDefaultDirection(){
		return 'asc';
	}
	public function getAvailableOrders(){
		return array('name'=> 'Name','position'=>'Position','children_count'=>'Sub Category Count');
	}
	public function getSortBy(){
		return 'name';
	}
	public function getToolbarBlock()
	{
		$block = $this->getLayout()->createBlock('embeded/toolbar', microtime());
		return $block;
	}
	public function getMode()
	{
		return $this->getChild('toolbar')->getCurrentMode();
	}

	public function getToolbarHtml()
	{
		return $this->getChildHtml('toolbar');
	}


    // ========================================================
    public function generateRecentProductsList()
    {
        $this->setListTitle($this->__('RECENT COURSES'));
        $this->setListType('recent');

        $productIds = Mage::registry('embeded_products');
        if(!empty($productIds) && !empty($productIds['recent'])) {
            $this->setProductCollection($productIds['recent']);
        } else {
            $this->setProductCollection(array());
        }
    }

    public function generatePopularProductsList()
    {
        $this->setListTitle($this->__('POPULAR COURSES'));
        $this->setListType('popular');

        $productIds = Mage::registry('embeded_products');
        if(!empty($productIds) && !empty($productIds['popular'])) {
            $this->setProductCollection($productIds['popular']);
        } else {
            $this->setProductCollection(array());
        }
    }

    public function generateTopratedProductsList()
    {
        $this->setListTitle($this->__('TOP RATED COURSES'));
        $this->setListType('toprated');

        $productIds = Mage::registry('embeded_products');
        if(!empty($productIds) && !empty($productIds['toprated'])) {
            $this->setProductCollection($productIds['toprated']);
        } else {
            $this->setProductCollection(array());
        }
    }

    public function getSimilarProducts()
    {
        $allIds = Mage::registry('all_ids');
        if(empty($allIds)) {
            return array();
        }
        foreach($allIds as $pid) {
            $_productObj = Mage::getModel('catalog/product')->load($pid);
            $relatedProductIds = $_productObj->getRelatedProductIds();
            if(!empty($relatedProductIds)) {
                return array_slice($relatedProductIds, 0, 5);
            }
        }

        return array();
    }
}