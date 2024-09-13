<?php

namespace WPVuePlugin;

class AdminMenu {
    /**
     * Add the admin menu page for the plugin.
     */
    public function add_menu() {
        add_menu_page(
            'WP Plugin Vue Template',   // Page title
            'WP Plugin Vue Template',   // Menu title
            'manage_options',           // Capability
            'wp-plugin-vue-template',   // Menu slug
            [$this, 'render_menu_page'],// Callback function
            'dashicons-admin-plugins',  // Icon URL
            10                          // Position
        );
    }

    /**
     * Render the content for the admin page.
     */
    public function render_menu_page() {
        // Development or production environment detection
//        $is_debug = defined('WP_DEBUG') && WP_DEBUG;
//
//        header("Content-Security-Policy: script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:5170;");
//        // Render the Vue.js app container and load scripts dynamically
        echo '<div id="app"></div>';

    }
}
