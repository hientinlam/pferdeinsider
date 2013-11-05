<?php
class Brst_Experts_Block_Adminhtml_Amount_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {
        $this->_blockGroup = 'brst_experts';
        $this->_controller = 'adminhtml_amount';
     
        parent::__construct();
     
        $this->_updateButton('save', 'label', $this->__('Save Amount'));
        $this->_updateButton('delete', 'label', $this->__('Delete Amount'));
    }
     
    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('brst_experts')->getId()) {
            return $this->__('Edit Amount');
        }  
        else {
            return $this->__('New Amount');
        }
    }
}