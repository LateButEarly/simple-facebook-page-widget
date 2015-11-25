(function (d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) {
		return;
	}
	js = d.createElement(s);
	js.id = id;
	js.async = true;
	js.src = "//connect.facebook.net/" + sfpp_script_vars.language + "/sdk.js#xfbml=1&version=v2.3";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));