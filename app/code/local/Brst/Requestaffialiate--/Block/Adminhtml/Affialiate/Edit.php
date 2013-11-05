<?php
class Brst_Requestaffialiate_Block_Adminhtml_Affialiate_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {  
        $this->_blockGroup = 'brst_requestaffialiate';
        $this->_controller = 'adminhtml_affialiate';
     
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
        if (Mage::registry('brst_requestaffialiate')->getId()) {
        
            return $this->__('Edit Affialiate');
        }  
        else {
            return $this->__('New Affialiate');
        }  
    }  
}