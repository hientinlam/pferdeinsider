<?php
class Brst_Requestaffialiate_Block_Adminhtml_Approveaffialiate extends Mage_Adminhtml_Block_Widget_Grid_Container

{
    public function __construct()
    {
        // The blockGroup must match the first half of how we call the block, and controller matches the second half
        // ie. foo_bar/adminhtml_baz
        $this->_blockGroup = 'brst_requestaffialiate';
        $this->_controller = 'adminhtml_approveaffialiate';
        $this->_headerText = $this->__('Approveaffialiate');
         
        parent::__construct();
    }
}