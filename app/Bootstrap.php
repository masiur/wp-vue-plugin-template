<?php

namespace WPVuePlugin;

class Bootstrap {
    private $admin_menu;
    private $enqueue_scripts;

    public function __construct() {
        // Initialize plugin components
        $this->admin_menu = new AdminMenu();
        $this->enqueue_scripts = new EnqueueScripts();

        // Set up hooks
        $this->initialize_hooks();
    }

    /**
     * Register hooks for the plugin.
     */
    private function initialize_hooks() {
        // Hook for adding the admin menu
        add_action('admin_menu', [$this->admin_menu, 'add_menu']);

        // Register the AJAX action
        add_action('wp_ajax_handle_ai_request', [AIHandler::class, 'handle_request']);
        add_action('wp_ajax_nopriv_handle_ai_request', [AIHandler::class, 'handle_request']);

        // Hook for enqueueing scripts
        add_action('admin_enqueue_scripts', [$this->enqueue_scripts, 'enqueue']);
    }
}
