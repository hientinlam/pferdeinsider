<?php
class Brst_Calculate_Block_Adminhtml_Discount extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        // The blockGroup must match the first half of how we call the block, and controller matches the second half
        // ie. foo_bar/adminhtml_baz
        $this->_blockGroup = 'brst_calculate';
        $this->_controller = 'adminhtml_discount';
        $this->_headerText = $this->__('Discount');
         
        parent::__construct();
    }
}