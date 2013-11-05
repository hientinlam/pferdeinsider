<?php
class Brst_Calculate_Block_Adminhtml_Discount_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     */
    public function __construct()
    {  
        parent::__construct();
     
        $this->setId('brst_calculate_discount_form');
        $this->setTitle($this->__('Discount Information'));
    }  
     
    /**
     * Setup form fields for inserts/updates
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {  
        $model = Mage::registry('brst_calculate');
        $oderincermtid =  $model->getOrderId();
      
         $orders = Mage::getModel('sales/order')
          ->getCollection()
          ->addAttributeToFilter('state', array('neq' => Mage_Sales_Model_Order::STATE_CANCELED))
          ->addAttributeToFilter('increment_id', $oderincermtid)
          ->getFirstItem();
         $orderid=$orders['entity_id'];
     
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method'    => 'post'
        ));
     
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('checkout')->__('Discount Information'),
            'class'     => 'fieldset-wide',
        ));
     
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name' => 'id',
            ));
        }  
           $fieldset->addField('member_id', 'label', array(
            'name'      => 'member_id',
            'label'     => Mage::helper('checkout')->__('Member ID'),
            'title'     => Mage::helper('checkout')->__('Member ID'),
            'required'  => true,
        ));
        $fieldset->addField('member_name', 'text', array(
            'name'      => 'member_name',
            'label'     => Mage::helper('checkout')->__('Member Name'),
            'title'     => Mage::helper('checkout')->__('Member Name'),
            'required'  => true,
        ));
         $fieldset->addField('total_order', 'text', array(
            'name'      => 'total_order',
            'label'     => Mage::helper('checkout')->__('Total Orders'),
            'title'     => Mage::helper('checkout')->__('Total Orders'),
            'required'  => true,
        ));
          $fieldset->addField('gross_earned', 'text', array(
            'name'      => 'gross_earned',
            'label'     => Mage::helper('checkout')->__('Gross Earned'),
            'title'     => Mage::helper('checkout')->__('Gross Earned'),
            'required'  => true,
        ));
        $fieldset->addField('amount_earned', 'text', array(
            'name'      => 'amount_earned',
            'label'     => Mage::helper('checkout')->__('Amount Earned'),
            'title'     => Mage::helper('checkout')->__('Amount Earned'),
            'required'  => true,
        ));
        $fieldset->addField('tax_paid', 'text', array(
            'name'      => 'tax_paid',
            'label'     => Mage::helper('checkout')->__('Tax Paid'),
            'title'     => Mage::helper('checkout')->__('Tax Paid'),
            'required'  => true,
        ));
        $fieldset->addField('admin_amount', 'text', array(
            'name'      => 'admin_amount',
            'label'     => Mage::helper('checkout')->__('Admin Amount'),
            'title'     => Mage::helper('checkout')->__('Admin Amount'),
            'required'  => true,
        ));
         $fieldset->addField('balance', 'text', array(
            'name'      => 'balance',
            'label'     => Mage::helper('checkout')->__('Balance'),
            'title'     => Mage::helper('checkout')->__('Balance'),
            'required'  => true,
        ));
         
        $form->setValues($model->getData());
       
        $form->setUseContainer(true);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }  
}