<?php 
class Brst_Comment_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
     $this->loadLayout(array('default'));
     $this->renderLayout();
    }
    public function saveAction()
    {
      if(isset($_POST))
      {
         $comment_model=Mage::getModel('comment/comment');
          $comment_model->setcustomer_id($_POST['customer_id']);
          $comment_model->setproduct_id($_POST['product_id']);
          $comment_model->setcomment($_POST['comment']);
          $comment_model->setposted_date($_POST['posted_date']);
          $comment_model->save();
          $res['success']='success';
          echo json_encode($res);
           exit;
      }
      
    }
}
?>