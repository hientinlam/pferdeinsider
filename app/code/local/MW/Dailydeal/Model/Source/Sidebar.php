<?php
class MW_Dailydeal_Model_Source_Sidebar
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Left')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('Right')),
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('None')),
        );
    }
}