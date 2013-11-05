<?php
class Brst_Member_Adminhtml_PaymentController extends Mage_Adminhtml_Controller_Action
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
        $model = Mage::getModel('brst_member/payment');
     
        if ($id) {
            // Load record
            $model->load($id);
     
            // Check if record is loaded
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This payment no longer exists.'));
                $this->_redirect('*/*/');
     
                return;
            }  
        }  
     
        $this->_title($model->getId() ? $model->getName() : $this->__('New Payment'));
     
        $data = Mage::getSingleton('adminhtml/session')->getPaymentData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
     
        Mage::register('brst_member', $model);
     
        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Payment') : $this->__('New Payment'), $id ? $this->__('Edit Payment') : $this->__('New Payment'))
            ->_addContent($this->getLayout()->createBlock('brst_member/adminhtml_payment_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }
     
    public function saveAction()
    {
        
        if ($postData = $this->getRequest()->getPost()) {
            if($postData['status']=='paid')
            {
                $model = Mage::getModel('brst_member/payment')->load($postData['id']);
                $model->setmember_name($postData['member_name']);
                $model->settotal_earned($postData['total_earned']);
                $model->settotal_paid($postData['total_earned']);
                $model->setpending_amount('0');
                $model->setinprogress('0');
                $model->setlast_invoice_date($postData['last_invoice_date']);
                $model->setstatus('paid');
                $model->save();
            }
            else
            {
                $model = Mage::getSingleton('brst_member/payment');
                $model->setData($postData);
            }
 
            try {
                $model->save();
 
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The Payment has been saved.'));
                $this->_redirect('*/*/');
 
                return;
            }  
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this payment.'));
            }
 
            Mage::getSingleton('adminhtml/session')->setPaymentData($postData);
            $this->_redirectReferer();
        }
    }
     
    public function deleteAction()
    {
        $affiliateId = (int)$this->getRequest()->getParam('id');
        $affiliate =Mage::getSingleton('brst_member/payment')->load($affiliateId);
        if ($affiliate->getData()) {
            $affiliate->delete();
            $this->_getSession()->addSuccess($this->__("Payment has been successfully deleted."));
        } else {
            $this->_getSession()->addError($this->__("Can't load Payment by given ID."));
            
        }
        $this->_redirect('*/*');
    }
    
    /** 
    * @function         : exportCsvAction 
    * @created by       : parvez Alam 
    * @description      : Export data grid to CSV format 
    * @params           : null 
    * @returns          : array 
    */
    public function exportCsvAction()
    {
        $fileName   = 'member.csv';
        $content    = $this->getLayout()->createBlock('brst_member_block_adminhtml_payment_grid');
        $this->_prepareDownloadResponse($fileName, $content->getCsvFile());
    }
    
    public function exportExcelAction()
    {
        $fileName   = 'member.xml';
        $grid       = $this->getLayout()->createBlock('brst_member_block_adminhtml_payment_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile());
    }
   public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('brst_member_block_adminhtml_payment_grid')->toHtml()
        );
    }
    public function paidAction()
    {
         $this->_initAction();
     
        // Get id if available
        $ids  = $this->getRequest()->getParam('id');
        if (!empty($ids)) {
                try {
                     foreach ($ids as $Id) {
                        $model = Mage::getModel('brst_member/payment')->load($Id);
                        $model->setmember_name($model['member_name']);
                        $model->settotal_earned($model['total_earned']);
                        $model->settotal_paid($model['total_earned']);
                        $model->setpending_amount('0');
                        $model->setinprogress('0');
                        $model->setlast_invoice_date($model['last_invoice_date']);
                        $model->setstatus('paid');
                        $model->save();
                     }
                   $this->_getSession()->addSuccess(
                            $this->__('Total of %d record(s) have been updated.', count($ids))
                        ); 
                }
               catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
               $this->_redirect('*/*');
        }
     }
    public function unpaidAction()
    {
       die('helo');
    }
    
    public function messageAction()
    {
        $data = Mage::getModel('brst_member/payment')->load($this->getRequest()->getParam('id'));
        echo $data->getContent();
    }
    
    public function createAction()
    {
        $id = Mage::app()->getRequest()->getParam('id');
        $model = Mage::getModel('brst_member/payment')->load($id);
        $name = $model['member_name'];
        $user = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('username',$name)->getData();
        $email = $user[0]['email'];
        $earned = $model['total_earned'];
        $paid = $model['total_paid'];
        $pending = $model['pending_amount'];
        $inprogress = $model['inprogress'];
        $date = $model['last_invoice_date'];
        $status = $model['status'];
        $pdf = Mage::getModuleDir('local', 'Brst_Member') . DS .'pdf/index.php';
        require_once ($pdf);
        $url = Mage::getModuleDir('local', 'Brst_Member') . DS .'pdf/';
        $fpdf = new PDF();
	$fpdf->AddPage();
	$fpdf->SetFont( 'Arial', 'B', 10 );
        
        $fpdf->Cell(30,8,'Member Name',1,0,'C',0);
        $fpdf->Cell(30,8,'Total Earned',1,0,'C',0);
        $fpdf->Cell(25,8,'Total Paid',1,0,'C',0);
        $fpdf->Cell(35,8,'Pending Amount',1,0,'C',0);
        $fpdf->Cell(25,8,'Inprogress',1,0,'C',0);
        $fpdf->Cell(30,8,'Invoice Date',1,0,'C',0);
        $fpdf->Cell(20,8,'Status',1,1,'C',0);
	//$fpdf->Ln();
        $fpdf->Cell(30,8,$name,1,0,'C',0);
        $fpdf->Cell(30,8,$earned,1,0,'C',0);
        $fpdf->Cell(25,8,$paid,1,0,'C',0);
        $fpdf->Cell(35,8,$pending,1,0,'C',0);
        $fpdf->Cell(25,8,$inprogress,1,0,'C',0);
        $fpdf->Cell(30,8,$date,1,0,'C',0);
        $fpdf->Cell(20,8,$status,1,1,'C',0);
	$content = $fpdf->Output($url.time().'.pdf','F');
        /* send email */
        $eol="\r\n";
        $file = time().'.pdf';
        $f_name= $url.$file;
        $handle=fopen($f_name, 'rb');
        $f_contents=fread($handle, filesize($f_name));
        $f_contents=chunk_split(base64_encode($f_contents));
        $f_type=filetype($f_name);
        fclose($handle);
        
        $emailaddress=$email;
        $emailsubject="Here's An Email with a PDF";
        $admin_email = Mage::getStoreConfig('trans_email/ident_general/email');
        ob_start();
        $body=ob_get_contents(); ob_end_clean();
        $headers .= 'From: Admin <'.$admin_email.'>'.$eol;
        $headers .= 'Reply-To: Admin <'.$admin_email.'>'.$eol;
        $headers .= 'Return-Path: Admin <'.$admin_email.'>'.$eol;
        $mime_boundary=md5(time());
        $headers .= 'MIME-Version: 1.0'.$eol;
        $headers .= "Content-Type: multipart/related; boundary=\"".$mime_boundary."\"".$eol; 
        $msg = "";
        # Attachment
        $msg .= "--".$mime_boundary.$eol;
        $msg .= "Content-Type: application/pdf; name=\"".$file."\"".$eol;
        $msg .= "Content-Transfer-Encoding: base64".$eol;
        $msg .= "Content-Disposition: attachment; filename=\"".$file."\"".$eol.$eol;
        $msg .= $f_contents.$eol.$eol;
        $msg .= $body.$eol.$eol;
        mail($emailaddress, $emailsubject, $msg, $headers);
        
        $model->setmember_name($name);
        $model->settotal_earned($earned);
        $model->settotal_paid($paid);
        $model->setpending_amount('0');
        $model->setinprogress($pending);
        $model->setlast_invoice_date($date);
        $model->setstatus('unpaid');
        $model->save();
        
        $this->_getSession()->addSuccess($this->__('Email successfully sent to the member.'));
        $this->_redirect('*/*');
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
            ->_setActiveMenu('sales/brst_member_payment')
            ->_title($this->__('Sales'))->_title($this->__('Payment'))
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Payment'), $this->__('Payment'));
         
        return $this;
    }
    
    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/brst_member_payment');
    }
}