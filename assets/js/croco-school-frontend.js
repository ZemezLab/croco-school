( function( $ ) {

	'use strict';

	var CrocoSchool = {

		init: function() {

			if ( $( '.croco-school__single-article-sidebar' )[0] ) {
				var stickySidebar = new StickySidebar( '.croco-school__single-article-sidebar', { topSpacing: 20 } );
			}
		}

	};

	$( document ).on( 'ready', CrocoSchool.init );


}( jQuery ) );
