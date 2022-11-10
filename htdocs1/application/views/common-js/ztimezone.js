
function setCookie(key, value) {
    var expires = new Date();
	expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000));
	secure =  (SslUsed == 1) ? ' secure;' :'';
	samesite = "";
	if(secure){
		samesite = " samesite=none;";
	}
	console.log(key + '=' + value + '; '+secure+samesite+' expires=' + expires.toUTCString()+'; path='+confWebRootUrl);
	document.cookie = key + '=' + value + '; '+secure+samesite+' expires=' + expires.toUTCString()+'; path='+confWebRootUrl;
}
function getCookie(key) {
    var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
    return keyValue ? keyValue[2] : null;
}

$(document).ready(function() {
	var user_timezone  = getCookie('user_timezone');
	var tz = jstz.determine();
	var timezone = tz.name();
	
	if(	(( user_timezone == null || user_timezone =='' || user_timezone == undefined ) || user_timezone != timezone) && cookieConsent.preferences == 1) {
		setCookie('user_timezone', timezone);
	}	
	  
});