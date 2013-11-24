<?php

class Brst_Embeded_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_videoCategoryOptions = null;

    public function getVideoCategoryOptions()
    {
        if(is_null($this->_videoCategoryOptions)) {
            $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'video_category ');
            if ($attribute->usesSource()) {
                $options = $attribute->getSource()->getAllOptions(false);
            } else {
                $options = array();
            }

            $this->_videoCategoryOptions = $options;
        }

        return $this->_videoCategoryOptions;
    }
}