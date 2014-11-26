jQuery(document).ready(function($) {
	$( 'body' ).on( 'click', '#pagebuilder-tertiary-nav .selected a', function(event) {
		//$( this ).addClass('selected');
		//$(this).closest('ul').toggleClass('selected');
		//event.preventDefault();
		$(this).closest('ul').toggleClass('open');
		event.preventDefault();
	});
	$( 'body' ).on( 'click', '#pagebuilder-tertiary-nav a', function(event) {
		event.preventDefault();
		if( jQuery( this ).parent().hasClass('selected') ){
			jQuery(this).closest('ul').toggleClass('open');
		} else {
			jQuery(this).parent().addClass('selected').siblings().removeClass('selected');
			var eq = jQuery(this).parent().index();
			var sel = jQuery('.pagebuilder-tertiary-page').filter('.tertiary-'+eq);
			sel.addClass('selected').siblings().removeClass('selected');
		}
		//$( this ).addClass('selected');
		//$(this).closest('ul').toggleClass('selected');
		//event.preventDefault();
		$(this).closest('ul').toggleClass('open');
		event.preventDefault();
	});
});