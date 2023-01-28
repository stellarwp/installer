<?php

namespace StellarWP\Installer\Handler;

use StellarWP\Installer\Config;
use WP_Error;

class Plugin extends Handler_Abstract {
	/**
	 * @inheritDoc
	 */
	public $type = 'plugin';

	/**
	 * @inheritDoc
	 */
	public $permission = 'install_plugins';

	/**
	 * @inheritDoc
	 */
	public function activate(): bool {
		if ( ! $this->is_installed() ) {
			return $this->install();
		}

		if ( $this->is_active() ) {
			return true;
		}

		$activate = activate_plugin( $this->basename, '', false, true );

		return ! is_wp_error( $activate );
	}

	/**
	 * @inheritDoc
	 */
	public function get_error_message(): ?string {
		$hook_prefix = Config::get_hook_prefix();

		$install_url = wp_nonce_url(
			self_admin_url(
			'update.php?action=install-plugin&plugin=' . $this->slug
			),
			'install-plugin_' . $this->slug
		);

		$message     = sprintf(
			/* Translators: %1$s - opening link tag, %2$s - closing link tag. */
			__( 'There was an error and plugin could not be installed, %1$splease install manually%2$s.', '%TEXTDOMAIN%' ),
			'<a href="' . esc_url( $install_url ) . '">',
			'</a>'
		);

		/**
		 * Filters the error message for a plugin install.
		 *
		 * @since 1.0.0
		 *
		 * @param string $message The error message.
		 * @param string $slug The plugin slug.
		 * @param Plugin $plugin The plugin handler.
		 */
		return apply_filters( "stellarwp/installer/{$hook_prefix}/install_error_message", $message, $this->slug, $this );
	}

	/**
	 * @inheritDoc
	 */
	public function get_wordpress_org_data() {
		if ( $this->wordpress_org_data ) {
			return $this->wordpress_org_data;
		}

		if ( ! function_exists( 'plugins_api' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		}

		$hook_prefix = Config::get_hook_prefix();

		$api_results = plugins_api(
		'plugin_information',
			[
				'slug'   => $this->slug,
				'fields' => [
					'short_description' => false,
					'sections'          => false,
					'requires'          => false,
					'rating'            => false,
					'ratings'           => false,
					'downloaded'        => false,
					'last_updated'      => false,
					'added'             => false,
					'tags'              => false,
					'compatibility'     => false,
					'homepage'          => false,
					'donate_link'       => false,
				],
			]
		);

		/**
		 * Filters the WordPress.org data for a plugin.
		 *
		 * @since 1.0.0
		 *
		 * @param object|WP_Error $api_results The WordPress.org data.
		 * @param string $slug The plugin slug.
		 * @param Plugin $plugin The plugin handler.
		 */
		$api_results = apply_filters( "stellarwp/installer/{$hook_prefix}/wordpress_org_data", $api_results, $this->slug, $this );

		$this->wordpress_org_data = $api_results;

		return $this->wordpress_org_data;
	}

	/**
	 * @inheritDoc
	 */
	public function install(): bool {
		if ( ! class_exists( 'WP_Upgrader' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		$url = $this->get_download_url();

		if ( ! is_wp_error( $this->wordpress_org_data ) ) {
			$upgrader  = new \Plugin_Upgrader( new \WP_Ajax_Upgrader_Skin() );
			$installed = $upgrader->install( $url );

			if ( $installed ) {
				$activate = activate_plugin( $this->basename, '', false, true );
				$success  = ! is_wp_error( $activate );
			} else {
				$success = false;
			}
		} else {
			$success = false;
		}

		return $success;
	}

	/**
	 * @inheritDoc
	 */
	public function is_active(): bool {
		$did_action = false;

		if ( isset( $this->did_action ) ) {
			$did_action = did_action( $this->did_action );
		}

		return is_plugin_active( $this->basename ) || is_plugin_active_for_network( $this->basename ) || $did_action;
	}

	/**
	 * Checks if `Event Tickets` is installed.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean True if active
	 */
	public function is_installed(): bool {
		$installed_plugins = get_plugins();

		return array_key_exists( $this->basename, $installed_plugins ) || in_array( $this->basename, $installed_plugins, true );
	}
}
