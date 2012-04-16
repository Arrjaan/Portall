var xmlhttp;
var target;
var reply_to;
var quene;

reply_to = "";
quene = [];

function createAJAX() {
	if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
	}
	if (window.ActiveXObject) {
		return new ActiveXObject("Microsoft.XMLHTTP");
	}
	return null;
}

function ajax(url,trgt) {
	if ( url !== null ) { quene[quene.length] = [url,trgt]; }
	var current = quene[0];
	xmlhttp=createAJAX();
	xmlhttp.onreadystatechange=stateChanged;
	xmlhttp.open("GET", current[0], true);
	xmlhttp.send(null);
	target = current[1];
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
}

function stateChanged() {
	if (xmlhttp.readyState==4 && xmlhttp.responseText != "") {
		if ( target == "postM") {
			if ( xmlhttp.responseText == "200" ) { 
				document.getElementById("postM").className += " success"; 
				document.getElementById("postMHelp").innerHTML = "Your update has been sent!";
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