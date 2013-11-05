<?php
class Brst_Approve_Block_Adminhtml_Affialiaterequest extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        // The blockGroup must match the first half of how we call the block, and controller matches the second half
        // ie. foo_bar/adminhtml_baz
        $this->_blockGroup = 'brst_approve';
        $this->_controller = 'adminhtml_affialiaterequest';
        $this->_headerText = $this->__('Affialiate');
         
        parent::__construct();
    }
}