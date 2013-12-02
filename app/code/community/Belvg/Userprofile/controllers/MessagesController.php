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

class Belvg_Userprofile_MessagesController extends Mage_Core_Controller_Front_Action
{

	protected function _getSession()
        {
            return Mage::getSingleton('customer/session');
        }

	protected function _initAction() {		
		$this->loadLayout();			
		return $this;
	}   
 
	public function indexAction() {		
		$this->_initAction();
                $this->getLayout()->getBlock('head')->setTitle('Messages');
		$this->renderLayout();

	}

    public function allAction() {
		$this->_initAction();
                $this->getLayout()->getBlock('head')->setTitle('Messages');
		$this->renderLayout();

	}

    public function newAction() {
		$this->_initAction();
                $this->getLayout()->getBlock('head')->setTitle('New Message');
		$this->renderLayout();
	}
        public function postmessageAction() {
		$this->_initAction();
                $this->getLayout()->getBlock('head')->setTitle('New Message');
		$this->renderLayout();
	}

    public function newPostAction()
    {
        /** @var Mage_Customer_Model_Session $session */
        $session = Mage::getSingleton('customer/session');
        if ($session->getId()) {
            $message = $this->getRequest()->getPost();
            $messageModel = Mage::getModel('userprofile/messages');
            $messageModel->add($message);
        } else {
            $session->addError('You must be logged in in order to send a message.');
        }
        $this->_redirect('*/*/');
    }

    public function readAction() {
		if ($this->getRequest()->getParam('id'))
			Mage::getModel('userprofile/messages')->isRead($this->getRequest()->getParam('id'));
		$this->_initAction()
		->renderLayout();
	}

    public function deleteAction() {
		$mid = $this->getRequest()->getParam('id');
		if ($mid){
			$messageModel = Mage::getModel('userprofile/messages');
			$messageModel->remove($mid);
		}                
		$this->_redirect ('*/*/');		
	}

    public function newMessageAction()
    {
        /** @var Mage_Customer_Model_Session $session */
        $session = Mage::getSingleton('customer/session');
        if (!$session->getId()) {
            $session->addError('You must be logged in in order to send a message.');
            $this->_redirect('*/*/');
            return;
        }
        $customer_id = $_POST['customer_id'];
        $date = date('Y-m-d', time());
        $message = $_POST['message'];
        $title = $_POST['title'];

        $send_to = $_POST['email'];
        if ($customer_id == NULL) {
            $custid = '0';
        } else {
            $custid = $_POST['customer_id'];
        }
        /** @var Varien_Db_Adapter_Pdo_Mysql $connection */
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connection->insert('belvg_userprofile_messages', array(
            'customer_id' => $custid,
            'date' => $date,
            'title' => $title,
            'message' => $message,
            'status' => '0',
            'type' => '1',
            'send_to' => $send_to,
        ));
        $id = $connection->lastInsertId();
        if ($id) {
            $res['success'] = 'success';
            $res['id'] = $id;
            echo json_encode($res);
            exit;
        }
    }
}
