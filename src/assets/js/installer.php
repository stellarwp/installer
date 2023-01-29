<?php
/**
 * JS: Dynamically generated JS with custom object for a particular instance of the installer.
 *
 * @var string $js_object The JS object name.
 * @var string $busy_class The CSS class to use when the button is busy.
 * @var array  $selectors The collection of registered resource selectors. The key of each selector is the resource slug and the value is the selector.
 */
// We include the following use line to ensure that Strauss copies this file.
use StellarWP\Installer\Installer;
?>
$( document ).ready( () => {
	window.wp.hooks.addFilter( 'stellarwp_installer_items', items => return items.push( {
		id: '<?php echo sanatize_key( $object_key ); ?>'
		ajaxurl: '<?php echo admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ); ?>',
		selectors: <?php echo wp_json_encode( $selectors ); ?>,
	} ) );
} );

