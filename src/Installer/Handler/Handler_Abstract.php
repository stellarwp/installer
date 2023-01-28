<?php

namespace StellarWP\Installer\Handler;

use stdClass;
use StellarWP\Installer\Button;
use StellarWP\Installer\Config;
use StellarWP\Installer\Contracts\Handler;
use StellarWP\Installer\Installer;
use StellarWP\Installer\Utils\Array_Utils;
use WP_Error;

abstract class Handler_Abstract implements Handler {
	/**
	 * Resource basename.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $basename;

	/**
	 * Button.
	 *
	 * @since 1.0.0
	 *
	 * @var Button|null
	 */
	protected $button;

	/**
	 * Action indicating that a resource has been activated.
	 *
	 * @since 1.0.0
	 *
	 * @var string|null
	 */
	protected $did_action;

	/**
	 * Download URL.
	 *
	 * @since 1.0.0
	 *
	 * @var string|null
	 */
	protected $download_url;

	/**
	 * The JS action to be used in the button.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $js_action;

	/**
	 * Resource name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Permission for installation (current_user_can).
	 *
	 * @var string|null
	 */
	public $permission;

	/**
	 * Resource slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * Type of resource.
	 *
	 * @var string|null
	 */
	public $type;

	/**
	 * WordPress.org data.
	 *
	 * @since 1.0.0
	 *
	 * @var stdClass|WP_Error|null
	 */
	protected $wordpress_org_data;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct( string $name, string $slug, string $basename, ?string $download_url = null, ?string $did_action = null, string $js_action ) {
		$this->name         = $name;
		$this->slug         = $slug;
		$this->basename     = $basename;
		$this->download_url = $download_url;
		$this->did_action   = $did_action;
		$this->js_action    = $js_action;
	}

	/**
	 * Gets the resource's button.
	 *
	 * @since 1.0.0
	 *
	 * @return Button
	 */
	public function get_button() : Button {
		if ( empty( $this->button ) ) {
			$this->button = new Button( $this );
		}
		return $this->button;
	}

	/**
	 * Gets the download_url.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	protected function get_download_url() : ?string {
		if ( ! $this->download_url ) {
			$api = $this->get_wordpress_org_data();

			if ( ! is_wp_error( $api ) ) {
				$this->download_url = $api->download_link;
			}
		}

		$hook_prefix = Config::get_hook_prefix();

		/**
		 * Filters the download URL for the resource.
		 *
		 * @since 1.0.0
		 *
		 * @param string|null      $download_url The download URL.
		 * @param string           $slug         The resource slug.
		 * @param Handler_Abstract $handler      The handler instance.
		 */
		return apply_filters( "stellarwp/installer/{$hook_prefix}/download_url", $this->download_url, $this->slug, $this );
	}

	/**
	 * Gets the error message if the resource cannot be installed.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	abstract protected function get_error_message(): ?string;

	/**
	 * @inheritDoc
	 */
	public function get_js_action(): string {
		return $this->js_action;
	}

	/**
	 * @inheritDoc
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Gets the resource permission.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_permission(): string {
		$hook_prefix = Config::get_hook_prefix();

		/**
		 * Filters the permission for installing the resource.
		 *
		 * @since 1.0.0
		 *
		 * @param string|null $permission The permission.
		 * @param string      $slug       The resource slug.
		 * @param Handler     $handler    The installer object.
		 */
		return apply_filters( "stellarwp/installer/{$hook_prefix}/get_permission", $this->permission, $this->slug, $this );
	}

	/**
	 * Tests to see if the requested variable is set either as a post field or as a URL
	 * param and returns the value if so.
	 *
	 * Post data takes priority over fields passed in the URL query. If the field is not
	 * set then $default (null unless a different value is specified) will be returned.
	 *
	 * The variable being tested for can be an array if you wish to find a nested value.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $var
	 * @param mixed        $default
	 *
	 * @return mixed
	 */
	protected function get_request_var( $var, $default = null ) {
		$requests = [];

		// Prevent a slew of warnings every time we call this.
		$requests[] = $_REQUEST;
		$requests[] = $_GET;
		$requests[] = $_POST;

		$unsafe = Array_Utils::get_in_any( $requests, $var, $default );
		return Array_Utils::sanitize_deep( $unsafe );
	}

	/**
	 * @inheritDoc
	 */
	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * Fetch the WordPress.org data for the resource.
	 *
	 * @since 1.0.0
	 *
	 * @return stdClass|WP_Error|null
	 */
	abstract protected function get_wordpress_org_data();

	/**
	 * @inheritDoc
	 */
	public function handle_request() {
		$installer = Installer::get();

		if ( ! check_ajax_referer( $installer->get_nonce_name(), 'nonce', false ) ) {
			$response['message'] = wpautop( __( 'Insecure request.', '%TEXTDOMAIN%' ) );

			wp_send_json_error( $response );
		}

		if ( ! current_user_can( $this->get_permission() ) ) {
			wp_send_json_error( [ 'message' => wpautop( sprintf( __( 'Security Error, Need higher permissions to install %1$s.' , '%TEXTDOMAIN%' ), $this->name ) ) ] );
		}

		$vars = [
			'request' => $this->get_request_var( 'request' ),
		];

		$success = false;

		if ( 'install' === $vars['request'] ) {
			$success = $this->install();
		} elseif ( 'activate' === $vars['request'] ) {
			$success = $this->activate();
		}

		if ( false === $success ) {
			wp_send_json_error( [ 'message' => wpautop( $this->get_error_message() ) ] );
		} else {
			wp_send_json_success( [ 'message' => __( 'Success.', '%TEXTDOMAIN%' ) ] );
		}
	}
}
