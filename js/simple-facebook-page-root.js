jQuery(document).ready(function ($) {
	$('body').prepend('<div id="fb-root"></div>');
    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/" + sfpp_script_vars.language + "/sdk.js#xfbml=1&version=v2.3&appId=872972519428691";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    //console.log('App ID: ' + sfpp_script_vars.appid);
});