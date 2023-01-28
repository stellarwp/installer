<?php
/**
 * JS: Dynamically generated JS with custom object for a particular instance of the installer.
 *
 * @var string $js_object The JS object name.
 * @var string $busy_class The CSS class to use when the button is busy.
 * @var array $selectors The collection of registered resource selectors. The key of each selector is the resource slug and the value is the selector.
 */
// We include the following use line to ensure that Strauss copies this file.
use StellarWP\Installer\Installer;

// @phpstan-ignore-next-line
$js_object = preg_replace( '/[^a-zA-Z0-9_]/', '', $js_object );
?>
/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 1.0.0
 *
 * @type {PlainObject}
 */
const <?php echo $js_object; ?> = {
	ajaxurl: '<?php echo admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ); ?>',
	/**
	* Selectors used for configuration and setup
	*
	* @since 1.0.0
	*
	* @type {PlainObject}
	*/
	selectors: <?php echo wp_json_encode( $selectors ); ?>
};

/**
 * Initializes in a Strict env the code that manages the Events admin notice.
 *
 * @since 1.0.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} _   Underscore.js
 * @param  {PlainObject} obj tribe.events.admin.noticeInstall
 *
 * @return {void}
 */
( function( $, _, obj ) {
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
	obj.getData = function( $button ) {
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
	obj.handleInstall = function( e ) {
		const $button = $( this );
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

		$.post( ajaxUrl, data, function( response ) {
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
				$document.trigger( 'stellarwp_installer_' + $button.data( 'hook-prefix' ) + '_error', {
					slug: $button.data( 'slug' ),
					hookPrefix: $button.data( 'hook-prefix' ),
					action: data.action,
					message: response.data.message,
					selector: e.data.selector
				} );
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
	obj.ready = function() {
		for ( const key in obj.selectors ) {
			$document.on(
				'click',
				obj.selectors[ key ],
				{
					slug: key,
					selector: obj.selectors[key]
				},
				obj.handleInstall
			);
		}
	};

	// Configure on document ready.
	$document.ready( obj.ready );
} )( jQuery, window.underscore || window._, <?php echo $js_object; ?> );
