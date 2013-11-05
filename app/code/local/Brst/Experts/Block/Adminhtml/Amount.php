<?php
class Brst_Experts_Block_Adminhtml_Amount extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        // The blockGroup must match the first half of how we call the block, and controller matches the second half
        // ie. foo_bar/adminhtml_baz
        $this->_blockGroup = 'brst_experts';
        $this->_controller = 'adminhtml_amount';
        $this->_headerText = $this->__('Amount');
         
        parent::__construct();
    }
}