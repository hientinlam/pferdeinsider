<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Paymentnetwork
 * @package	Paymentnetwork_Sofortueberweisung
 * @copyright  Copyright (c) 2008 [m]zentrale GbR, 2010 Payment Network AG
 * @license	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version	$Id: Grid.php 3844 2012-04-18 07:37:02Z dehn $
 */
class Paymentnetwork_Pnsofortueberweisung_Block_Adminhtml_Pnsofortueberweisung_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
	  parent::__construct();
	  $this->setId('pnsofortueberweisungGrid');
	  $this->setDefaultSort('pnsofortueberweisung_id');
	  $this->setDefaultDir('ASC');
	  $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
	  $collection = Mage::getModel('pnsofortueberweisung/pnsofortueberweisung')->getCollection();
	  $this->setCollection($collection);
	  return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
	  $this->addColumn('pnsofortueberweisung_id', array(
		  'header'	=> Mage::helper('pnsofortueberweisung')->__('ID'),
		  'align'	 =>'right',
		  'width'	 => '50px',
		  'index'	 => 'pnsofortueberweisung_id',
	  ));

	  $this->addColumn('title', array(
		  'header'	=> Mage::helper('pnsofortueberweisung')->__('Title'),
		  'align'	 =>'left',
		  'index'	 => 'title',
	  ));

	  /*
	  $this->addColumn('content', array(
			'header'	=> Mage::helper('sofortueberweisung')->__('Item Content'),
			'width'	 => '150px',
			'index'	 => 'content',
	  ));
	  */

	  $this->addColumn('created_time', array(
			'header'	=> Mage::helper('pnsofortueberweisung')->__('Creation Time'),
			'align'	 => 'left',
			'width'	 => '120px',
			'type'	  => 'date',
			'default'   => '--',
			'index'	 => 'created_time',
	  ));

	  $this->addColumn('update_time', array(
			'header'	=> Mage::helper('pnsofortueberweisung')->__('Update Time'),
			'align'	 => 'left',
			'width'	 => '120px',
			'type'	  => 'date',
			'default'   => '--',
			'index'	 => 'update_time',
	  ));

	  $this->addColumn('status', array(
		  'header'	=> Mage::helper('pnsofortueberweisung')->__('Status'),
		  'align'	 => 'left',
		  'width'	 => '80px',
		  'index'	 => 'status',
		  'type'	  => 'options',
		  'options'   => array(
			  1 => 'Enabled',
			  2 => 'Disabled',
		  ),
	  ));
	  
		$this->addColumn('action',
			array(
				'header'	=>  Mage::helper('pnsofortueberweisung')->__('Action'),
				'width'	 => '100',
				'type'	  => 'action',
				'getter'	=> 'getId',
				'actions'   => array(
					array(
						'caption'   => Mage::helper('pnsofortueberweisung')->__('Edit'),
						'url'	   => array('base'=> '*/*/edit'),
						'field'	 => 'id'
					)
				),
				'filter'	=> false,
				'sortable'  => false,
				'index'	 => 'stores',
				'is_system' => true,
		));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('pnsofortueberweisung')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('pnsofortueberweisung')->__('XML'));
	  
	  return parent::_prepareColumns();
  }

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('pnsofortueberweisung_id');
		$this->getMassactionBlock()->setFormFieldName('pnsofortueberweisung');

		$this->getMassactionBlock()->addItem('delete', array(
			 'label'	=> Mage::helper('pnsofortueberweisung')->__('Delete'),
			 'url'	  => $this->getUrl('*/*/massDelete'),
			 'confirm'  => Mage::helper('sofortueberweisung')->__('Are you sure?')
		));

		$statuses = Mage::getSingleton('pnsofortueberweisung/status')->getOptionArray();

		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
			 'label'=> Mage::helper('pnsofortueberweisung')->__('Change status'),
			 'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
			 'additional' => array(
					'visibility' => array(
						 'name' => 'status',
						 'type' => 'select',
						 'class' => 'required-entry',
						 'label' => Mage::helper('pnsofortueberweisung')->__('Status'),
						 'values' => $statuses
					 )
			 )
		));
		return $this;
	}

  public function getRowUrl($row)
  {
	  return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}