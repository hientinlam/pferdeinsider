<?php
class AW_Affiliate_Block_Campaign_Embeded extends Mage_Core_Block_Template
{
    public function generateRecentProductsList()
    {
        $this->setListTitle($this->__('RECENT COURSES'));
        $this->setListType('recent');

        $productCollection = Mage::getResourceModel('reports/product_collection')
            ->addAttributeToSelect('*')
            ->setVisibility(array(2,3,4))
            ->setOrder('created_at', 'desc')
            ->setPageSize(6);

        $this->setProductCollection($productCollection);
    }

    public function generatePopularProductsList()
    {
        $this->setListTitle($this->__('POPULAR COURSES'));
        $this->setListType('popular');

        $productCollection = Mage::getResourceModel('reports/product_collection')
            ->addAttributeToSelect('*')
            ->setVisibility(array(2,3,4))
            ->addOrderedQty()
            ->setOrder('ordered_qty', 'desc')
            ->setPageSize(6);

        $this->setProductCollection($productCollection);
    }

    public function generateTopratedProductsList()
    {
        $this->setListTitle($this->__('TOP RATED COURSES'));
        $this->setListType('toprated');

        $productCollection = Mage::getResourceModel('reports/product_collection')
            ->addAttributeToSelect('*')
            ->setVisibility(array(2,3,4));

        $productCollection->joinField('rating_summary', 'review/review_aggregate', 'rating_summary', 'entity_pk_value=entity_id',  array('entity_type' => 1, 'store_id' => Mage::app()->getStore()->getId()), 'left');
        $productCollection->setOrder('rating_summary', 'desc');
        $productCollection->setPageSize(6);

        $this->setProductCollection($productCollection);
    }
}