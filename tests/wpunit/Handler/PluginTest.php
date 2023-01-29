<?php
namespace StellarWP\Installer\Handler;

use StellarWP\Installer\Config;
use StellarWP\Installer\Installer;
use StellarWP\Installer\Tests\InstallerTestCase;

class PluginTest extends InstallerTestCase {
	public function setUp(): void {
		// before
		parent::setUp();
	}

	public function tearDown(): void {
		parent::tearDown();
		Config::reset();
		Installer::reset();
		$this->remove_fake_plugins();
	}

	public function remove_fake_plugins() {
		@unlink( WP_PLUGIN_DIR . '/another-fake-plugin/another-fake-plugin.php' );
		@rmdir( WP_PLUGIN_DIR . '/another-fake-plugin' );
		@unlink( WP_PLUGIN_DIR . '/fake-plugin/fake-plugin.php' );
		@rmdir( WP_PLUGIN_DIR . '/fake-plugin' );
	}

	/**
	 * @test
	 */
	public function should_get_simple_properties() {
		$installer   = Installer::get();
		$installer->register_plugin( 'event-tickets', 'Event Tickets' );
		$et = $installer->get_registered_plugin( 'event-tickets' );

		$this->assertEquals( 'event-tickets', $et->get_slug() );
		$this->assertEquals( 'Event Tickets', $et->get_name() );
		$this->assertEquals( "stellarwp_installer_test_install_plugin_event-tickets", $et->get_js_action() );
	}

	/**
	 * @test
	 */
	public function should_install_plugin() {
		$this->remove_fake_plugins();

		$installer   = Installer::get();
		$installer->register_plugin( 'fake-plugin', 'Fake Plugin', dirname( dirname( __DIR__ ) ) . '/_data/fake-plugin.zip' );
		$plugin = $installer->get_registered_plugin( 'fake-plugin' );

		$result = $plugin->install();
		$this->assertTrue( $result );
		$this->assertTrue( $plugin->is_installed() );
		$this->assertTrue( $plugin->is_active() );
	}

	/**
	 * @test
	 */
	public function should_activate_plugin() {
		$this->remove_fake_plugins();

		$installer   = Installer::get();
		$installer->register_plugin( 'fake-plugin', 'Fake Plugin', dirname( dirname( __DIR__ ) ) . '/_data/fake-plugin.zip' );
		$plugin = $installer->get_registered_plugin( 'fake-plugin' );

		$plugin->install();
		deactivate_plugins( [ 'fake-plugin/fake-plugin.php' ] );

		$this->assertTrue( $plugin->is_installed() );
		$this->assertFalse( $plugin->is_active() );

		$result = $plugin->activate();

		$this->assertTrue( $result );
		$this->assertTrue( $plugin->is_installed() );
		$this->assertTrue( $plugin->is_active() );
	}
}
