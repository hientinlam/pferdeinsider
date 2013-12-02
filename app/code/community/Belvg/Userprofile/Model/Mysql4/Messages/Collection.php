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

class Belvg_Userprofile_Model_Mysql4_Messages_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract{
    public function _construct()
    {
	parent::_construct();
        $this->_init('userprofile/messages');
    }

    public function addInboxFilter(){
        $this->getSelect()->where('type = 0');
        return $this;
    }

    public function addOutboxFilter(){
        $this->getSelect()->where('type = 1');
        return $this;
    }

    public function addCustomerFilter($customer_id){
        $this->getSelect()->where('customer_id = ?',$customer_id);
        return $this;
    }

    public function addCustomerEntityFilter(){
        $this->getSelect()->join(
            array('p' => $this->getTable('customer/entity')),
            'main_table.customer_id = p.entity_id',
            array('customer_email'=>'p.email')
            );
        return $this;
    }
    public function newCustomFilter($customer_id,$send_to){
      $this->getSelect()
          ->where('customer_id = ?',$customer_id)
           ->orwhere('send_to = ?',$send_to) ;
      return $this;
    }

    public function addRecepientFilter($recepientId)
    {
        $this->getSelect()->where('send_to = ?', $recepientId);
        return $this;
    }
}
?>

