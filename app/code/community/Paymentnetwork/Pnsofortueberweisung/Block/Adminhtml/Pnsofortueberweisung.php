<?php
class Paymentnetwork_Pnsofortueberweisung_Block_Adminhtml_Pnsofortueberweisung extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_controller = 'adminhtml_pnsofortueberweisung';
		$this->_blockGroup = 'pnsofortueberweisung';
		$this->_headerText = Mage::helper('pnsofortueberweisung')->__('Item Manager');
		$this->_addButtonLabel = Mage::helper('pnsofortueberweisung')->__('Add Item');
		parent::__construct();
	}
}