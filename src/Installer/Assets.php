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
	protected static $has_enqueued = false;

	/**
	 * Has a JS named StellarWP object key created.
	 *
	 * @since 1.1.0
	 *
	 * @var bool
	 */
	public static $has_namespaced_object = false;

	public static function get_url( $file ): string {
		$path     = dirname( __DIR__ );
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

		static::register_script( 'stellarwp-installer', 'assets/js/installer.js', [ 'jQuery', 'wp-hooks' ], true );


//		$hook_prefix = Config::get_hook_prefix();
//		$object_key  = Installer::get()->get_js_object_key();
//		$selectors   = Installer::get()->get_js_selectors();
//
//		/**
//		 * Filters the CSS class for indicating a button is busy.
//		 *
//		 * @since 1.0.0
//		 *
//		 * @param string $busy_class The CSS class.
//		 */
//		$busy_class = apply_filters( "stellarwp/installer/{$hook_prefix}/busy_class", 'is-busy' );
//		$path       = dirname( __DIR__ );
//		ob_start();
//		include $path . '/assets/js/installer.php';
//		$script = ob_get_clean();
//		wp_add_inline_script( 'stellarwp_installer', $script, 'after' );
//
//		if ( ! wp_script_is( 'stellarwp_installer', 'enqueued' ) ) {
//			wp_enqueue_script( 'stellarwp_installer' );
//		}

		static::$has_enqueued = true;
	}

	public static function get_script_handle( string $slug ): string {
		return implode( '-', [ $slug, Config::get_hook_prefix() ] );
	}

	/**
	 * @param string           $handle    Name of the script. Should be unique.
	 * @param string|false     $src       Full URL of the script, or path of the script relative to the WordPress root directory.
	 *                                    If source is set to false, script is an alias of other scripts it depends on.
	 * @param string[]         $deps      Optional. An array of registered script handles this script depends on. Default empty array.
	 * @param bool             $in_footer Optional. Whether to enqueue the script before `</body>` instead of in the `<head>`.
	 *                                    Default 'false'.
	 * @return bool Whether the script has been registered. True on success, false on failure.
	 */
	public static function register_script( $handle, $src, $deps, $in_footer ): bool {
		$script_handle = static::get_script_handle( $handle );
		$registered = wp_register_script( $script_handle, static::get_url( $src ), $deps, Installer::VERSION, $in_footer );

		// On fail bail early.
		if ( ! $registered ) {
			return $registered;
		}

		// Ensure we have a stellar object ready.
		static::print_stellar_namespaced_object();
		add_filter( 'script_loader_tag', static function( $tag, $handle ) use ( $script_handle ) {
			if ( $handle !== $script_handle ) {
				return $tag;
			}

			$namespace_key = Installer::get()->get_js_object_key();

			$replacement = "<script stellarwpNamespace='{$namespace_key}'";
			return str_replace( '<script ', $replacement, $tag );

		}, 50, 2 );

		return $registered;
	}

	public static function print_stellar_namespaced_object(): void {
		if ( static::has_namespaced_object() ) {
			return;
		}

		if ( ! did_action( 'admin_enqueue_scripts' ) && ! doing_action( 'admin_enqueue_scripts' ) ) {
			add_action( 'admin_enqueue_scripts', [ static::class, 'print_stellar_namespaced_object' ] );
			return;
		}

		wp_print_inline_script_tag( static::get_stellar_namespace_js() );

		// Prevents multiple.
		static::$has_namespaced_object = true;
	}

	public static function get_stellar_namespace_js(): string {
		$path = dirname( __DIR__ );

		ob_start();
		include $path . '/admin-views/stellarwp-namespace-script.php';
		$script = ob_get_clean();

		return $script;
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

	/**
	 * Has the installer script been enqueued?
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public static function has_namespaced_object(): bool {
		return static::$has_namespaced_object;
	}
}
