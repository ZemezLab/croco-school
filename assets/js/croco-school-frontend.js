( function( $ ) {

	'use strict';

	var CrocoSchool = {

		init: function() {

			CrocoSchool.stickySidebarInit();
			CrocoSchool.embedVideoInit();
		},

		stickySidebarInit: function() {

			if ( $( '.croco-school__single-article-sidebar' )[0] ) {
				var stickySidebar = new StickySidebar( '.croco-school__single-article-sidebar', { topSpacing: 20 } );
			}
		},

		embedVideoInit: function() {

			var $mediaFrame = $( '.croco-school__single-media-frame' );

			if ( ! $mediaFrame[0] ) {
				return false;
			}

			var $videoIframe = $( '.croco-school-video-iframe', $mediaFrame ),
				$overlay     = $( '.video-embed-image-overlay', $mediaFrame );

			$overlay.on( 'click.CrocoSchool', function( event ) {
				var newSourceUrl = $videoIframe[0].src.replace('&autoplay=0', '');

				$videoIframe[0].src = newSourceUrl + '&autoplay=1';

				$overlay.remove();

			} );

		}

	};

	$( document ).on( 'ready.CrocoSchool', CrocoSchool.init );


}( jQuery ) );
