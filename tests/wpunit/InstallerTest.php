<?php
namespace StellarWP\Installer;

use StellarWP\Installer\Tests\InstallerTestCase;

class InstallerTest extends InstallerTestCase {
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
	public function should_add_prefix_to_js_object() {
		$installer = Installer::get();

		$this->assertEquals( 'stellarwpInstallertest', $installer->get_js_object() );
	}

	/**
	 * @test
	 */
	public function should_have_selectors_for_all_registered_plugins() {
		$installer = Installer::get();
		$installer->register_plugin( 'event-tickets', 'Event Tickets' );
		$installer->register_plugin( 'the-events-calendar', 'The Events Calendar' );

		$et  = $installer->get_registered_plugin( 'event-tickets' );
		$tec = $installer->get_registered_plugin( 'the-events-calendar' );

		$selectors = $installer->get_js_selectors();

		$this->assertArrayHasKey( 'event-tickets', $selectors );
		$this->assertArrayHasKey( 'the-events-calendar', $selectors );
		$this->assertEquals( '.' . $et->get_button()->get_selector(), $selectors['event-tickets'] );
		$this->assertEquals( '.' . $tec->get_button()->get_selector(), $selectors['the-events-calendar'] );
	}
}
