<?php
class Brst_Member_Block_Adminhtml_Payment extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        // The blockGroup must match the first half of how we call the block, and controller matches the second half
        // ie. foo_bar/adminhtml_baz
        $this->_blockGroup = 'brst_member';
        $this->_controller = 'adminhtml_payment';
        $this->_headerText = $this->__('Payment');
         
        parent::__construct();
    }
}