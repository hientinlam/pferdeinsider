/*product page tabs*/
  function tabs(x)
  {
    var lis=document.getElementById("sidebarTabs").childNodes; //gets all the LI from the UL
 
    for(i=0;i<lis.length;i++)
    {
      lis[i].className=""; //removes the classname from all the LI
    }
    x.className="selected"; //the clicked tab gets the classname selected
    var res=document.getElementById("tabContent");  //the resource for the main tabContent
    var tab=x.id;
    switch(tab) //this switch case replaces the tabContent
    {
      case "tab1":
           res.innerHTML="";
        res.innerHTML=document.getElementById("tab1Content").innerHTML;
        break;
 
      case "tab2":
          res.innerHTML="";
        res.innerHTML=document.getElementById("tab2Content").innerHTML;
      
        break;
      case "tab3":
          res.innerHTML="";
        res.innerHTML=document.getElementById("tab3Content").innerHTML;
       
        break;
         case "tab4":
             res.innerHTML="";
        res.innerHTML=document.getElementById("tab4Content").innerHTML;
        
        break;
      default:
        res.innerHTML=document.getElementById("tab1Content").innerHTML;
        break;
 
    }
  }
  

  
    function tabdata(x)
    {
   
    var tab=x.id;
    
    switch(tab) //this switch case replaces the tabContent
    {
      case "tab1":
        document.getElementById("linktype").value='video';
        document.getElementById("tab1Content").style.display='block';
        document.getElementById("tab2Content").style.display='none';
        document.getElementById("tab3Content").style.display='none';
        document.getElementById("tab4Content").style.display='none';
        document.getElementById("tab5Content").style.display='none';
       
        document.getElementById("tab2").className='';
        document.getElementById("tab3").className='';
        document.getElementById("tab4").className='';
        document.getElementById("tab5").className='';
        document.getElementById('tab1').className='selected';
        break;
 
      case "tab2":
        document.getElementById("linktype").value='audio';
        document.getElementById("tab2Content").style.display='block';
        document.getElementById("tab1Content").style.display='none';
        document.getElementById("tab3Content").style.display='none';
        document.getElementById("tab4Content").style.display='none';
        document.getElementById("tab5Content").style.display='none';
        
        document.getElementById('tab2').className='selected';
        document.getElementById('tab3').className='';
        document.getElementById('tab4').className='';
        document.getElementById("tab5").className='';
        document.getElementById("tab1").className= '';
        break;
      
      case "tab3":
        document.getElementById("linktype").value='ebook';
        document.getElementById("tab3Content").style.display='block';
        document.getElementById("tab1Content").style.display='none';
        document.getElementById("tab2Content").style.display='none';
        document.getElementById("tab4Content").style.display='none';
        document.getElementById("tab5Content").style.display='none';
        
        document.getElementById("tab1").className='';
        document.getElementById("tab2").className='';
        document.getElementById("tab4").className='';
        document.getElementById("tab5").className='';
        document.getElementById('tab3').className='selected';
        break;
       
     case "tab4":
        document.getElementById("linktype").value='photo';
        document.getElementById("tab4Content").style.display='block';
        document.getElementById("tab1Content").style.display='none';
        document.getElementById("tab2Content").style.display='none';
        document.getElementById("tab3Content").style.display='none';
        document.getElementById("tab5Content").style.display='none';
        document.getElementById("tab1").className='';
        document.getElementById("tab3").className='';
        document.getElementById("tab2").className='';
        document.getElementById("tab5").className='';
        document.getElementById('tab4').className='selected';
        break;
        
    case "tab5":
        document.getElementById("tab4Content").style.display='none';
        document.getElementById("tab1Content").style.display='none';
        document.getElementById("tab2Content").style.display='none';
        document.getElementById("tab3Content").style.display='none';
        document.getElementById("tab5Content").style.display='block';
        document.getElementById("tab1").className='';
        document.getElementById("tab3").className='';
        document.getElementById("tab2").className='';
        document.getElementById("tab5").className='selected';
        document.getElementById('tab4').className='';
        break;
        
     default:
        document.getElementById("tab1Content").style.display='block';
        break;
 
    }
  }
  
  
  
    function viewtabdata(x)
    {
   
    var tab=x.id;
    
    switch(tab) //this switch case replaces the tabContent
    {
      case "tab1":
        document.getElementById("tab1Content").style.display='block';
        document.getElementById("tab2Content").style.display='none';
        document.getElementById("tab3Content").style.display='none';
        document.getElementById("tab4Content").style.display='none';
        
        document.getElementById("tab2").className='';
        document.getElementById("tab3").className='';
        document.getElementById("tab4").className='';
        
        document.getElementById('tab1').className='selected';
        break;
 
      case "tab2":
        document.getElementById("tab2Content").style.display='block';
        document.getElementById("tab1Content").style.display='none';
        document.getElementById("tab3Content").style.display='none';
        document.getElementById("tab4Content").style.display='none';
        
        document.getElementById('tab2').className='selected';
        document.getElementById('tab3').className='';
        document.getElementById('tab4').className='';
       
        document.getElementById("tab1").className= '';
        break;
      
      case "tab3":
        document.getElementById("tab3Content").style.display='block';
        document.getElementById("tab1Content").style.display='none';
        document.getElementById("tab2Content").style.display='none';
        document.getElementById("tab4Content").style.display='none';
        
        document.getElementById("tab1").className='';
        document.getElementById("tab2").className='';
        document.getElementById("tab4").className='';
      
        document.getElementById('tab3').className='selected';
        break;
       
     case "tab4":
        document.getElementById("tab4Content").style.display='block';
        document.getElementById("tab1Content").style.display='none';
        document.getElementById("tab2Content").style.display='none';
        document.getElementById("tab3Content").style.display='none';
       
        document.getElementById("tab1").className='';
        document.getElementById("tab3").className='';
        document.getElementById("tab2").className='';
       
        document.getElementById('tab4').className='selected';
        break;
        
   
        
     default:
        document.getElementById("tab1Content").style.display='block';
        break;
 
    }
    }
  
 function tab(y)
  {
    var lis=document.getElementById("sidebarTab").childNodes; //gets all the LI from the UL
 
    for(i=0;i<lis.length;i++)
    {
      lis[i].className=""; //removes the classname from all the LI
    }
    y.className="selected"; //the clicked tab gets the classname selected
    var res=document.getElementById("tabContents");  //the resource for the main tabContent
    var tab=y.id;
    switch(tab) //this switch case replaces the tabContent
    {
      case "tab5":
        res.innerHTML=document.getElementById("tab5Content").innerHTML;
        break;
 
      case "tab6":
        res.innerHTML=document.getElementById("tab6Content").innerHTML;
        break;
        default:
        res.innerHTML=document.getElementById("tab5Content").innerHTML;
        break;
 
    }
  }

  /*expert page tabs*/
  
