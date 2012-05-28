var xmlhttp;
var target;
var reply_to;
var quene;
var postStatus;
var FBID;
var pref_default;

reply_to = "";
quene = [];
postStatus = [];

function createAJAX() {
	if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
	}
	if (window.ActiveXObject) {
		return new ActiveXObject("Microsoft.XMLHTTP");
	}
	return null;
}

function setFBID(fbid) { FBID = fbid; }
function setDefault(def) { pref_default = def; }

function loadIMG(name,url) {
	document.getElementById("mdlTitle").innerHTML = name;
	document.getElementById("mdlBody").innerHTML = '<img src="' + url + '" />';
	document.getElementById("mdlFooter").innerHTML = '';
}

function loadIFrame(url) {
	document.getElementById("mdlTitle").innerHTML = 'Website preview';
	document.getElementById("mdlBody").innerHTML = '<img src="http://immediatenet.com/t/l?Size=1024x768&URL=' + url + '" />';
	document.getElementById("mdlFooter").innerHTML = '<p><a class="btn btn-primary" href="' + url + '" target="_BLANK" onclick="$(\'#mdl\').modal(\'hide\')">Visit website</a>';
}

function loadYT(code) {
	document.getElementById("mdlTitle").innerHTML = 'YouTube';
	document.getElementById("mdlBody").innerHTML = '<iframe width="530" height="299" src="http://www.youtube.com/embed/' + code + '" frameborder="0" allowfullscreen></iframe>';
	document.getElementById("mdlFooter").innerHTML = '<p><a class="btn btn-primary" href="http://youtu.be/' + code + '" target="_BLANK" onclick="$(\'#mdl\').modal(\'hide\')">Visit website</a>';
}

function reset() {
	if ( FBID > 0 && pref_default == "facebook" ) { 
		ajax('/lib/facebook/index.php?call=/'+FBID, 'span3');
	}	
	else {
		ajax('/lib/twitter/index.php?call=account/verify_credentials','span3');
	}
}

function setRegion(area) {
	xmlhttp=createAJAX();
	xmlhttp.open("GET", "/settings/getTimezone/"+area, false);
	xmlhttp.send(null);
	var data = xmlhttp.responseText;
	data = data.split('"]');
	data = data[0] + '"]';
	data = jQuery.parseJSON(data);
	var tz = '';

	for ( i=0;i<data.length;i++ ) {
		tz += "<option>"+data[i]+"</option>"; 
	}

	document.getElementById("tz").innerHTML = tz;
}

function fbShowComments(id) {
	ajax('/lib/facebook/index.php?call=/'+id+'/comments', 'span3');
	$("html, body").animate({ scrollTop: 0 }, "slow");
}

function fbComment(id,obj) {
	var node = obj.getElementsByTagName("input");
	var data = node[0].value;
	post('/lib/facebook/index.php','call=/' + id + '/comments&message=' + encodeURIComponent(data), 'postM'); 
	$("html, body").animate({ scrollTop: 0 }, "slow");
}

function fbLike(id) {
	post('/lib/facebook/index.php','call=/' + id + '/likes', 'postM'); 
	$("html, body").animate({ scrollTop: 0 }, "slow");
}

function status() {
	if ( document.getElementById('nTweet').value == "" ) { 
		document.getElementById("postM").className += " warning"; 
		document.getElementById("postMHelp").innerHTML = "No message? :(";
		return;
	}	
	if ( postStatus['Twitter'] == 'true' ) {
		post('/lib/twitter/index.php?call=statuses/update','status=' + encodeURIComponent(document.getElementById('nTweet').value), 'postM'); 
	}
	if ( postStatus['Facebook'] == 'true' ) {
		post('/lib/facebook/index.php','call=/' + FBID + '/feed&message=' + encodeURIComponent(document.getElementById('nTweet').value), 'postM'); 
	}
}

$(document).ready(function(){
	$("#span1,#span2").mouseenter(function(){
		$(".msgRow").mouseenter(function(){
			$(".twToolBox",this).css("visibility","visible");
		}).mouseleave(function(){
			$(".twToolBox",this).css("visibility","hidden");
		});
	});
	$('a[href=#top]').click(function(){
		$('html, body').animate({scrollTop:0}, 'slow');
		return false;
    });
	$("#search").keyup(function(event){
		if(event.keyCode == 13){
			var data = document.getElementById("search").value;
			document.getElementById('controll_search').className += " success"; 
			setTimeout("document.getElementById('controll_search').className = document.getElementById('controll_search').className.replace( /(?:^|\s)success(?!\S)/ , '' );", 5000);
			ajax('/lib/twitter/index.php?call=search&q=' + encodeURIComponent(data) + '&include_entities=true', 'span3'); 
		}
	});
});

