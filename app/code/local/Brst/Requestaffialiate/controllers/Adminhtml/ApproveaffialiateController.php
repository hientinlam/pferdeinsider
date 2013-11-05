<?php
class Brst_Requestaffialiate_Adminhtml_ApproveaffialiateController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {  
        // Let's call our initAction method which will set some basic params for each action
        $this->_initAction()
            ->renderLayout();
    }  
     
    public function newAction()
    {  
        // We just forward the new action to a blank edit form
        $this->_forward('edit');
    }  
     
    public function editAction()
    {  
        $this->_initAction();
     
        // Get id if available
        $id  = $this->getRequest()->getParam('id');
        $model = Mage::getModel('brst_requestaffialiate/approveaffialiate');
     
        if ($id) {
            //die('sjsj');
            // Load record
            $model->load($id);
     
            // Check if record is loaded
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This Affialiate no longer exists.'));
                $this->_redirect('*/*/');
     
                return;
            }  
        }  
     
        $this->_title($model->getId()? $model->getName() : $this->__('New Affialiate'));
     
        $data = Mage::getSingleton('adminhtml/session')->getAffialiateData(true);
     //   echo "<pre>";print_r($data);die('fhfh');
        if (!empty($data)) {
            $model->setData($data);
        }  
     
        Mage::register('brst_requestaffialiate', $model);
     
        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Affialiate') : $this->__('New Affialiate'), $id ? $this->__('Edit Affialiate') : $this->__('New Affialiate'))
            ->_addContent($this->getLayout()->createBlock('brst_requestaffialiate/adminhtml_approveaffialiate_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }
    public function deleteAction()
    {
        $affiliateId = (int)$this->getRequest()->getParam('id');
        $affiliate =Mage::getSingleton('brst_requestaffialiate/approveaffialiate')->load($affiliateId);
        if ($affiliate->getData()) {
            $affiliate->delete();
            $this->_getSession()->addSuccess($this->__("Affiliate has been successfully deleted."));
        } else {
            $this->_getSession()->addError($this->__("Can't load affiliate by given ID."));

        }
        $this->_redirect('*/*');
    }
     
    public function saveAction()
    {
        echo "<pre>";print_r($this->getRequest()->getPost());
        print_r($_POST);
       die("herrre");
        if ($postData = $this->getRequest()->getPost()) {
           // echo "<pre>";print_r($postData);die('jdjj');
            $model = Mage::getSingleton('brst_requestaffialiate/approveaffialiate');
            $model->setData($postData);
 
            try {
                $model->save();
 
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The Affialiate has been saved.'));
                $this->_redirect('*/*/');
 
                return;
            }  
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this baz.'));
            }
 
            Mage::getSingleton('adminhtml/session')->setAffialiateData($postData);
            $this->_redirectReferer();
        }
    }
     
    public function messageAction()
    {
        $data = Mage::getModel('brst_requestaffialiate/approveaffialiate')->load($this->getRequest()->getParam('id'));
        echo $data->getContent();
    }
     
    /**
     * Initialize action
     *
     * Here, we set the breadcrumbs and the active menu
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            // Make the active menu match the menu config nodes (without 'children' inbetween)
            ->_setActiveMenu('sales/brst_requestaffialiate_approveaffialiate')
            ->_title($this->__('Sales'))->_title($this->__('Affialiate'))
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Affialiate'), $this->__('Affialiate'));
         
        return $this;
    }
     
    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/brst_requestaffialiate_approveaffialiate');
    }
}