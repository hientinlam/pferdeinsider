<?php
class Brst_Calculate_Block_Adminhtml_Discount_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {  
        $this->_blockGroup = 'brst_calculate';
        $this->_controller = 'adminhtml_discount';
     
        parent::__construct();
     
        $this->_updateButton('save', 'label', $this->__('Save Discount'));
        $this->_updateButton('delete', 'label', $this->__('Delete Discount'));
    }  
     
    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {  
        if (Mage::registry('brst_calculate')->getId()) {
            return $this->__('Edit Discount');
        }  
        else {
            return $this->__('New Discount');
        }  
    }  
}