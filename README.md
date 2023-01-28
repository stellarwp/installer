# StellarWP Installer

A library for installing / activating other plugins.

## Docs

* [Installation](#installation)
* [Initialization](#initialization)
* [Registering a plugin](#registering-a-plugin)
  * [Simple registration](#simple-registration)
  * [Registration with download link](#registration-with-download-link)
  * [Registration with an action indicating that the plugin is active](#registration-with-an-action-indicating-that-the-plugin-is-active)
* [Rendering an install/activate button](#rendering-an-install-activate-button)
  * [Render a button](#render-a-button)
  * [Get a button](#get-a-button)
  * [Get or render a button and redirect](#get-or-render-a-button-and-redirect)
* [Hooks](#hooks)

### Installation

It's recommended that you install Schema as a project dependency via [Composer](https://getcomposer.org/):

```bash
composer require stellarwp/schema
```

> We _actually_ recommend that this library gets included in your project using [Strauss](https://github.com/BrianHenryIE/strauss).
>
> Luckily, adding Strauss to your `composer.json` is only slightly more complicated than adding a typical dependency, so checkout our [strauss docs](https://github.com/stellarwp/global-docs/blob/main/docs/strauss-setup.md).

### Initialization

During the `plugins_loaded` action, initialize the installer.

```php
namespace StellarWP\Installer\Config;
namespace StellarWP\Installer\Installer;

Config::set_hook_prefix( 'boomshakalaka' );
Installer::init();
```

### Registering a plugin

`$installer->register_plugin( $slug, $plugin_name, $plugin_basename, $download_link, $did_action );`

| Parameter | Type | Description                                                                                                              |
| --- | --- |--------------------------------------------------------------------------------------------------------------------------|
| `$slug` | `string` | **Required.** A simple slug for referring to your plugin.                                                                |
| `$plugin_name` | `string` | **Required.** The plugin name.                                                                                           |
| `$plugin_basename` | `string` | **Required.** The plugin basename. (`event-tickets/event-tickets.php`)                                                   |
| `$download_link` | `string` | The plugin download link. If this is omitted, it is assumed that the URL will come from WordPress.org plugin repository. |
| `$did_action` | `string` | If provided, the action will be checked with `did_action()` to indicate that the plugin is active.                       |

#### Simple registration

```php
use StellarWP\Installer\Installer;

$installer = Installer::get();
$installer->register_plugin( 'event-tickets', 'Event Tickets', 'event-tickets/event-tickets.php' );
```

#### Registration with download link

```php
use StellarWP\Installer\Installer;

$installer = Installer::get();
$installer->register_plugin( 'event-tickets', 'Event Tickets', 'event-tickets/event-tickets.php', 'https://example.com/event-tickets.zip' );
```

#### Registration with an action indicating that the plugin is active

```php
use StellarWP\Installer\Installer;

$installer = Installer::get();
$installer->register_plugin( 'event-tickets', 'Event Tickets', 'event-tickets/event-tickets.php', null, 'event_tickets_plugin_loaded' );
```

### Rendering an install/activate button

Buttons are the main point of this library. You can get or render a button. When you do, the relevant JavaScript will be enqueued to hook the button up with admin-ajax.php.

#### Render a button

```php
use StellarWP\Installer\Installer;

Installer::get()->render_plugin_button( 'event-tickets', 'install', 'Install Event Tickets' );
```

#### Get a button

```php
use StellarWP\Installer\Installer;

Installer::get()->get_plugin_button( 'event-tickets', 'install', 'Install Event Tickets' );
```

#### Get or render a button and redirect

```php
use StellarWP\Installer\Installer;

// Get it.
$button = Installer::get()->get_plugin_button( 'event-tickets', 'install', 'Install Event Tickets', $redirect_url );

// Or render it.
Installer::get()->render_plugin_button( 'event-tickets', 'install', 'Install Event Tickets', $redirect_url );
```

### Hooks

#### Action: `stellarwp/installer/{$hook_prefix}/deregister_plugin`

Fired when a plugin is deregistgered.

#### Action: `stellarwp/installer/{$hook_prefix}/register_plugin`

Fired after registering a plugin.

#### Filter: `stellarwp/installer/{$hook_prefix}/activated_label`

Filters the label used for the "activated" button.

#### Filter: `stellarwp/installer/{$hook_prefix}/activating_label`

Filters the label used for the "activating" button.

#### Filter: `stellarwp/installer/{$hook_prefix}/busy_class`

Filters the class used for the "busy" state.

#### Filter: `stellarwp/installer/{$hook_prefix}/button_classes`

Filters the classes used for the button.

#### Filter: `stellarwp/installer/{$hook_prefix}/button_id`

Filters the button id attribute for the button.

#### Filter: `stellarwp/installer/{$hook_prefix}/download_url`

Filters the download_url used for the installation of the plugin.

#### Filter: `stellarwp/installer/{$hook_prefix}/get_permission`

Filters the permissions used for the installation of the plugin.

#### Filter: `stellarwp/installer/{$hook_prefix}/install_error_message`

Filters the install error message.

#### Filter: `stellarwp/installer/{$hook_prefix}/installed_label`

Filters the label used for the "installed" button.

#### Filter: `stellarwp/installer/{$hook_prefix}/installing_label`

Filter the label used for the "installing" button.

#### Filter: `stellarwp/installer/{$hook_prefix}/nonce_name`

The name of the nonce field that is used when interacting with an install/activate button.

#### Filter: `stellarwp/installer/{$hook_prefix}/wordpress_org_data`

Filters the data returned from the WordPress.org plugin repository.
