<?php

class Belvg_Userprofile_Block_Adminhtml_AdminMessages extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_messages';
        $this->_blockGroup = 'userprofile';
        $this->_headerText = Mage::helper('userprofile/data')->__('Manage Admin Messages');
    }

    protected function _prepareLayout()
    {
        $result =  parent::_prepareLayout();
        $this->getChild('grid')->setAdminFilter(true);
        return $result;
    }
}
