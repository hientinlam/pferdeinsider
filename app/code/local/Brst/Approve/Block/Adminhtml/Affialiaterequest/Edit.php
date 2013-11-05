<?php
class Brst_Approve_Block_Adminhtml_Affialiaterequest_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {  
        $this->_blockGroup = 'brst_approve';
        $this->_controller = 'adminhtml_affialiaterequest';
     
        parent::__construct();
     
        $this->_updateButton('save', 'label', $this->__('Save Affialiate'));
        $this->_updateButton('delete', 'label', $this->__('Delete Affialiate'));
    }  
     
    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {  
        if (Mage::registry('brst_approve')->getId()) {
            return $this->__('Edit Affialiate');
        }  
        else {
            return $this->__('New Affialiate');
        }  
    }  
}