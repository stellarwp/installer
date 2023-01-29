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

	public static function get_url( $file ): string {
		$path = dirname( __DIR__ );
		$base_url = str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, $path );
		return $base_url . '/' . $file;
	}

	/**
	 * Enqueues the installer script.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function enqueue_scripts(): void {
		if ( static::has_enqueued() ) {
			return;
		}

		$hook_prefix = Config::get_hook_prefix();
		$object_key  = Installer::get()->get_js_object_key();
		$selectors   = Installer::get()->get_js_selectors();

		/**
		 * Filters the CSS class for indicating a button is busy.
		 *
		 * @since 1.0.0
		 *
		 * @param string $busy_class The CSS class.
		 */
		$busy_class = apply_filters( "stellarwp/installer/{$hook_prefix}/busy_class", 'is-busy' );
		$path       = dirname( __DIR__ );

		ob_start();
		include $path . '/assets/js/installer.php';
		$script = ob_get_clean();

		if ( ! wp_script_is( 'stellarwp_installer', 'registered' ) ) {
			wp_register_script( 'stellarwp_installer', static::get_url( 'assets/js/installer.js' ), [ 'jQuery', 'wp-hooks' ], Installer::VERSION, true );
		}

		wp_add_inline_script( 'stellarwp_installer', $script, 'after' );

		if ( ! wp_script_is( 'stellarwp_installer', 'enqueued' ) ) {
			wp_enqueue_script( 'stellarwp_installer' );
		}

		static::$has_enqueued = true;
	}

	/**
	 * Has the installer script been enqueued?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function has_enqueued(): bool {
		return static::$has_enqueued;
	}
}
