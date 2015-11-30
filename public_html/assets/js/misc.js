/*Cookie Functions*/
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

function eraseCookie(name) {
	createCookie(name,"",-1);
}
/*Window size functions*/
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
/*RANGE SLIDER*/
$(function(){var e,t,n,r;$("input[type='range']").bind("change",function(){e=$(this);width=e.width();t=(e.val()-e.attr("min"))/(e.attr("max")-e.attr("min"));r=-1.3;if(t<0){n=0}else if(t>1){n=width}else{n=width*t+r;r-=t}e.next("output").css({left:n,marginLeft:r+"%"}).text(e.val())}).trigger("change")})
/*ERROR REPORTING*/
function openErrorReportDialog(event) {
    $("#error_report_div").dialog({modal: true});
}

function show_result(message) {
    $("#error_report_div").dialog("destroy");
    $("#error_result_div").dialog({modal: true, buttons: [{text: "Close", click: function() {
                    $(this).dialog("close");
                }}]});
    $("#error_result_div").html(message);
}

$('#submit_error').click(function(event) {
    event.preventDefault();
    var url = config.base_url + "data_fetch/error_report";
    var posting = $.post(url, {error_text: $('#error_text').val()});
    posting.done(function(data) {
        show_result(data);
    });
    posting.fail(function()
    {
        show_result('Could not submit error report. Please try again later.');
    });
});