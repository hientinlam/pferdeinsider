function submitcode()
     {
         jQuery("#user-form-loader").show();

         var affiliateId=document.getElementById('affiliateId').value;
         var campaignId=document.getElementById('campaignId').value;
         var current_url=document.getElementById('current_url').value;
         var defaulturl=document.getElementById('defaulturl').value;
        
         if(affiliateId.length == 0)
             { 
                 jQuery("#user-form-loader").hide();
                 var url=current_url;
                 var urlRegex1 = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
                 var urltest1=urlRegex1.test(url);
                 if(urltest1)
                    {
                      bit_url(url);
                    }
             }
         else{
                 
         jQuery.ajax({
                        url:defaulturl+"affiliate/customer_affiliate/code",
                        type:"post",
                        dataType:"json",
                        data:({
                                 affiliate_Id:affiliateId,
                                 campaign_Id:campaignId,
                                 current_url:current_url
                             }),
                            success:function(data)
                            {
                                if(data.success=='success')
                                    {
                                        var showiframe =data.id; 
                                        jQuery("#user-form-loader").hide();
                                        var url=showiframe;
                                        var urlRegex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
                                        var urltest=urlRegex.test(url);

                                        if(urltest)
                                        {
                                            bit_url(url);
                                        }
                                        else
                                        {
                                            alert("Bad URL");
                                        }
                                     }
                            }
                    });
                 }
      }
       function submitcomment()
         {
             jQuery("#user-form-loade").show();
             var customer_id=document.getElementById('customer_id').value;
             var product_id=document.getElementById('product_id').value;
             var posted_date=document.getElementById('posted_date').value;
             var comment=document.getElementById('comment_section').value;
             var base_url=document.getElementById('base_url').value;
             var image=document.getElementById('imageid').value;
           if(comment.length != 0)
               {
            jQuery.ajax({
                            url:base_url+"/comment/index/save",
                            type:"post",
                            dataType:"json",
                            data:({
                                     customer_id:customer_id,
                                     product_id:product_id,
                                     posted_date:posted_date,
                                     comment:comment
                                  }),
                                success:function(data)
                                {
                                    if(data.success=='success')
                                        {
                                           jQuery("#mylist li").size();
                                           jQuery("#mylist").append('<li style="width:960px;float:left;border-bottom:1px dashed;height:100px;padding-top:10px;">\n\
                                                                     <div> <div style="float:left;width:100px;"><img src="'+image+'" height="75" width="75" alt="" /></div></div>\n\
                                                                    <div><div style="width:730px;">'+comment+'</div>\n\
                                                                    <div style="font-style:italic;float:left">Posted Date'+posted_date+'</div>\n\
                                                                    </div></li>');
                                             jQuery("#user-form-loade").hide();
                                        }
                                }
                        });
         }
         }
      function submitcode_order(current_url,affiliateId,campaignId,defaulturl,count)
      {
         var cnt=count;
      
         jQuery.ajax({
                        url:defaulturl+"affiliate/customer_affiliate/code",
                        type:"post",
                        dataType:"json",
                        data:({
                                 affiliate_Id:affiliateId,
                                 campaign_Id:campaignId,
                                 current_url:current_url
                             }),
                            success:function(data)
                            {
                                if(data.success=='success')
                                    {
                                        var showiframe =data.id; 
                                        
                                        var url=showiframe;
                                        var urlRegex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
                                        var urltest=urlRegex.test(url);

                                        if(urltest)
                                        {
                                            bit_url1(url,cnt);
                                        }
                                        else
                                        {
                                            alert("Bad URL");
                                        }
                                        //jQuery('#ifrm-'+cnt).attr('src', "//www.facebook.com/plugins/like.php?href="+showiframe+"&amp;send=false&amp;layout=standard&amp;width=450&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=428249980602262");
                                       // jQuery('#tweet-button-'+cnt).attr('src', "http://platform.twitter.com/widgets/tweet_button.html?url="+showiframe);
                                       // jQuery.getScript('http://platform.twitter.com/widgets.js');
                               }
                            }
                    });
                 
      }
            
 function bit_url1(url,cnt) 
        { 
            
            var url=url;
            var cnt=cnt;
            var username="brsttest"; // bit.ly username
            var key="R_7d2ed78e5e0184eb52579fa1422f9952";
            jQuery.ajax({
            url:"http://api.bit.ly/v3/shorten",
            data:{longUrl:url,apiKey:key,login:username},
            dataType:"jsonp",
            success:function(v)
            {
                var bit_url=v.data.url;
              //  document.getElementById('fbLike').setAttribute('href', bit_url);
                jQuery("#showdiv").html(bit_url);
                jQuery('#ifrm-'+cnt).attr('src', "//www.facebook.com/plugins/like.php?href="+bit_url +"&send=false&layout=button_count&width=450&show_faces=false&colorscheme=light&action=like&height=21&appId=428249980602262");
                jQuery('#tweet-button-'+cnt).attr('src', "http://platform.twitter.com/widgets/tweet_button.html?url="+bit_url);
                jQuery.getScript('http://platform.twitter.com/widgets.js');
               
                
            
            }
            });
        }
      
 function bit_url(url) 
        { 
            
            var url=url;
            var username="brsttest"; // bit.ly username
            var key="R_7d2ed78e5e0184eb52579fa1422f9952";
            jQuery.ajax({
            url:"http://api.bit.ly/v3/shorten",
            data:{longUrl:url,apiKey:key,login:username},
            dataType:"jsonp",
            success:function(v)
            {
                var bit_url=v.data.url;
              //  document.getElementById('fbLike').setAttribute('href', bit_url);
                jQuery("#showdiv").html(   bit_url);
                jQuery('#ifrm').attr('src', "//www.facebook.com/plugins/like.php?href="+bit_url +"&send=false&layout=button_count&width=450&show_faces=false&colorscheme=light&action=like&height=21&appId=428249980602262");
                jQuery('#tweet-button').attr('src', "http://platform.twitter.com/widgets/tweet_button.html?url="+bit_url);
                jQuery.getScript('http://platform.twitter.com/widgets.js');
               
                
            
            }
            });
        }
  
 