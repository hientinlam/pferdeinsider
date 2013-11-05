initRTE = function(){


tinyMCE.init({
	mode : "exact",
	language: (SFdefault_language ? SFdefault_language : 'en'),
	theme : "advanced",
	elements : "Post, post",
	plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",

	convert_urls : "false",
	theme_advanced_buttons1 : "bold,italic,underline,separator,formatselect,separator,forecolor,backcolor,separator,bullist,numlist,separator,outdent,indent,separator,undo,redo,separator,hr,removeformat,visualaid,image,simpleupload,media,link",

	theme_advanced_toolbar_location : "top",

	theme_advanced_buttons2 : " separator,sub,sup,separator, charmap,emotions,removeformat",
	theme_advanced_buttons3 : "",
	inline_styles : true,
	force_br_newlines : "true",
	relative_urls: false,
	height:400,

	skin : "o2k7",
	skin_variant : "silver",


	// Drop lists for link/image/media/template dialogs
	template_external_list_url : "js/template_list.js",
	external_link_list_url : "js/link_list.js",
	external_image_list_url : "js/image_list.js",
	media_external_list_url : "js/media_list.js",
	autosave_ask_before_unload : false // Disable for example purposes
});

}
