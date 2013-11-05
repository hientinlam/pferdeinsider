<?php
class Brst_Member_Block_Adminhtml_Payment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {
        $this->_blockGroup = 'brst_member';
        $this->_controller = 'adminhtml_payment';
     
        parent::__construct();
     
        $this->_updateButton('save', 'label', $this->__('Save Payment'));
        $this->_updateButton('delete', 'label', $this->__('Delete Payment'));
    }
     
    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('brst_member')->getId()) {
            return $this->__('Edit Payment');
        }  
        else {
            return $this->__('New Payment');
        }
    }
}