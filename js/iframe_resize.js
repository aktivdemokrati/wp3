function getElementbyClass(classname)
{
 var inc=0;
 var arr = [];
 var alltags=document.all? document.all : document.getElementsByTagName("*");
 for (i=0; i<alltags.length; i++)
 {
   if (alltags[i].className==classname)
     arr[inc++]=alltags[i];
 }
 return arr;
}


/* Modified. Based on: */

/***********************************************
* IFrame SSI script II- © Dynamic Drive DHTML code library (http://www.dynamicdrive.com)
* Visit DynamicDrive.com for hundreds of original DHTML scripts
* This notice must stay intact for legal use
***********************************************/


//extra height in px to add to iframe in FireFox 1.0+ browsers
var getFFVersion=navigator.userAgent.substring(navigator.userAgent.indexOf("Firefox")).split("/")[1]
//var FFextraHeight=parseFloat(getFFVersion)>=0.1?16 : 0
var FFextraHeight=parseFloat(getFFVersion)>=0.1?34 : 0
var FFextraWidth=parseFloat(getFFVersion)>=0.1?20: 0

function resizeCaller()
{
  var dyniframe=new Array();
  iframeobjs = getElementbyClass("iframe_autoresize");
  for(i=0; i<iframeobjs.length; i++)
  {
    resizeIframe(iframeobjs[i]);
  }
}

function resizeIframe(currentfr)
{
  if( currentfr && !window.opera )
  {
    currentfr.style.display="block";
    if( currentfr.contentDocument && currentfr.contentDocument.body.offsetHeight ) //ns6 syntax
    {
      currentfr.height = currentfr.contentDocument.body.offsetHeight+FFextraHeight; 
//      currentfr.width = currentfr.contentDocument.body.scrollWidth+FFextraWidth;
    }
    else if( currentfr.Document && currentfr.Document.body.scrollHeight ) //ie5+ syntax
    {
      currentfr.height = currentfr.Document.body.scrollHeight;
//      currentfr.width = currentfr.Document.body.scrollWidth;
    }

    if (currentfr.addEventListener)
      currentfr.addEventListener("load", readjustIframe, false)
    else if (currentfr.attachEvent)
    {
      currentfr.detachEvent("onload", readjustIframe) // Bug fix line
      currentfr.attachEvent("onload", readjustIframe)
    }
  }
}

function readjustIframe(loadevt) {
var crossevt=(window.event)? event : loadevt
var iframeroot=(crossevt.currentTarget)? crossevt.currentTarget : crossevt.srcElement
if (iframeroot)
resizeIframe(iframeroot.id);
}

function loadintoIframe(iframeid, url){
if (document.getElementById)
document.getElementById(iframeid).src=url
}

function onLoadPage() {
if (window.addEventListener)
window.addEventListener("load", resizeCaller, false)
else if (window.attachEvent)
window.attachEvent("onload", resizeCaller)
else
window.onload=resizeCaller
}
