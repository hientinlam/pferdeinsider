

   <!-- <div class="entry-edit">
    <div class="entry-edit-head" style=" background: none repeat scroll 0 0 #6F8992; padding: 2px;height:19px;">
        <h4 style="background: none repeat scroll 0px 0px transparent; color: rgb(255, 255, 255); float: left; margin: 0px; min-height: 0px; padding: 0px; font: bold 13px/1.5em Arial,Helvetica,sans-serif;" class="icon-head head-edit-form fieldset-legend">Upload Video on Youtube</h4></div>
   <fieldset style=" background: none repeat scroll 0 0 #FAFAFA;border: 1px solid #D6D6D6;margin-bottom: 15px; padding: 10px 15px;" id="grop_fields">-->
<?php
 $status=$_GET['status'];
            $unique_id=$_GET['id'];
  $categories =array("Film"=>"Film &amp; Animation",
                    "Autos"=>"Autos &amp; Vehicles",
                    "Music"=>"Music",
                    "Animals"=>"Pets &amp; Animals",
                    "Sports"=>"Sports",
                    "Shortmov"=>"Short Movies",
                    "Travel"=>"Travel &amp; Events",
                    "Games"=>"Gaming",
                    "Videoblog"=>"Videoblogging",
                    "People"=>"People &amp; Blogs",
                    "Comedy"=>"Comedy",
                    "Entertainment"=>"Entertainment",
                    "News"=>"News &amp; Politics",
                    "Howto"=>"Howto &amp; Style",
                    "Education"=>"Education",
                    "Tech"=>"Science &amp; Technology",
                    "Nonprofit"=>"Nonprofits &amp; Activism",
                    "Movies"=>"Movies",
                    "Movies_anime_animation"=>"Anime/Animation",
                    "Movies_action_adventure"=>"Action/Adventure",
                    "Movies_classics"=>"Classics",
                    "Movies_comedy"=>"Comedy",
                    "Movies_documentary"=>"Documentary",
                    "Movies_drama"=>"Drama",
                    "Movies_family"=>"Family",
                    "Movies_foreign"=>"Foreign",
                    "Movies_horror"=>"Horror",
                    "Movies_sci_fi_fantasy"=>"Sci-Fi/Fantasy",
                    "Movies_thriller"=>"Thriller",
                    "Movies_shorts"=>"Shorts",
                    "Shows"=>"Shows",
                    "Trailers"=>"Trailers");?>


<div id="upload_div">
    <form id="videoInfo" method="post">
        <ul class="form-list" style="list-style:none;padding:0px;">
      
              
                <div style="float:left;width:715px;">
                    <h1 style=' background: none repeat scroll 0 0 #666666;color: #FFFFFF;font-size: 18px;font-weight: normal;padding-bottom: 5px;padding-right: 5px;padding-top: 5px;padding-left: 15px;'>Vorschau Video für Youtube hochladen
                        
                    </h1>
                </div>  
                  <?php if( $unique_id == '' ) :?>
    
     
                <li class="field" style="color: #666666;width: 275px;">
                      <div class="field">
                             <label style=" color: #666666;padding-bottom: 5px;position: relative;z-index: 0;" class="required" for="title">Produkt Name</label>
                                 <div class="input-box">
                                     <input style="width:260px;background: none repeat scroll 0 0 #FFFFFF; border: 1px solid #B6B6B6;padding:5px;" type="text" id="title" name="title" value="<?php echo $_POST['title'];?>" class="input-text"/>
                                 </div>
                        </div>
                 </li>
                 <li class="field"  style="color: #666666;width: 275px;">
                      <div class="field">
                             <label style=" color: #666666;padding-bottom: 5px;position: relative;z-index: 0;"  class="required" for="Description"><em> </em>Beschreibung</label>
                                 <div class="input-box">
                                     <input style="width:260px;background: none repeat scroll 0 0 #FFFFFF; border: 1px solid #B6B6B6;padding:5px;" type="text" id="description" name="description" value="<?php echo $_POST['description'];?>" class="input-text"/>
                                 </div>
                        </div>
                 </li>
                 
                 <li class="field"  style="color: #666666;width: 550px;padding-bottom:10px;">
                      <div style="float:left;width:750px;">
                             <label style=" color: #666666;padding-bottom: 5px;position: relative;z-index: 0;width:500px"  class="required" for="category"><em> </em>Kategorie in Youtube</label>
                                  <div class="category-choose" style=" background: url('../html/images/drop.png') no-repeat scroll 0 0 transparent;border: 2px solid #999999;height: 35px;overflow: hidden;width: 194px;"/>
                                     
                                         <select class="choose"  name="mvideo_category" id="mvideo_category" style="background: none repeat scroll 0 0 transparent;border: medium none;color: #ABABAB;float: left;font-family: Arial;font-size: 14px;height: 30px;overflow: hidden;padding-top: 8px;width: 214px !important;" name="productcategory"><option>--SELECT--</option>
                                        <?php foreach($categories as $key=>$category):?>
                                            <option <?php if($key=="Animals"){ echo 'selected=selected';}?> value="<?php echo $key;?>"><?php echo $category;?></option>
                                         <?php endforeach;?>
                                            </select>
                                    
                                 </div>
                         </div>
                   
                         
                 </li>
                
             <?php if(!$_POST){ ?>
 
                   <li class="field"  style="color: #666666;width: 300px;padding-bottom:10px;">
                   
                      <div class="button" style="width:100px;float:right;padding:35px 35px 0px 0px;">
                          <div class="button-set">
                           <button  style="padding:5px;cursor:pointer;color:#FFFFFF;background:#333333;width:100px;border:0 none;" class="scalable save" type="submit"><span style="background:#333333;color:#FFFFFF;width:100px;padding:5px;">Weiter</span></button>
                          </div>
                         </div>
                         
                 </li>
                 <?php } ?>
        </ul>
         <?php endif; ?>
        
              <!--  <table cellspacing="0" class="form-list">
            <tbody>
                <tr>
                    <td class="label" style=""><label for="title" style=" display: block;padding-right: 15px;padding-top: 1px;width: 185px;">Title <span class="required">*</span></label></td>
                    <td class="value"><input style="background: none repeat scroll 0 0 #FFFFFF;border-color: #AAAAAA #C8C8C8 #C8C8C8 #AAAAAA;border-style: solid;border-width: 1px;font: 12px arial,helvetica,sans-serif;"type="text" id="title" name="title" value="<?php echo $_POST['title'];?>" class="input-text"></td>
                    <td class="scope-label"></td>
                    <td><small>&nbsp;</small></td>
                </tr>
                <tr>
                    <td class="label"><label for="description">Description <span class="required">*</span></label></td>
                    <td class="value"><input type="text" id="description" name="description" value="<?php echo $_POST['description'];?>" class="input-text"></td>
                    <td class="scope-label"></td>
                        <td><small>&nbsp;</small></td>
                </tr>
                <tr>
                    <td class="label"><label for="weight">Category </label></td>
                    <td class="value">
                        <select name="mvideo_category" style="width:200px" id="mvideo_category">                        
                           <?php foreach($categories as $key=>$category){?>
                                    <option <?php if($key==$_POST['mvideo_category']){ echo 'selected=selected';}?> value="<?php echo $key;?>"><?php echo $category;?></option>
                           <?php }?>
                        </select>
                    </td>
                    <td class="scope-label"></td>
                        <td><small>&nbsp;</small></td>
                </tr>
            </tbody>
        </table>-->


    </form>
