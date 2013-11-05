<?php

/**
 * webideaonline.com.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webideaonline.com/licensing/
 *
 */


class Simple_Forum_Block_Adminhtml_Topic_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'topic_id';
        $this->_blockGroup = 'forum';
        $this->_controller = 'adminhtml_topic';
        $this->_updateButton('save', 'label', Mage::helper('forum/topic')->__('Save Topic'));
        $this->_updateButton('delete', 'label', Mage::helper('forum/topic')->__('Delete Topic'));
    }

    public function getHeaderText()
    {
        if( Mage::registry('topic_data') && Mage::registry('topic_data')->getId() ) 
		{
            return Mage::helper('forum/topic')->__("Edit Topic '%s'", $this->htmlEscape(Mage::registry('topic_data')->getTitle()));
        } 
		else 
		{
            return Mage::helper('forum/topic')->__('Add Topic');
        }
    }
}
