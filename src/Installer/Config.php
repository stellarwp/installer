<?php

namespace StellarWP\Installer;

class Config {
	/**
	 * @var ?string
	 */
	protected static $hook_prefix;

	/**
	 * Gets the hook prefix.
	 *
	 * @return string
	 */
	public static function get_hook_prefix(): string {
		if ( ! static::$hook_prefix ) {
			throw new \RuntimeException( __( 'You must provide a hook prefix via StellarWP\Installer\Config::set_hook_prefix() before creating an Installer.', '%TEXTDOMAIN%' ) );
		}

		return static::$hook_prefix;
	}

	/**
	 * Resets the class back to default.
	 *
	 * @return void
	 */
	public static function reset(): void {
		static::$hook_prefix = null;
	}

	/**
	 * Sets the hook prefix.
	 *
	 * @param string $hook_prefix
	 *
	 * @return void
	 */
	public static function set_hook_prefix( string $hook_prefix ): void {
		$sanitized_prefix = sanitize_key( $hook_prefix );

		if ( $sanitized_prefix !== $hook_prefix ) {
			throw new \InvalidArgumentException( __( 'Hook prefix must only contain lowercase letters, numbers, "_", or "-".', '%TEXTDOMAIN%' ) );
		}

		static::$hook_prefix = $hook_prefix;
	}
}
