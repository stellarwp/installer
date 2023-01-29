
namespace StellarWP\Installer;

use StellarWP\Installer\Tests\InstallerTestCase;

class ConfigTest extends InstallerTestCase {
	public function setUp() : void {
		// before
		parent::setUp();
	}

	public function tearDown() : void {
		parent::tearDown();
		Config::reset();
	}

	/**
	 * @test
	 */
	public function should_set_hook_prefix() {
		$this->assertEquals( 'test', Config::get_hook_prefix() );
	}
}
