<?php
class Brst_Requestaffialiate_Block_Adminhtml_Affialiate extends Mage_Adminhtml_Block_Widget_Grid_Container

{
    public function __construct()
    {
        // The blockGroup must match the first half of how we call the block, and controller matches the second half
        // ie. foo_bar/adminhtml_baz
        $this->_blockGroup = 'brst_requestaffialiate';
        $this->_controller = 'adminhtml_affialiate';
        $this->_headerText = $this->__('Affialiate');
         
        parent::__construct();
    }
}