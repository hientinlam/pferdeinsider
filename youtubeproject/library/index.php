
<?php

//error_reporting(0);
require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path
Zend_Loader::loadClass('Zend_Gdata_YouTube');
$yt = new Zend_Gdata_YouTube();
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');



function getAuthSubRequestUrl()
{
    $next = 'http://54.228.195.42/magento/youtubeproject/library/index.php';
    $scope = 'http://gdata.youtube.com';
    $secure = false;
    $session = true;
    return Zend_Gdata_AuthSub::getAuthSubTokenUri($next, $scope, $secure, $session);
    
}


function getAuthSubHttpClient()
{
    if (!isset($_SESSION['sessionToken']) && !isset($_GET['token']) ){
        echo '<a href="' . getAuthSubRequestUrl() . '">Login!</a>';
        return;
    } else if (!isset($_SESSION['sessionToken']) && isset($_GET['token'])) {
        $_SESSION['sessionToken'] = Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
    }

    $httpClient = Zend_Gdata_AuthSub::getHttpClient($_SESSION['sessionToken']);
    return $httpClient;
}
//echo getAuthSubRequestUrl();
echo getAuthSubHttpClient();
echo $token=$_GET['token'];
	$authenticationURL = 'https://www.google.com/accounts/ClientLogin';
       
        
	$httpClient = Zend_Gdata_ClientLogin::getHttpClient(
				     $email = 'pferdeinsider.com@gmail.com',
                                     $password = 'ashishpferdeinsider',
                                     $service = 'youtube',
                                     $client = null,
                                     $source = 'youtubeproject',
                                     $loginToken = null,
                                     $loginCaptcha 	= null,
                                     $authenticationURL
                );
        if($httpClient)
        {
            echo 'logged in successfully';
        }

	$devkey = 'AI39si4CirAhkOS_pbvBgWJgGkuZROWEgtndI4xnshvdaNxojCX7y-h1-qMOyNUN89ehoud4pIlZuoPT0uACA3hHlT2-aV2ENw';

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
        
        //echo "<pre>";print_r($tokenArray);die('sjsj');
        // place to redirect user after upload
        $nextUrl = 'http://54.228.195.42/magento/youtubeproject/library/index.php';

        echo $form = '<form action="'. $postUrl .'?nexturl='. $nextUrl .
                '" method="post" enctype="multipart/form-data">'.
                '<input name="file" type="file"/>'.
                '<input name="token" type="hidden" value="'. $tokenValue .'"/>'.
                '<input value="Upload Video File" type="submit" />'.
                '</form>'; 
      ?>
<?php
            $status=$_GET['status'];
            $unique_id=$_GET['id'];
        if( $unique_id != '' && $status = '200' ) {?>
        <div id="video-success">
            <h4>Video Successfully Uploaded!</h4>
            <p>Video's usually take around 2-3 hours to get accepted by youtube. Please check back soon.</p>
            <p>Here is your url to view your video:<a href="http://www.youtube.com/watch?v=<?php echo $unique_id; ?>" target="_blank">http://www.youtube.com/watch?v=<?php echo $unique_id; ?></a></p>
        </div> <!-- /div#video-success -->
<?php }?>