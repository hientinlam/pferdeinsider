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
           $fieldset->addField('order_id', 'label', array(
            'name'      => 'ordre_id',
            'label'     => Mage::helper('checkout')->__('Order ID'),
            'title'     => Mage::helper('checkout')->__('Order ID'),
            'required'  => true,
        ));
        $fieldset->addField('customer_name', 'text', array(
            'name'      => 'customer_name',
            'label'     => Mage::helper('checkout')->__('Customer Name-(Buyer)'),
            'title'     => Mage::helper('checkout')->__('Customer Name-(Buyer)'),
            'required'  => true,
        ));
         $fieldset->addField('affiliate_name', 'text', array(
            'name'      => 'affiliate_name',
            'label'     => Mage::helper('checkout')->__('Affiliate Name'),
            'title'     => Mage::helper('checkout')->__('Affiliate Name'),
            'required'  => true,
        ));
          $fieldset->addField('commission', 'text', array(
            'name'      => 'commission',
            'label'     => Mage::helper('checkout')->__('Commission'),
            'title'     => Mage::helper('checkout')->__('Commission'),
            'required'  => true,
        ));
        $fieldset->addField('attracted_amount', 'text', array(
            'name'      => 'attracted_amount',
            'label'     => Mage::helper('checkout')->__('Attracted Amount'),
            'title'     => Mage::helper('checkout')->__('Attracted Amount'),
            'required'  => true,
        ));
        $fieldset->addField('member_amount', 'text', array(
            'name'      => 'member_amount',
            'label'     => Mage::helper('checkout')->__('Member Amount'),
            'title'     => Mage::helper('checkout')->__('Member Amount'),
            'required'  => true,
        ));
        $fieldset->addField('admin_amount', 'text', array(
            'name'      => 'admin_amount',
            'label'     => Mage::helper('checkout')->__('Admin Amount'),
            'title'     => Mage::helper('checkout')->__('Admin Amount'),
            'required'  => true,
        ));
         $fieldset->addField('created_at', 'text', array(
            'name'      => 'created_at',
            'label'     => Mage::helper('checkout')->__('Created At'),
            'title'     => Mage::helper('checkout')->__('Created At'),
            'required'  => true,
        ));
         $fieldset->addField('link', 'link', array(
          'label'     => 'Link',
          'style'   => "",
          'href' => $this->getUrl('*/sales_order/view/order_id/16/key/ed8c500e3b367fc34c1930e95ab471fc/') ,
          'value'  => 'Magento Blog',
          'tabindex' => 1,
          'after_element_html' => '<a style="padding-right:5px;" href="'.$this->getUrl('*/sales_order/view/order_id/'.$orderid.'/key/ed8c500e3b367fc34c1930e95ab471fc/').'" target="_blank">View Order Detail</a> <a style="padding-right:5px;" href="'.$this->getUrl('*/customer/edit/id/'.$model->getCustomerId().'/key/bcce443b21e466016b5bd4635664d459/').'" target="_blank">View Buyer Detail</a> <a style="padding-right:5px;" href="'.$this->getUrl('affiliate_admin/adminhtml_affiliate/edit/id/'.$model->getAffiliateId().'/key/fc3b7dd7fc99b7d073300d34973e5162/').'" target="_blank">View Affiliate Detail</a>'
        ));
     
     
        $form->setValues($model->getData());
       
        $form->setUseContainer(true);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }  
}