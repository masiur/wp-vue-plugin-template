<?php

namespace WPVuePlugin;

class EnqueueScripts {
    /**
     * Enqueue the scripts required for the plugin.
     */
    public function enqueue() {
        // Check if WordPress is in debug mode
        $is_debug = defined('WP_DEBUG') && WP_DEBUG;

        if ($is_debug) {
            // Development mode: Use Vite's development server
            wp_enqueue_script(
                'vite-dev-script',                  // Handle
                'http://localhost:5170/src/main.js', // Vite Dev Server Script URL
                [],                                  // Dependencies
                null,                                // Version
                true                                 // Load in footer
            );

            // Add type="module" attribute
            add_filter('script_loader_tag', function ($tag, $handle) {
                if ($handle === 'vite-dev-script') {
                    return str_replace('<script ', '<script type="module" ', $tag);
                }
                return $tag;
            }, 10, 2);

            // Allow Vite dev server scripts to run with relaxed CSP
            header("Content-Security-Policy: script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:5170;");
        } else {
            // Production mode: Use the built files
            wp_enqueue_script(
                'wpplug-script',                   // Handle
                MY_PLUGIN_URL . 'dist/main.js',    // Script URL
                [],                                // Dependencies
                null,                              // Version
                true                               // Load in footer
            );

            // Add type="module" attribute
            add_filter('script_loader_tag', function ($tag, $handle) {
                if ($handle === 'wpplug-script') {
                    return str_replace('<script ', '<script type="module" ', $tag);
                }
                return $tag;
            }, 10, 2);
        }
    }
}


