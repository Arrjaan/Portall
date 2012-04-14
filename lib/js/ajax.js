var xmlhttp;
var target;

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
	xmlhttp=createAJAX();
	xmlhttp.onreadystatechange=stateChanged;
	xmlhttp.open("GET", url, true);
	xmlhttp.send(null);
	target = trgt;
}

function post(url,data,trgt) {
	xmlhttp=createAJAX();
	xmlhttp.onreadystatechange=stateChanged;
	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
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
				document.getElementById("postMHelp").innerHTML = "Something went wrong...";
			}
		}
		else {
			document.getElementById(target).innerHTML=xmlhttp.responseText;
		}
	}
}

function count(str) {
	document.getElementById('postMHelp').innerHTML = 180 - str.length;
	if ( str.length > 180 ) { document.getElementById("postM").className += " warning"; }
	if ( str.length < 181 ) { document.getElementById("postM").className = document.getElementById("postM").className.replace( /(?:^|\s)warning(?!\S)/ , '' )
 }
}