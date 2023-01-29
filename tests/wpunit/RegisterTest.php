<?php
namespace StellarWP\Installer;

use StellarWP\Installer\Tests\InstallerTestCase;

class RegisterTest extends InstallerTestCase {
	public function setUp(): void {
		// before
		parent::setUp();
	}

	public function tearDown(): void {
		parent::tearDown();
		Config::reset();
		Installer::reset();
	}

	/**
	 * @test
	 */
	public function should_register_plugin() {
		$installer = Installer::get();
		$installer->register_plugin( 'event-tickets', 'Event Tickets' );
		$handler   = $installer->get_registered_plugin( 'event-tickets' );

		$this->assertTrue( $installer->is_registered( 'event-tickets' ) );
		$this->assertNotFalse( has_action( 'wp_ajax_' . $handler->get_js_action(), [ $handler, 'handle_request' ] ) );
		$this->assertCount( 1, $installer->get_registered_plugins() );

		$installer->register_plugin( 'the-events-calendar', 'The Events Calendar' );
		$handler   = $installer->get_registered_plugin( 'the-events-calendar' );
		$this->assertTrue( $installer->is_registered( 'the-events-calendar' ) );
		$this->assertNotFalse( has_action( 'wp_ajax_' . $handler->get_js_action(), [ $handler, 'handle_request' ] ) );
		$this->assertCount( 2, $installer->get_registered_plugins() );
	}

	/**
	 * @test
	 */
	public function should_deregister_plugin() {
		$installer = Installer::get();
		$installer->register_plugin( 'event-tickets', 'Event Tickets' );
		$handler   = $installer->get_registered_plugin( 'event-tickets' );

		$installer->deregister_plugin( 'event-tickets' );

		$this->assertFalse( $installer->is_registered( 'event-tickets' ) );
		$this->assertFalse( has_action( 'wp_ajax_' . $handler->get_js_action(), [ $handler, 'handle_request' ] ) );
		$this->assertCount( 0, $installer->get_registered_plugins() );
	}
}
