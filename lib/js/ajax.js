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
		document.getElementById(target).innerHTML=xmlhttp.responseText;
	}
}