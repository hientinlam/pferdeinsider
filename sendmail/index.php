<?php 
                        //  $sender_name=Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name Admin
                        ////  $sender_email=Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email Admin
                         // $emailid=$customer->getEmail();

                          $sender_name1='Nidhi'; //fetch sender name Admin
                          $sender_email1='nidhi@gmail.com'; //fetch sender email Admin
                          $adminid        = 'brsttest@gmail.com';
                          $adminname      ='brsttest';
                          $mailTemplate1  = Mage::getModel('core/email_template');
                          $translate1     = Mage::getSingleton('core/translate');
                          $templateId1    = 2; 
                          $template_collection1 =  $mailTemplate1->load($templateId1);
                          $template_data1      = $template_collection1->getData();
                          $tem_text1            = $template_data1['template_text'];
                          $subject             = $template_data1['template_code']; 
                          $logo2               = $adminname;
                                 $logo1 ='nidhi';

                          $mail_text1          = str_replace("{{var firstname}}", $logo1, $tem_text1);
                          $mail_text2          = str_replace("{{var admin}}", $logo2, $mail_text1);
                          //die('hello die here');
                          $to1        = $adminid;
                          $headers   = 'MIME-Version: 1.0' . "\r\n";
                          $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                              // Additional headers
                          $headers .= "To: $adminid" . "\r\n";
                          $headers .= 'From:'.$sender_name1.'<'.$sender_email1 .'>' . "\r\n";
                          mail($to1, $subject, $mail_text2, $headers);
                          ?>