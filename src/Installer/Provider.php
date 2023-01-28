<?php

namespace StellarWP\Installer;

class Provider {
	public function register() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Enqueues the installer script.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
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
	}
}
