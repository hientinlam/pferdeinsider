<?php

class Display_Allmembers_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle('Horse Experts');
        $this->renderLayout();
    }

    public function messageAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function earningsAction()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->loadLayout();
            $this->getLayout()->getBlock('head')->setTitle('Earnings-Via Website');
            $this->renderLayout();
        } else {
            $this->_redirect('customer/account/login');
        }
    }

    public function viaaffiliateAction()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->loadLayout();
            $this->getLayout()->getBlock('head')->setTitle('Earnings-Via Affiliate');
            $this->renderLayout();
        } else {
            $this->_redirect('customer/account/login');
        }
    }

    public function new_earningsAction()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->loadLayout();
            $this->getLayout()->getBlock('head')->setTitle('Earnings-Via Website');
            $this->renderLayout();
        } else {
            $this->_redirect('customer/account/login');
        }
    }

    public function new_viaaffiliateAction()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->loadLayout();
            $this->getLayout()->getBlock('head')->setTitle('Earnings-Via Affiliate');
            $this->renderLayout();
        } else {
            $this->_redirect('customer/account/login');
        }
    }

    public function expertcalculationAction()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->loadLayout();
            $this->getLayout()->getBlock('head')->setTitle('Expert-Calculation');
            $this->renderLayout();
        } else {
            $this->_redirect('customer/account/login');
        }
    }

    public function bydateAction()
    {
        $data = $this->getRequest()->getPost();
        // $from             =explode('/',$data['startdate']);
        // $to               =explode('/',$data['enddate']);
        $from = date("d/m/Y", strtotime($data['startdate']));
        $to = date("d/m/Y", strtotime($data['enddate']));
        //    $fromtimestamp    = mktime(0,0,0,date($from[0]),date($from[1]),date($from[2])) ;
        //   $totimestamp      = mktime(0,0,0,date($to[0]),date($to[1]),date($to[2])) ;
        $expertname = $data['expertdata'];
        $collection = Mage::getModel('brst_experts/amount')->getCollection()
            ->addFieldToFilter('created_at', array("from" => $from, "to" => $to, "datetime" => false))
            ->addFieldToFilter('expert_name', array('like' => $expertname));
    }

    public function exportcsvAction()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $data = $this->getRequest()->getPost();

            $url = Mage::helper('core/url')->getCurrentUrl();
            $expertmodel = Mage::getModel('brst_experts/amount');
            $customer = Mage::getModel('customer/session')->getCustomer();
            $customeremail = $customer->getEmail();
            $adminmodel = Mage::getModel('admin/user')->getCollection()->getData();
            foreach ($adminmodel as $admindata) {
                if ($customeremail == $admindata['email']) {
                    $adminuser = explode('-', $admindata['username']);
                    $expertname = $adminuser[0];
                    break;
                }
            }

            $from = $data['from'];
            $to = $data['to'];
            $affiliatename = $this->getRequest()->getParam('name');
            $value = $this->getRequest()->getParam('value');
            if ($data['affiliatename'] != NULL) {
                $collection = Mage::getModel('brst_experts/amount')->getCollection()
                    ->addFieldToFilter('affiliate_name', array('like' => $_POST['affiliatename']))
                    //->addFieldToFilter('expert_name', array('like' => $expertname));
                    ->addFieldToFilter('customer_id', $customer->getId()); // PhuongLan fixed collection
            } elseif ($data['datevalue'] == '1') {
                $collection = Mage::getModel('brst_experts/amount')->getCollection()
                    ->addFieldToFilter('created_at', array("from" => $from, "to" => $to, "datetime" => false))
                    ->addFieldToFilter('affiliate_name', array('neq' => ''))
                    //->addFieldToFilter('expert_name', array('like' => $expertname));
                    ->addFieldToFilter('customer_id', $customer->getId()); // PhuongLan fixed collection
            } else {
                $collection = Mage::getModel('brst_experts/amount')->getCollection()
                    //->addFieldToFilter('expert_name', array('like' => $expertname))
                    ->addFieldToFilter('customer_id', $customer->getId()) // PhuongLan fixed collection
                    ->addFieldToFilter('affiliate_name', array('neq' => ''));
            }
            $curencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();

            if ($data['earning_export'] == 'csv') {
                if ($_POST['collectionsize'] > 0) {
                    $list = array("OrderID:::->Affiliate Name:::->Actual Product Cost:::->Affiliate Earning:::->% of Affiliate Earning:::->Expert Earning");
                    $file = fopen("Earnings.csv", "w");
                    foreach ($list as $line) {
                        fputcsv($file, split(':::->', $line));
                    }
                    $lost = array();

                    foreach ($collection as $record) {
                        $orignalprice = number_format($record['gross_price'], 0);
                        $percent = ($record['affiliate_pay'] / $orignalprice) * 100;
                        $lost[] = $record['order_id'] . ":::->" . $record['affiliate_name'] . ":::->" . $curencySymbol . number_format($record['gross_price'], 2) . ":::->" . $record['affiliate_pay'] . ":::->" . $percent . ":::->" . $record['getyoupaid'];
                    }
                    $file = fopen("Earnings.csv", "a+");
                    foreach ($lost as $line) {
                        fputcsv($file, split(':::->', $line));
                    }
                    fseek($file, 0);
                    header('Content-Type: application/csv');
                    header('Content-Disposition: attachment; filename=Earnings.csv');
                    fpassthru($file);
                    fclose($file);
                } else {
                    $session = Mage::getSingleton('core/session');
                    $session->addError($this->__('No record found to generate csv file.'));
                    $this->_redirectReferer();
                }
            } else {
                if ($_POST['collectionsize'] > 0) {

                    $DOMObject = new DOMDocument("1.0");
                    header("Content-Type: text/plain");
                    $root_element = $DOMObject->createElement("earnings");

                    foreach ($collection as $record) {
                        $orignalprice = number_format($record['gross_price'], 0);
                        $percent = ($record['affiliate_pay'] / $orignalprice) * 100;

                        $root_element1 = $DOMObject->createElement("details");
                        $DOMObject->appendChild($root_element);
                        $root_element->appendChild($root_element1);

                        $root_item1 = $DOMObject->createElement("orderid");
                        $root_element1->appendChild($root_item1);
                        $root_item_text1 = $DOMObject->createTextNode($record['order_id']);
                        $root_item1->appendChild($root_item_text1);

                        $root_item2 = $DOMObject->createElement("affiliate_name");
                        $root_element1->appendChild($root_item2);
                        $root_item_text2 = $DOMObject->createTextNode($record['affiliate_name']);
                        $root_item2->appendChild($root_item_text2);

                        $root_item3 = $DOMObject->createElement("actualproductcost");
                        $root_element1->appendChild($root_item3);
                        $root_item_text3 = $DOMObject->createTextNode($curencySymbol . number_format($record['gross_price'], 2));
                        $root_item3->appendChild($root_item_text3);

                        $root_item4 = $DOMObject->createElement("affiliate_pay");
                        $root_element1->appendChild($root_item4);
                        $root_item_text4 = $DOMObject->createTextNode($record['affiliate_pay']);
                        $root_item4->appendChild($root_item_text4);

                        $root_item5 = $DOMObject->createElement("percentage");
                        $root_element1->appendChild($root_item5);
                        $root_item_text5 = $DOMObject->createTextNode($percent);
                        $root_item5->appendChild($root_item_text5);

                        $root_item6 = $DOMObject->createElement("expertearning");
                        $root_element1->appendChild($root_item6);
                        $root_item_text6 = $DOMObject->createTextNode($record['getyoupaid']);
                        $root_item6->appendChild($root_item_text6);

                    }
                    $file = 'earnings.xml';
                    $DOMObject->save('earnings.xml');
                    file_put_contents($file, $DOMObject->saveXML());
                    header('Content-type: text/xml');
                    header('Content-Disposition: attachment; filename="earnings.xml"');
                    echo $DOMObject->saveXML();
                } else {
                    $session = Mage::getSingleton('core/session');
                    $session->addError($this->__('No record found to generate xml file.'));
                    $this->_redirectReferer();
                }
            }
        } else {
            $this->_redirect('customer/account/login');
        }
    }

    public function exportAction()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $data = $this->getRequest()->getPost();
            //    echo "<pre>";print_r($data);die('ssgh');
            $url = Mage::helper('core/url')->getCurrentUrl();
            $expertmodel = Mage::getModel('brst_experts/amount');
            $customer = Mage::getModel('customer/session')->getCustomer();
            $customeremail = $customer->getEmail();
            $adminmodel = Mage::getModel('admin/user')->getCollection()->getData();
            foreach ($adminmodel as $admindata) {
                if ($customeremail == $admindata['email']) {
                    $adminuser = explode('-', $admindata['username']);
                    $expertname = $adminuser[0];
                    break;
                }
            }

            $from = $data['from'];
            $to = $data['to'];
            $affiliatename = $this->getRequest()->getParam('name');
            $value = $this->getRequest()->getParam('value');
            if ($data['datevalue'] == '1') {
                $collection = Mage::getModel('brst_experts/amount')->getCollection()
                    ->addFieldToFilter('created_at', array("from" => $from, "to" => $to, "datetime" => false))
                    ->addFieldToFilter('affiliate_name', array('like' => ''))
                    //->addFieldToFilter('expert_name', array('like' => $expertname));
                    ->addFieldToFilter('customer_id', $customer->getId());
            } else {
                $collection = Mage::getModel('brst_experts/amount')->getCollection()
                    //->addFieldToFilter('expert_name', array('like' => $expertname))
                    ->addFieldToFilter('customer_id', $customer->getId())
                    ->addFieldToFilter('affiliate_name', array('like' => ''));
            }
            $curencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();

            if ($_POST['earning_export'] == 'csv') {
                if ($_POST['collectionsize'] > 0) {
                    $list = array("OrderID:::->Actual Product Cost:::->Expert Earning");
                    $file = fopen("Earnings.csv", "w");
                    foreach ($list as $line) {
                        fputcsv($file, split(':::->', $line));
                    }
                    $lost = array();

                    foreach ($collection as $record) {
                        $orignalprice = number_format($record['gross_price'], 0);
                        $percent = ($record['affiliate_pay'] / $orignalprice) * 100;
                        $lost[] = $record['order_id'] . ":::->" . $curencySymbol . number_format($record['gross_price'], 2) . ":::->" . $record['getyoupaid'];
                    }
                    $file = fopen("Earnings.csv", "a+");
                    foreach ($lost as $line) {
                        fputcsv($file, split(':::->', $line));
                    }
                    fseek($file, 0);
                    header('Content-Type: application/csv');
                    header('Content-Disposition: attachment; filename=Earnings.csv');
                    fpassthru($file);
                    fclose($file);
                } else {
                    $session = Mage::getSingleton('core/session');
                    $session->addError($this->__('No record found to generate csv file.'));
                    $this->_redirectReferer();
                }
            } else {
                if ($_POST['collectionsize'] > 0) {

                    $DOMObject = new DOMDocument("1.0");
                    header("Content-Type: text/plain");
                    $root_element = $DOMObject->createElement("earnings");

                    foreach ($collection as $record) {
                        $orignalprice = number_format($record['gross_price'], 0);
                        $percent = ($record['affiliate_pay'] / $orignalprice) * 100;

                        $root_element1 = $DOMObject->createElement("details");
                        $DOMObject->appendChild($root_element);
                        $root_element->appendChild($root_element1);

                        $root_item1 = $DOMObject->createElement("orderid");
                        $root_element1->appendChild($root_item1);
                        $root_item_text1 = $DOMObject->createTextNode($record['order_id']);
                        $root_item1->appendChild($root_item_text1);


                        $root_item3 = $DOMObject->createElement("actualproductcost");
                        $root_element1->appendChild($root_item3);
                        $root_item_text3 = $DOMObject->createTextNode($curencySymbol . number_format($record['gross_price'], 2));
                        $root_item3->appendChild($root_item_text3);


                        $root_item6 = $DOMObject->createElement("expertearning");
                        $root_element1->appendChild($root_item6);
                        $root_item_text6 = $DOMObject->createTextNode($record['getyoupaid']);
                        $root_item6->appendChild($root_item_text6);

                    }
                    $file = 'earnings.xml';
                    $DOMObject->save('earnings.xml');
                    file_put_contents($file, $DOMObject->saveXML());
                    header('Content-type: text/xml');
                    header('Content-Disposition: attachment; filename="earnings.xml"');
                    echo $DOMObject->saveXML();
                } else {
                    $session = Mage::getSingleton('core/session');
                    $session->addError($this->__('No record found to generate xml file.'));
                    $this->_redirectReferer();
                }
            }
        } else {
            $this->_redirect('customer/account/login');
        }
    }

    public function affiliatesAction()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->loadLayout();
            $this->getLayout()->getBlock('head')->setTitle('Expert Earnings');
            $this->renderLayout();
        } else {
            $this->_redirect('customer/account/login');
        }
    }

    public function eventAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function productsAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function subscribeAction()
    {
        $arrData = $this->getRequest()->getPost();

        $data1['customer_id'] = $arrData['customerid'];
        $data1['customer_email'] = $arrData['customeremail'];
        $data1['member_id'] = $arrData['memberid'];
        $data1['createddate'] = date('d/m/Y');
        $data1['status'] = '1';
        $model = Mage::getModel('allmembers/allmembers');
        if ($arrData['id'] != '') {
            $data1['id'] = $arrData['id'];
            $model->load($arrData['id']);
        }
        $model->setData($data1);
        $model->save();

        $attributecode = 'member_list';
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attributecode);
        if ($attribute->usesSource()) {
            $attribute_options = $attribute->getSource()->getAllOptions(false);
        }
        foreach ($attribute_options as $optiondata) {
            if ($optiondata['value'] == $arrData['memberid']) {
                $expertname = $optiondata['label'];
            }
        }
        $adminmodel = Mage::getModel('admin/user')->getCollection()->getData();
        foreach ($adminmodel as $admininfo) {
            $adminusername = explode('-', $admininfo['username']);
            if ($adminusername[0] == $expertname) {
                $emailid = $admininfo['email'];
                break;
            }
        }

        $customerdetail = Mage::getSingleton('customer/session')->getCustomer();
        $firstname = $customerdetail->getFirstname();
        $customeremail = $customerdetail->getEmail();
        $adminid = Mage::getStoreConfig('trans_email/ident_general/email');
        $adminname = Mage::getStoreConfig('trans_email/ident_general/name');
        $mailTemplate1 = Mage::getModel('core/email_template');
        $translate = Mage::getSingleton('core/translate');
        /***************** SEND EMAIL TO EXPERT**********************/

        $templateId1 = 5;
        $template_collection1 = $mailTemplate1->load($templateId1);
        $template_data1 = $template_collection1->getData();
        $tem_text1 = $template_data1['template_text'];
        $subject = $template_data1['template_code'];
        $mail_text = str_replace("{{var customername}}", $expertname, $tem_text1);

        $to1 = $emailid;
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        // Additional headers
        $headers .= "To: $adminid" . "\r\n";
        $headers .= 'From:' . $adminname . '<' . $adminid . '>' . "\r\n";
        mail($to1, $subject, $mail_text, $headers);


        /***************** sEND SUBSCRIPTION NOTIFICATION TO CUSTOMER**********************/

        $templateId2 = 7;
        $template_collection2 = $mailTemplate1->load($templateId2);
        $template_data2 = $template_collection2->getData();
        $tem_text2 = $template_data2['template_text'];
        $subject = $template_data2['template_code'];
        $mail_text = str_replace("{{var expertname}}", $expertname, $tem_text2);
        $mail_text1 = str_replace("{{var customername}}", $firstname, $mail_text);

        $to1 = $customeremail;
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        // Additional headers
        $headers .= "To: $adminid" . "\r\n";
        $headers .= 'From:' . $adminname . '<' . $adminid . '>' . "\r\n";
        mail($to1, $subject, $mail_text1, $headers);

        $session = Mage::getSingleton('core/session');
        $session->addSuccess($this->__('You have been subscribed successfully.'));
        $this->_redirectReferer();
    }

    public function unsubscribeAction()
    {
        $arrData = $this->getRequest()->getPost();
        if ($arrData['id'] != '') {
            $data1['id'] = $arrData['id'];
            $data1['customer_id'] = $arrData['customerid'];
            $data1['customer_email'] = $arrData['customeremail'];
            $data1['member_id'] = $arrData['memberid'];
            $data1['createddate'] = date('d/m/Y');
            $data1['status'] = '0';
            $model = Mage::getModel('allmembers/allmembers')->load($arrData['id']);
            $model->setData($data1);
            $model->save();
            $session = Mage::getSingleton('core/session');
            $session->addSuccess($this->__('You have been unsubscribed successfully.'));

            $attributecode = 'member_list';
            $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attributecode);
            if ($attribute->usesSource()) {
                $attribute_options = $attribute->getSource()->getAllOptions(false);
            }
            foreach ($attribute_options as $optiondata) {
                if ($optiondata['value'] == $arrData['memberid']) {
                    $expertname = $optiondata['label'];

                }
            }
            $adminmodel = Mage::getModel('admin/user')->getCollection()->getData();
            foreach ($adminmodel as $admininfo) {
                $adminusername = explode('-', $admininfo['username']);
                if ($adminusername[0] == $expertname) {
                    $emailid = $admininfo['email'];
                    break;
                }
            }

            $customerdetail = Mage::getSingleton('customer/session')->getCustomer();
            $firstname = $customerdetail->getFirstname();
            $customeremail = $customerdetail->getEmail();
            $adminid = Mage::getStoreConfig('trans_email/ident_general/email');
            $adminname = Mage::getStoreConfig('trans_email/ident_general/name');
            $mailTemplate1 = Mage::getModel('core/email_template');
            $translate = Mage::getSingleton('core/translate');
            /***************** SEND EMAIL TO EXPERT**********************/

            $templateId1 = 8;
            $template_collection1 = $mailTemplate1->load($templateId1);
            $template_data1 = $template_collection1->getData();
            $tem_text1 = $template_data1['template_text'];
            $subject = $template_data1['template_code'];
            $mail_text = str_replace("{{var customername}}", $expertname, $tem_text1);
            $mail_text1 = str_replace("{{var subscribecustomer}}", $firstname, $mail_text);

            $to1 = $emailid;
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            // Additional headers
            $headers .= "To: $adminid" . "\r\n";
            $headers .= 'From:' . $adminname . '<' . $adminid . '>' . "\r\n";
            mail($to1, $subject, $mail_text, $headers);


            /***************** SEND UNSUBSCRIPTION NOTIFICATION TO CUSTOMER**********************/

            $templateId2 = 9;
            $template_collection2 = $mailTemplate1->load($templateId2);
            $template_data2 = $template_collection2->getData();
            $tem_text2 = $template_data2['template_text'];
            $subject = $template_data2['template_code'];
            $mail_text = str_replace("{{var expertname}}", $expertname, $tem_text2);
            $mail_text1 = str_replace("{{var customername}}", $firstname, $mail_text);

            $to1 = $customeremail;
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            // Additional headers
            $headers .= "To: $adminid" . "\r\n";
            $headers .= 'From:' . $adminname . '<' . $adminid . '>' . "\r\n";
            mail($to1, $subject, $mail_text1, $headers);

            $this->_redirectReferer();
        }
    }

    public function videosAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle('Member Videos');
        $this->renderLayout();
    }
}