function setStatus(serv, value) {
	if ( serv !== null ) { postStatus[serv] = value; }
	document.getElementById("postPrefs").innerHTML = 'Twitter ';
	if ( postStatus['Twitter'] == 'true' ) {
		document.getElementById("postPrefs").innerHTML += '<a onclick="setStatus(\'Twitter\', \'false\');"><i class="icon-ok"></i></a>';
	}
	else {
		document.getElementById("postPrefs").innerHTML += '<a onclick="setStatus(\'Twitter\', \'true\');"><i class="icon-remove"></i></a>';
	}
	document.getElementById("postPrefs").innerHTML += ' | Facebook ';
	if ( postStatus['Facebook'] == 'true' ) {
		document.getElementById("postPrefs").innerHTML += '<a onclick="setStatus(\'Facebook\', \'false\');"><i class="icon-ok"></i></a>';
	}
	else {
		document.getElementById("postPrefs").innerHTML += '<a onclick="setStatus(\'Facebook\', \'true\');"><i class="icon-remove"></i></a>';
	}
}
	
function ajax(url,trgt) {
	if ( url !== null ) { quene[quene.length] = [url,trgt]; }
	if ( quene.length > 5 ) { 
		quene.shift(); 
		document.getElementById("quene").className += " badge-error"; 
	}
	else { document.getElementById("quene").className = document.getElementById("quene").className.replace( /(?:^|\s)badge-error(?!\S)/ , '' ); }
	document.getElementById("quene").innerHTML = quene.length;
	
	var current = quene[0];
	xmlhttp=createAJAX();
	xmlhttp.onreadystatechange=stateChanged;
	xmlhttp.open("GET", current[0], true);
	xmlhttp.send(null);
	target = current[1];
	if ( target == "span3" ) { document.getElementById('span3').innerHTML = '<h2>User Info</h2><div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div>'; }
}

function post(url,data,trgt) {
	xmlhttp=createAJAX();
	xmlhttp.onreadystatechange=stateChanged;
	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	if ( reply_to !== "" ) {
		data += "&in_reply_to_status_id="+reply_to;
		reply_to = "";
	}
	xmlhttp.send(data);
	target = trgt;
	if ( target == "span3" ) { document.getElementById('span3').innerHTML = '<h2>User Info</h2><div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div>'; }
}

function stateChanged() {
/* 	if ( xmlhttp.readyState != 4 ) {
		var wdt = xmlhttp.readyState * 25;
		var node = document.getElementById(target).getElementsByTagName("div");
		node[1].style.width = wdt + "%";
	} */
	if (xmlhttp.readyState == 4 && xmlhttp.responseText != "") {
		if ( target == "postM" ) {
			if ( xmlhttp.responseText == "200" ) { 
				document.getElementById("postM").className += " success"; 
				document.getElementById("postMHelp").innerHTML = "Your update has been sent!";
				document.getElementById('nTweet').value = ''; 
				setTimeout("count('');",5000);
			}
			else if ( xmlhttp.responseText == "1" ) { 
				document.getElementById("postM").className += " success"; 
				document.getElementById("postMHelp").innerHTML = "The status has been liked!";
				document.getElementById('nTweet').value = ''; 
				setTimeout("count('');",5000);
			}
			else { 
				document.getElementById("postM").className += " error"; 
				document.getElementById("postMHelp").innerHTML = "Something went wrong... (Error "+ xmlhttp.responseText + ")";
			}
		}
		else if ( target == "ratelimit" ) {
			var data = xmlhttp.responseText.split("|");
			document.getElementById(target).innerHTML = data[0];
			document.getElementById(target).title = "Amount of remaining available Twitter-API calls till " + data[1];
			document.getElementById(target).className = "badge pull-left tt "+ data[2];
		}
		else {
			document.getElementById(target).innerHTML=xmlhttp.responseText;
		}
		if ( target == "span1" ) { // Adds Twitter icon hover
			var reg = /PLAYSND/;
			if ( reg.test(xmlhttp.responseText) ) {
				document.getElementById("sound").play();
			}
			$("img.hoverClass").mouseenter(function() {
			   var regex = /\_/;
			   if ( regex.test($(this).attr("src")) ) { return false; }
			   var oldSRC = $(this).attr("src");
			   $(this).attr("src", oldSRC.replace(".png","_hover.png"));
			}).mouseleave(function(){
				var oldSRC = $(this).attr("src");
				$(this).attr("src", oldSRC.replace("_hover.png",".png"));
			}).click(function(){
				var oldSRC = $(this).attr("src");
				$(this).attr("src", oldSRC.replace("_hover.png","_on.png"));
			});
		}
		quene.shift();
		document.getElementById("quene").innerHTML = quene.length;
		if ( quene[0] !== "" ) { ajax(null,null); }
	}
}

function reply(user,twid) {
	postStatus["Twitter"] = 'true';
	postStatus["Facebook"] = 'false';
	setStatus(null, null)
	
	document.getElementById('nTweet').focus();
	document.getElementById('nTweet').value = "@"+user+" ";

	reply_to = twid;
	$("html, body").animate({ scrollTop: 0 }, "slow");
}

function count(str) {
	document.getElementById("postM").className = document.getElementById("postM").className.replace( /(?:^|\s)success(?!\S)/ , '' );
	document.getElementById('postMHelp').innerHTML = 140 - str.length;
	if ( str.length > 140 ) { document.getElementById("postM").className += " warning"; }
	if ( str.length < 141 ) { document.getElementById("postM").className = document.getElementById("postM").className.replace( /(?:^|\s)warning(?!\S)/ , '' ); }
}