var xmlhttp;
var target;
var reply_to;
var quene;
var postStatus;
var FBID;

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

function loadIMG(name,url) {
	document.getElementById("imgModalTitle").innerHTML = name;
	document.getElementById("imgModalBody").innerHTML = '<img src="' + url + '" />';
}

function status() {
	if ( postStatus['Twitter'] == 'true' ) {
		post('/lib/twitter/index.php?call=statuses/update','status=' + encodeURIComponent(document.getElementById('nTweet').value), 'postM'); 
	}
	if ( postStatus['Facebook'] == 'true' ) {
		post('/lib/facebook/index.php','call=/' + FBID + '/feed&message=' + encodeURIComponent(document.getElementById('nTweet').value), 'postM'); 
	}
}

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
	var current = quene[0];
	xmlhttp=createAJAX();
	xmlhttp.onreadystatechange=stateChanged;
	xmlhttp.open("GET", current[0], true);
	xmlhttp.send(null);
	target = current[1];
	if ( target == "span3" ) { document.getElementById('span3').innerHTML = '<h2>User Info</h2><img src="/lib/layout/img/ajax-loader.gif" alt="Loading..." />'; }
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
	if ( target == "span3" ) { document.getElementById('span3').innerHTML = '<h2>User Info</h2><img src="/lib/layout/img/ajax-loader.gif" alt="Loading..." />'; }
}

function stateChanged() {
	if (xmlhttp.readyState==4 && xmlhttp.responseText != "") {
		if ( target == "postM") {
			if ( xmlhttp.responseText == "200" ) { 
				document.getElementById("postM").className += " success"; 
				document.getElementById("postMHelp").innerHTML = "Your update has been sent!";
				document.getElementById('nTweet').value = ''; 
			}
			else { 
				document.getElementById("postM").className += " error"; 
				document.getElementById("postMHelp").innerHTML = "Something went wrong..."+ xmlhttp.responseText;
			}
		}
		else {
			document.getElementById(target).innerHTML=xmlhttp.responseText;
		}
		quene.shift();
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
}

function count(str) {
	document.getElementById("postM").className = document.getElementById("postM").className.replace( /(?:^|\s)success(?!\S)/ , '' );
	document.getElementById('postMHelp').innerHTML = 140 - str.length;
	if ( str.length > 140 ) { document.getElementById("postM").className += " warning"; }
	if ( str.length < 141 ) { document.getElementById("postM").className = document.getElementById("postM").className.replace( /(?:^|\s)warning(?!\S)/ , '' ); }
}