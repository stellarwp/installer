<?php

namespace StellarWP\Installer\Tests;

use StellarWP\Installer\Config;
use StellarWP\Installer\Installer;

class InstallerTestCase extends \Codeception\Test\Unit {
	protected $backupGlobals = false;

	public function setUp(): void {
		// before
		parent::setUp();

		Config::set_hook_prefix( 'test' );
		Installer::init();
	}
}

