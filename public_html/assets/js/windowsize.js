function is_maximized() {
    return (document.getElementById('header').style.width === '100%');
    //return (document.body.style.width === '100%');
}
function toggle_maximize() {
    if (is_maximized()) {
        document.getElementById('header').style.width = '88%';
        document.getElementById('page-content').style.width = '88%';
        //document.body.style.width = '85%';
        createCookie("riskmp_maximized","false",365);
    } else {
        maximize();
        createCookie("riskmp_maximized","true",365);
    }
}
function load_maximize() {
    if (readCookie("riskmp_maximized") === "true")
        maximize();
}
function maximize() {
    document.getElementById('header').style.width = '100%';
    document.getElementById('page-content').style.width = '100%';
    //document.body.style.width = '100%';
}
function toggle_fullscreen() {
    if (isFullscreen()) {
        exitFullscreen();
    } else {
        if (!is_maximized())
            maximize();
        launchFullScreen(document.documentElement);
    }
}
function launchFullScreen(element) {
    if (element.requestFullscreen)
        element.requestFullscreen();
    else if (element.mozRequestFullScreen)
        element.mozRequestFullScreen();
    else if (element.webkitRequestFullscreen)
        element.webkitRequestFullscreen();
    else if (element.msRequestFullscreen)
        element.msRequestFullscreen();
}
function exitFullscreen() {
    if (document.exitFullscreen)
        document.exitFullscreen();
    else if (document.mozExitFullScreen)
        document.mozExitFullScreen();
    else if (document.webkitExitFullscreen)
        document.webkitExitFullscreen();
}
function isFullscreen() {
    return document.fullscreenEnabled || document.mozFullscreenEnabled || document.webkitIsFullScreen ? true : false;
}

function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}