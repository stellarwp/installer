/**
 * Initializes in a Strict env the code that manages the Stellar Installer buttons.
 *
 * @since 1.0.0
 *
 * @param  {Object} $     jQuery
 * @param  {Object} hooks WP Hooks
 * @param  {Object} obj   window.stellarwp.installer
 *
 * @return {void}
 */

console.log( document.currentScript.attributes );
( function( $, hooks, obj, namespace ) {
	'use strict';
	if ( typeof window.stellarwp[ namespace ].installer === 'object' ) {
		obj = window.stellarwp[ namespace ].installer;
	}

	const $document = $( document );

	/**
	 * Gets the AJAX request data.
	 *
	 * @since 1.0.0
	 *
	 * @param  {Element|jQuery} $button The button where the configuration data is.
	 *
	 * @return {Object} data
	 */
	obj.getData = ( $button ) => {
		const data = {
			'action': $button.data( 'action' ),
			'request': $button.data( 'request-action' ),
			'slug': $button.data( 'slug' ),
			'_wpnonce': $button.data( 'nonce' ),
		};

		return data;
	};

	/**
	 * Handles the plugin install AJAX call.
	 *
	 * @since 1.0.0
	 */
	obj.handleInstall = ( event ) => {
		const $button = $( event.target );
		const ajaxUrl = obj.ajaxurl;
		const data = obj.getData( $button );
		const requestType = $button.data( 'request-action' );

		$button.addClass( 'is-busy' );
		$button.prop( 'disabled', true );

		if ( 'install' === requestType ) {
			$button.text( $button.data( 'installing-label' ) );
		} else if ( 'activate' === requestType  ) {
			$button.text( $button.data( 'activating-label' ) );
		}

		$.post( ajaxUrl, data, ( response ) => {
			$button.removeClass( 'is-busy' );
			$button.prop( 'disabled', false );

			if ( 'undefined' === typeof response.data || 'object' !== typeof response.data ) {
				return;
			}

			if ( response.success ) {
				if ( 'install' === requestType ) {
					$button.text( $button.data( 'installed-label' ) );
				} else if ( 'activate' === requestType ) {
					$button.text( $button.data( 'activated-label' ) );
				}

				if ( $button.data('redirect-url') ) {
					location.replace( $button.data('redirect-url') );
				}
			} else {
				hooks.doAction(
					'stellarwp_installer_' + $button.data( 'hook-prefix' ) + '_error',
					event.data.selector,
					$button.data( 'slug' ),
					data.action,
					response.data.message,
					$button.data( 'hook-prefix' )
				);
			}
		} );
	}

	/**
	 * Handles the initialization of the notice actions.
	 *
	 * @since 1.0.0
	 *
	 * @return {void}
	 */
	obj.ready = ( event ) => {
		for ( const key in obj.selectors ) {
			$document.on(
				'click',
				obj.selectors[ key ],
				{
					slug: key,
					selector: obj.selectors[ key ]
				},
				obj.handleInstall
			);
		}
	};

	// Configure on document ready.
	$document.ready( obj.ready );

	window.stellarwp[ namespace ].installer = obj;
} )( window.jQuery, window.wp.hooks, JSON.parse( document.currentScript.getAttribute( 'data-stellarwp-data' ) ), document.currentScript.getAttribute( 'data-stellarwp-namespace' ) );
