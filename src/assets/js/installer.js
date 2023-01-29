/**
 * Makes sure we have all the required levels on the StellarWP Object.
 *
 * @since 1.1.0
 *
 * @type {Object}
 */
window.stellarwp = window.stellarwp || {};

/**
 * Makes sure we have all the required levels on the StellarWP Object.
 *
 * @since 1.1.0
 *
 * @type {Object}
 */
window.stellarwp.installer = window.stellarwp.installer || { items: [] };

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
( function( $, hooks, obj ) {
	'use strict';
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
		const ajaxUrl = event.data.item.ajaxurl;
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
		obj.items = hooks.applyFilters( 'stellarwp_installer_items', obj.items, event );
		obj.items.map( item => {
			for ( const key in item.selectors ) {
				$document.on(
					'click',
					item.selectors[ key ],
					{
						slug: key,
						selector: item.selectors[ key ],
						item: item,
					},
					obj.handleInstall
				);
			}
		} );
	};

	// Configure on document ready.
	$document.ready( obj.ready );
} )( window.jQuery, window.wp.hooks, window.stellarwp.installer );
