jQuery(document).ready(function ($) {
	$('body').prepend('<div id="fb-root"></div>');
});
window.fbAsyncInit = function() {
	FB.init({
		appId      : '872972519428691',
		xfbml      : true,
		version    : 'v2.3'
	});
};
(function(d, s, id){
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/" + sfpp_script_vars.language + "/sdk.js";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));