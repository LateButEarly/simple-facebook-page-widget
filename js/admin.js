jQuery(document).ready(function ($) {
	$(".chosen-select").chosen({no_results_text: "Oops, nothing found!"});
	// Tabs
	$('#sfpptabs').css({padding: '5px', border: '1px solid #ccc', borderTop: '0px'});
	$('.nav-tab-wrapper a').css({outline: '0px'});
	$('#sfpptabs .sfpp-tab').hide();
	// $('#sfpptabs h3').hide();
	var sup_html5st = 'sessionStorage' in window && window['sessionStorage'] !== undefined;
	if (sup_html5st) {
		var tab = unescape(sessionStorage.getItem('rocket_tab'));
		if (tab != 'null' && tab != null && tab != undefined && $('h2.nav-tab-wrapper a[href="' + tab + '"]').length == 1) {
			$('#sfpptabs .nav-tab').hide();
			$('h2.nav-tab-wrapper a[href="' + tab + '"]').addClass('nav-tab-active');
			$(tab).show();
		} else {
			$('h2.nav-tab-wrapper a:first').addClass('nav-tab-active');
			if ($('#tab_basic').length == 1)
				$('#tab_basic').show();
		}
	}
	$('h2.nav-tab-wrapper .nav-tab').on('click', function (e) {
		e.preventDefault();
		tab = $(this).attr('href');
		$('#sfpptabs .sfpp-tab').hide();
		$('h2.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
		$('h2.nav-tab-wrapper a[href="' + tab + '"]').addClass('nav-tab-active');
		$(tab).show();
	});
	if ($('#sfpptabs .sfpp-tab:visible').length == 0) {
		$('h2.nav-tab-wrapper a:first').addClass('nav-tab-active');
		$('#tab_extras').show();
		$('#tab_basic').show();
	}
});