</div>







<?php
if(isset($_POST['title']) && $_POST['title']!=NULL && isset($_POST['description']) && $_POST['description']!=NULL && isset($_POST['mvideo_category']) &&  $_POST['mvideo_category']!=NULL){
   // echo "<pre>";print_r($_POST);die("here");
//require_once '../lib/Zend/Loader.php'; 
//require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path
require_once '../app/Mage.php';
Mage::app();

Zend_Loader::loadClass('Zend_Gdata_YouTube');
$yt = new Zend_Gdata_YouTube();
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

$title=$_POST['title'];
$description=$_POST['description'];
$category=$_POST['mvideo_category'];
$authenticationURL = 'https://www.google.com/accounts/ClientLogin';

$httpClient = Zend_Gdata_ClientLogin::getHttpClient(
				     $email = 'pferdeinsider.com@gmail.com',
                                     $password = 'biscuit321!',
                                     $service = 'youtube',
                                     $client = null,
                                     $source = 'youtubeproject',
                                     $loginToken = null,
                                     $loginCaptcha 	= null,
                                     $authenticationURL
                );
      

if($httpClient){
        $devkey = 'AI39si4CirAhkOS_pbvBgWJgGkuZROWEgtndI4xnshvdaNxojCX7y-h1-qMOyNUN89ehoud4pIlZuoPT0uACA3hHlT2-aV2ENw';
        $yt = new Zend_Gdata_YouTube($httpClient, '', '', $devkey);

        $myVideoEntry= new Zend_Gdata_YouTube_VideoEntry();
        $myVideoEntry->setVideoTitle("$title");
        $myVideoEntry->setVideoDescription("$description");

        // Note that category must be a valid YouTube category
        $myVideoEntry->setVideoCategory("$category");
        $myVideoEntry->SetVideoTags("$title");

        $tokenHandlerUrl = 'http://gdata.youtube.com/action/GetUploadToken';
        $tokenArray = $yt->getFormUploadToken($myVideoEntry, $tokenHandlerUrl);

        $tokenValue = $tokenArray['token'];
        $postUrl = $tokenArray['url'];

        // place to redirect user after upload
        $nextUrl = 'http://54.228.195.42/magento/youtube/index.php';

        echo $form = '<form action="'. $postUrl .'?nexturl='. $nextUrl .
                '" method="post" enctype="multipart/form-data">'.
                '<div><input style="padding:5px;color:#FFFFFF;border:0 none;margin-top:10px;" name="file" id="file_upload" type="file"/></div>'.
                '<input name="token" type="hidden" value="'. $tokenValue .'"/>'.
                '<div style="float:left;">
                    <input style="cursor:pointer;padding:5px;color:#FFFFFF;background:#333333;width:150px;border:0 none;margin-top:10px;" value="Upload Video File" type="submit" /></div>'.
                '</form>';
}
}
        ?>
     <input type="hidden" name="youtubestatus" id="youtubestatus" value="<?php echo $status; ?>"/>
<?php
          
        if( $unique_id != '' && $status = '200' ) :?>
                
       <input type="hidden" name="youtubeurl" id="youtubeurl" value="http://www.youtube.com/watch?v=<?php echo $unique_id; ?>"/>
        <div style="float:left;" id="video-success">
            <h4 style="color:#666666;font-family:verdana;font-size:15px;">Video has been successfully uploaded!</h4>
            
        
        </div> <!-- /div#video-success -->
        <div style="position: relative;top:13px;left:10px;">
         
         <a style="padding:5px;float:left;font-family:verdana;color:#FFFFFF;cursor:pointer;background:#333333;width:100px;font-size:14px;text-align:center;text-decoration:none;" class="button" href="#" onclick="parent.jQuery.fancybox.close();">Continue</a>    
        </div>
<?php endif; ?>
  