function tabs4()
  {
      document.getElementById('tabContent2').style.display='block';
      //document.getElementById('subscribemember').style.display='none';
      document.getElementById('training').style.display='none';
      document.getElementById('tab5').className='selected';
      document.getElementById('tab7').className='';
      document.getElementById('tab6').className='';
  }  
function tabs5()
{
  document.getElementById('training').style.display='block';
  document.getElementById('tabContent2').style.display='none';
  //document.getElementById('subscribemember').style.display='none';
  document.getElementById('tab7').className='';
  document.getElementById('tab5').className='';
  document.getElementById('tab6').className='selected';
}  
function tabs6()
  {
      //document.getElementById('subscribemember').style.display='block';
      document.getElementById('tabContent2').style.display='none';
      document.getElementById('training').style.display='none';
      document.getElementById('tab7').className='selected';
      document.getElementById('tab5').className='';
      document.getElementById('tab6').className='';
  }
  
/*input field*/  

function start_typing3()
{
    var val = document.getElementById('typings').value;
    if (val == 'Contains these words...')
         {
             document.getElementById('typings').value="";
         }
}
function typing4(val)
{
   if (val=='')
         {
             document.getElementById('typings').value='Contains these words...';
         }
}

function start_typing()
{
    var val = document.getElementById('typings').value;
    if (val == 'Contains these words...')
         {
             document.getElementById('typings').value="";
         }
}
function typing(val)
{
   if (val=='')
         {
             document.getElementById('typings').value='Contains these words...';
         }
}

function showSearch()
{ 
    document.getElementById('search_show').style.display='block';
}



function showhide()
{ 
    document.getElementById('hide_show').style.display='block';
}

/*email field*/

function showtext()
{
var val = document.getElementById('email').value;
if (val == 'Your Email Here...')
{
document.getElementById('email').value="";
}
}
function eml(val)
{
if (val=='')
{
document.getElementById('email').value='Your Email Here...';
}
}

function sessiondiv(x)
    {
   
        var tab=x.id;
        if(document.getElementById('ul-'+tab).style.display=='none')
            {
                   document.getElementById(tab).style.background = "url(http://54.228.195.42/magento/skin/frontend/default/videoplatform/images/arrow_2.png) no-repeat";
                   document.getElementById(tab).style.backgroundPosition="10px";
                   document.getElementById('ul-'+tab).style.display='block';
            }
        else{
            document.getElementById(tab).style.background = "url(http://54.228.195.42/magento/skin/frontend/default/videoplatform/images/arrow_3.png) no-repeat";
             document.getElementById(tab).style.backgroundPosition="10px";
             document.getElementById('ul-'+tab).style.display='none';
        }
    }
