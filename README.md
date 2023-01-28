# installer
A library for installing / activating other plugins

## Usage

Initialize plugins and themes that you want to install and activate.

```php
namespace StellarWP\Installer\Config;
namespace StellarWP\Installer\Installer;

Config::set_hook_prefix( 'whatever_' );
$installer = Installer::get();

$plugin_installer = new Installer()

// Leave $download_link empty to use the WordPress.org repository.
$installer->register_plugin( $plugin_slug, $plugin_name, $plugin_basename, $download_link, $did_action );

// Leave $download_link empty to use the WordPress.org repository.
$installer->register_theme( $theme_slug, $theme_name, $theme_basename, $download_link, $did_action );
```

Get the install/activate button.

```php
namespace StellarWP\Installer\Installer;

$installer = Installer::get();

$installer->get_plugin_button( $plugin_slug );
$installer->get_theme_button( $theme_slug );
```
