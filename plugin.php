<?php
/**
 * Plugin Name: WP Vue Plugin Template
 * Plugin URI: https://example.com/my-plugin
 * Description: This is a description of my plugin.
 * Version: 1.0.0
 * Author: Masiur Rahman Siddiki
 * Author URI: https://example.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wp-vue-plugin-template
 * Domain Path: /languages
 */

// Define plugin constants for easy access
define('MY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MY_PLUGIN_URL', plugin_dir_url(__FILE__));

// Allow Vite scripts for development mode
function allow_vite_scripts_for_development($headers) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        // Append to the Content-Security-Policy header
        if (isset($headers['Content-Security-Policy'])) {
            $headers['Content-Security-Policy'] .= " script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:5170;";
        } else {
            $headers['Content-Security-Policy'] = "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:5170;";
        }
    }
    return $headers;
}
add_filter('wp_headers', 'allow_vite_scripts_for_development');


// Autoload classes using SPL autoloader
spl_autoload_register(function ($class) {
    // Define the namespace prefix for this plugin
    $prefix = 'WPVuePlugin\\';

    // Base directory for all class files in this plugin
    $base_dir = MY_PLUGIN_DIR . 'app/';

    // Check if the class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // If not, move to the next registered autoloader
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, and append .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load Composer's autoload file
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Initialize the plugin by instantiating the Bootstrap class
new WPVuePlugin\Bootstrap();
