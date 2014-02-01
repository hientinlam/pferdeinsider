<?php
	
	//Which bucket are we placing our files into
	$bucket = 'nmjkjhk';
	// This will place uploads into the '20100920-234138' folder in the $bucket bucket
	$folder = date('Ymd-His').'/'; //Include trailing /

	//Include required S3 functions
	require_once "includes/s3.php";

	//Generate policy and signature
	list($policy, $signature) = S3::get_policy_and_signature(array(
		'bucket' 		=> $bucket,
		
	));
?>
<?php $uploadURL = 'http://'.$bucket.'.s3.amazonaws.com/';?>
<html>
<head>
<title>test Upload</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="files/uploadify/uploadify.css" />
<script type='text/javascript' src="files/jquery.js"></script>
<script type='text/javascript' src="files/uploadify/swfobject.js"></script>
<script type='text/javascript' src="files/uploadify/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
                  var fileExt = "";
                  var fileDesc = "";
                  fileExt = "*.mp3;*.pdf*;.mp4;*.AVI;*.FLV;*.MPEG;*.MOV;*.mts;*.AVCHD;*.avi;*.flv;*.ogv;*.m4v;*.f4v;*.webm;*.MP4;*.jpg;*.jpeg;*.png;";
                 // fileExt = "*.gif;*.doc;*.docx;*.pdf";
                  fileDesc = "Allowed Files (.pdf,.MP3,.mp4,.AVI,.FLV,.MPEG,.MOV,.mts,.AVCHD,.avi,.flv,.ogv,.m4v,.f4v,.webm,.MP4,.jpg;.jpeg;.png;)";
		$("#file_upload").uploadify({
                   

                       
			'uploader'		: 'files/uploadify/uploadify.swf',
			'buttonText'	: 'Browse',
			'cancelImg'		: 'files/uploadify/cancel.png',
			'script'		: 'http://s3.amazonaws.com/<?= $bucket ?>',
                        //'script'                :'http://nmjkjhk.s3.amazonaws.com/',
			'scriptAccess'	: 'always',
			'method'		: 'post',
			'scriptData'	: {
                            
				"AWSAccessKeyId"			: "AKIAJJZZ476KF7TIK64Q",
				"key"						: "${filename}",
				"acl"						: "public-read",
				"policy"					: "<?= $policy ?>",
				"signature"					: "<?= $signature ?>",
				"success_action_status"		: "201",
				"key"						: encodeURIComponent(encodeURIComponent("${filename}")),
				"fileext"					: encodeURIComponent(encodeURIComponent("")),
				"Filename"					: encodeURIComponent(encodeURIComponent(""))
			},
			'fileExt'  : fileExt,
                        'fileDesc'    : fileDesc,
			'fileDataName' 	: 'file',
			'simUploadLimit': 20,
			'multi'			: true,
			'auto'			: true,
			'onError' 		: function(errorObj, q, f, err) { console.log(err); },
			'onComplete'	: function(event, ID, file, response, data) {

                             var get_end_loc=response.indexOf('</Location>');
                             var sub_loc=response.substring(0,get_end_loc);
                             var get_loc=sub_loc.indexOf('<Location>');
                             var substr_fronta=sub_loc.substring(get_loc+10);
                            // var paths=document.getElementById('result').innerHTML;
                             document.getElementById('result').innerHTML='<div><h4 style="color:#666666;font-family:verdana;font-size:15px;">Successfully Uploaded</h4></div> <div id="" style="position:relative;bottom:45px;left:187px;"><a id="" href="#" style="cursor:pointer;padding:4px;font-family:verdana;font-size:14px;float:left;color:#FFFFFF;background:#666666;width:85px;text-align:center;text-decoration:none;" class="button" onclick="parent.jQuery.fancybox.close();">Go Back</a></div>';
                            document.getElementById('s3url').value=substr_fronta;
                            
                            console.log(file); }
		});
	});
        
    
</script>


</head>
<body>
 
          
                <div style="float:left;width:635px;">
                    <h1 style=' background: none repeat scroll 0 0 #666666;color: #FFFFFF;font-size:15px;font-family:verdana;font-weight: normal;padding-bottom: 5px;padding-right: 5px;padding-top: 5px;padding-left: 15px;'>neues Produkt hochladen
                        
                    </h1>
                </div>  
                 
      
          
	<div align='center'>
		<input type='file' id='file_upload' name='file_upload' />
	</div>
        <div id="result" name="linkurl"></div>
        
        <input type='hidden' id='s3url' name='s3url' value="" />
</body>
</html>