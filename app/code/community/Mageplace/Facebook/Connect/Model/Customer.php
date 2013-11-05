<?php
/**
 * Mageplace Facebook Connect
 *
 * @category	Mageplace_Facebook
 * @package	 Mageplace_Facebook_Connect
 * @copyright   Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license	 http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Facebook_Connect_Model_Customer extends Mage_Customer_Model_Customer
{
	protected $_facebook;

	/**
	 * Load customer by facebook email
	 *
	 * @return  Mageplace_Facebook_Connect_Model_Customer
	 */
	public function loadByFacebookEmail($customerEmail = null)
	{
		if (is_null($customerEmail)) {
			$customerData = $this->getUserInfo();
			if (empty($customerData['email'])) {
				return $this;
			}
			$customerEmail = $customerData['email'];
		}

		return parent::loadByEmail($customerEmail);
	}

	public function create()
	{
		$customerData = $this->getUserInfo();

		$importData['email']		= (!empty ($customerData['email']))?$customerData['email']:'';
		$importData['firstname']	= (!empty ($customerData['first_name']))?$customerData['first_name']:'';
		$importData['middlename']	= (!empty ($customerData['middle_name']))?$customerData['middle_name']:'';
		$importData['lastname']		= (!empty ($customerData['last_name']))?$customerData['last_name']:'';
		$importData['website']		= Mage::app()->getStore()->getWebsite()->getCode();
		$importData['created_in']	= Mage::app()->getStore()->getCode();
		
		if (!empty ($customerData['gender'])){
			$gender = strtolower($customerData['gender']);
		}else $gender = 'male';
		
		if($gender == 'male') {
			$importData['gender'] = 'Male';
		} elseif($gender == 'female') {
			$importData['gender'] = 'Female';
		}

		$convertAdapter = Mage::getModel('customer/convert_adapter_customer');
	   
		$customer = $convertAdapter->getCustomerModel();
		$pass  = $customer->generatePassword(8);
		$hash = $customer->hashPassword($pass);
		$importData['password_hash'] = $hash;
		
		/* @var $convertAdapter Mage_Customer_Model_Convert_Adapter_Customer */
		if ($customerGroupId = Mage::getStoreConfig('customer/create_account/default_group')) {
			$customerGroupName = Mage::getModel('customer/group')->load($customerGroupId)->getCustomerGroupCode();
			$importData['group'] = $customerGroupName;
			$importData['group_id'] = $customerGroupName;
		} else if (($customerGroups = $convertAdapter->getCustomerGroups()) && !empty($customerGroups) && is_array($customerGroups)) {
			if (array_key_exists('General', $customerGroups)) {
				$importData['group'] = 'General';
				$importData['group_id'] = 'General';
			} else {
				$group_keys = array_keys($customerGroups);
				$importData['group'] = $group_keys[0];
				$importData['group_id'] = $group_keys[0];
			}
		} else {
			$importData['group'] = 'General';
			$importData['group_id'] = 'General';
		}

		try {
			$convertAdapter->saveRow($importData);
		} catch (Exception $e) {
			Mage::getSingleton('customer/session')->addException($e, $this->__('Cannot create the customer.'));
		}

		$this->load($convertAdapter->getCustomerModel()->getId());

		if ($this->getConfirmation()) {
			$this->setConfirmation(null)->save();
			$this->setIsJustConfirmed(true);
		}

		if (Mage::helper('facebookconnect')->getCfg(Mageplace_Facebook_Connect_Helper_Data::VAR_ENABLE_MESSAGE)) {
			try {
				$this->getFacebook()->facebookPostSingup($this);
			} catch (Exception $e) {
				Mage::logException($e);
			}
		}  
		 if (Mage::helper('facebookconnect')->getCfg(Mageplace_Facebook_Connect_Helper_Data::VAR_ENABLE_NOTIFY)) {

				$emailTemplate = Mage::getModel('core/email_template');
				
				$emailTemplate->sendTransactional(
						Mage::helper('facebookconnect')->getCfg(Mageplace_Facebook_Connect_Helper_Data::VAR_EMAIL_TEMPLATE),
						Mage::helper('facebookconnect')->getCfg(Mageplace_Facebook_Connect_Helper_Data::VAR_EMAIL_IDENTITY),
						$importData['email'], 
						$importData['firstname']." ".$importData['lastname'], 
						array('email' => $importData['email'], 'pass' => $pass)
				);
			}

		return $this;
	}

	public function login()
	{
		$customer_session = Mage::getSingleton('customer/session');
		/* @var $customer_session Mage_Customer_Model_Session */
		$customer_session->setCustomerAsLoggedIn($this);
	}

	public function getUserInfo()
	{
		$userData = $this->getData('user_info');
		if (is_null($userData)) {
			$userData = $this->getFacebook()->getUserInfo();
			$this->setData('user_info', $userData);
		}

		return $userData;
	}

	public function setFacebook($facebook)
	{
		$this->_facebook = $facebook;

		return $this;
	}

	public function getFacebook()
	{
		return $this->_facebook;
	}

}