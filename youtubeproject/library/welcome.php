<?php
session_start();
error_reporting(0);
require_once('Zend/Loader.php');

	Zend_Loader::loadClass('Zend_Gdata_YouTube');
	Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
        //$httpClient = new Zend_Gdata_ClientLogin();
        $token=$_GET['token'];
	$authenticationURL = 'https://www.google.com/accounts/ClientLogin';
       
        
	$httpClient = Zend_Gdata_ClientLogin::getHttpClient(
				     $email = 'brsttest@gmail.com',
                                     $password = 'developer981',
                                     $service = 'youtube',
                                     $client = null,
                                     $source = 'youtubeproject',
                                     $loginToken = null,
                                     $loginCaptcha 	= null,
                                     $authenticationURL
                );

	$devkey = 'AI39si5TJ6MjNdmsyvPhpk72KDRv37O3-77sFpKKKep-3FTPeDJs4HnUks_SmeU75796H5zRXWL7PK3DtylnrveEJvTclOl3LA';

	$yt = new Zend_Gdata_YouTube($httpClient, '', '', $devkey);
 



        $myVideoEntry= new Zend_Gdata_YouTube_VideoEntry();
        $myVideoEntry->setVideoTitle('My Test Movie');
        $myVideoEntry->setVideoDescription('My Test Movie');

        // Note that category must be a valid YouTube category
        $myVideoEntry->setVideoCategory('Comedy');
        $myVideoEntry->SetVideoTags('cars, funny');

        $tokenHandlerUrl = 'http://gdata.youtube.com/action/GetUploadToken';
        $tokenArray = $yt->getFormUploadToken($myVideoEntry, $tokenHandlerUrl);
        
        $tokenValue = $tokenArray['token'];
        $postUrl = $tokenArray['url']; 
      
        // place to redirect user after upload
        $nextUrl = 'http://localhost/youtube/index.php';

        echo $form = '<form action="'. $postUrl .'?nexturl='. $nextUrl .
                '" method="post" enctype="multipart/form-data">'.
                '<input name="file" type="file"/>'.
                '<input name="token" type="hidden" value="'. $tokenValue .'"/>'.
                '<input value="Upload Video File" type="submit" />'.
                '</form>'; 
?>



