jQuery( document ).ready( function($){

	// Chosen - Kudos harvesthq - https://github.com/harvesthq/chosen
	$(".chosen-select").chosen({no_results_text: "Oops, nothing found!"});
	
	// Tabs - Kudos WP Rocket - http://wp-rocket.me/
	$('#sfpptabs').css({padding: '5px', border: '1px solid #ccc', borderTop: '0px'});
	$('.nav-tab-wrapper a').css({outline: '0px'});
	$('#sfpptabs').find('.sfpp-tab').hide();
	$('#sfpptabs').find('h3').hide();
	var sup_html5st = 'sessionStorage' in window && window['sessionStorage'] !== undefined;
	if( sup_html5st ) {
		var tab = unescape( sessionStorage.getItem( 'rocket_tab' ) );
		if( tab!='null' && tab!=null && tab!=undefined && $('h2.nav-tab-wrapper a[href="'+tab+'"]').length==1 ) {
			$('#sfpptabs').find('.nav-tab').hide();
			$('h2.nav-tab-wrapper a[href="'+tab+'"]').addClass('nav-tab-active');
			$(tab).show();
		}else{
			$('h2.nav-tab-wrapper a:first').addClass('nav-tab-active');
			if( $('#tab_basic').length==1 )
				$('#tab_basic').show();
		}
	}
	$('h2.nav-tab-wrapper .nav-tab').on( 'click', function(e){
		e.preventDefault();
		tab = $(this).attr( 'href' );
		if( sup_html5st ) sessionStorage.setItem( 'rocket_tab', tab );
		$('#sfpptabs').find('.sfpp-tab').hide();
		$('h2.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
		$('h2.nav-tab-wrapper a[href="'+tab+'"]').addClass('nav-tab-active');
		$(tab).show();
	} );
	if( $('#sfpptabs').find('.sfpp-tab:visible').length == 0 ){
		$('h2.nav-tab-wrapper a:first').addClass('nav-tab-active');
		$('#tab_basic').show();
		if( sup_html5st ) sessionStorage.setItem( 'rocket_tab', null );
	}
	
});