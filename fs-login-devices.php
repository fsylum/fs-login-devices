<?php
/**
 * Plugin Name: Login Devices
 * Plugin URI: https://github.com/fsylum/wp-plugin-boilerplate
 * Description: Track and display all users devices used during authentication process
 * Version: 1.0.1
 * Author: Firdaus Zahari
 * Author URI: https://fsylum.net
 * Requires at least: 5.6
 * Requires PHP:      7.3
 */

require __DIR__ . '/vendor/autoload.php';

define('FSLD_PLUGIN_URL', untrailingslashit(plugin_dir_url(__FILE__)));
define('FSLD_PLUGIN_PATH', untrailingslashit(plugin_dir_path(__FILE__)));
define('FSLD_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('FSLD_PLUGIN_VERSION', '1.0.1');

register_activation_hook(__FILE__, [Fsylum\LoginDevices\WP\Database::class, 'install']);

$plugin = new Fsylum\LoginDevices\App;

// Load internal WP componments
$plugin->addService(new Fsylum\LoginDevices\WP\Auth);
$plugin->addService(new Fsylum\LoginDevices\WP\Admin);
$plugin->addService(new Fsylum\LoginDevices\WP\Asset);

// Finally run it
$plugin->run();
