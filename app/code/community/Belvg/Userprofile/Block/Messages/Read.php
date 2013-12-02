<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
 /***************************************
 *         DISCLAIMER   *
 *****************************************/
 /* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *****************************************************
 * @category   Belvg
 * @package    Belvg_Userprofile
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Userprofile_Block_Messages_Read  extends  Mage_Core_Block_Template
{
    /** @var Belvg_Userprofile_Model_Messages */
    protected $_message;

    protected function _getSession(){
        return Mage::getSingleton('customer/session');
    }

    protected function  _construct() {
        parent::_construct();
        
    }

    public function getMessage()
    {
        if (null === $this->_message) {
            $message_id = $this->getRequest()->getParam('id');
            if ($message_id) {
                $this->_message = Mage::getModel('userprofile/messages')->load($message_id);
            }
        }
        return $this->_message;
    }

    protected function getReplyTitle()
    {
        $prefix = $this->__('RE:');
        $title = $this->getMessage()->getTitle();
        if (strpos($title, $prefix) !== 0) {
            $title = $prefix . ' ' . $title;
        }
        return $title;
    }
}


