jQuery(document).ready( function($) {$('body').prepend('<div id="fb-root"></div>');} );
(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	/**
	 * TODO: Pass in User's appId from PHP Settings
	 */
	js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=872972519428691&version=v2.3";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));