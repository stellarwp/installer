<?php

namespace StellarWP\Installer;

class Assets {
	/**
	 * Has the installer script been enqueued?
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public static $has_enqueued = false;

	/**
	 * Enqueues the installer script.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function enqueue_scripts(): void {
		if ( self::has_enqueued() ) {
			return;
		}

		$hook_prefix = Config::get_hook_prefix();
		$js_object   = Installer::get()->get_js_object();
		$selectors   = Installer::get()->get_js_selectors();

		/**
		 * Filters the CSS class for indicating a button is busy.
		 *
		 * @since 1.0.0
		 *
		 * @param string $busy_class The CSS class.
		 */
		$busy_class = apply_filters( "stellarwp/installer/{$hook_prefix}/busy_class", 'is-busy' );

		ob_start();
		include basename( __DIR__ ) . '/assets/js/installer.php';
		$script = ob_get_clean();

		wp_add_inline_script( 'jquery', $script );

		self::$has_enqueued = true;
	}

	/**
	 * Has the installer script been enqueued?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function has_enqueued(): bool {
		return self::$has_enqueued;
	}
}
