<?php
use StellarWP\Installer\Installer;
?>
/**
 * Makes sure we have all the required levels on the StellarWP Object.
 *
 * @since 1.1.0
 *
 * @type {Object}
 */
window.stellarwp = window.stellarwp || {};
window.stellarwp['<?php echo Installer::get()->get_js_object_key(); ?>'] = window.stellarwp['<?php echo Installer::get()->get_js_object_key(); ?>'] || {};
