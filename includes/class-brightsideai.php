<?php
class BrightsideAI {
    public function __construct() {
        // Initialize both admin and frontend functionality
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_assets'));

        // Add defer attribute to Alpine.js
        add_filter('script_loader_tag', array($this, 'add_defer_attribute'), 10, 2);
    }

    public function init() {
        // Register shortcode
        add_shortcode('brightsideai', array($this, 'render_app'));
        add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue_assets'));
        
        // Add this new part to register Alpine.js globally
        wp_register_script(
            'alpinejs',
            'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js',
            array(),
            BRIGHTSIDEAI_VERSION,
            true
        );
    }

    public function add_defer_attribute($tag, $handle) {
        if ('alpinejs' === $handle) {
            return str_replace(' src', ' defer src', $tag);
        }
        return $tag;
    }

    public function admin_enqueue_assets($hook) {
        if ('toplevel_page_brightsideai' !== $hook) {
            return;
        }
    
        // Enqueue global Alpine.js registered in init
        wp_enqueue_script('alpinejs');
    
        // Enqueue admin-specific initialization script for Alpine.js
        wp_enqueue_script(
            'brightsideai-admin-init',
            BRIGHTSIDEAI_URL . 'assets/js/alpine-init-admin.js',
            array('alpinejs'),
            BRIGHTSIDEAI_VERSION,
            true
        );
    
        wp_enqueue_style(
            'brightsideai-tailwind',
            BRIGHTSIDEAI_URL . 'assets/css/tailwind.css',
            array(),
            BRIGHTSIDEAI_VERSION
        );
    
        wp_register_script(
            'brightsideai-admin',
            BRIGHTSIDEAI_URL . 'assets/js/admin.js',
            array(),
            BRIGHTSIDEAI_VERSION,
            true
        );
    
        wp_enqueue_script('brightsideai-admin');
    }

    public function frontend_enqueue_assets() {
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'brightsideai')) {
            wp_enqueue_style(
                'brightsideai-styles',
                BRIGHTSIDEAI_URL . 'assets/css/tailwind.css',
                array(),
                BRIGHTSIDEAI_VERSION
            );
                        
            // Register your app.js
            wp_register_script(
                'brightsideai-app',
                BRIGHTSIDEAI_URL . 'assets/js/app.js',
                array('alpinejs'),
                BRIGHTSIDEAI_VERSION,
                true
            );
    
            wp_localize_script(
                'brightsideai-app',
                'brightsideaiConfig',
                array(
                    'ajaxUrl' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('brightsideai-nonce')
                )
            );
    
            // Enqueue Flowbite JS (ensure it's loaded before app.js if needed)
            wp_enqueue_script(
                'flowbite-js',
                BRIGHTSIDEAI_URL . 'node_modules/flowbite/dist/flowbite.min.js',
                array(),
                BRIGHTSIDEAI_VERSION,
                true
            );
    
            // Enqueue scripts
            wp_enqueue_script('alpinejs');
            wp_enqueue_script('brightsideai-app');

            // Enqueue frontend-specific Alpine.js initialization script
            wp_enqueue_script(
                'brightsideai-frontend-init',
                BRIGHTSIDEAI_URL . 'assets/js/alpine-init-frontend.js',
                array('alpinejs'),
                BRIGHTSIDEAI_VERSION,
                true
            );
        }
    }

    public function add_admin_menu() {
        add_menu_page(
            'BrightsideAI',
            'BrightsideAI',
            'manage_options',
            'brightsideai',
            array($this, 'render_admin_page'),
            'dashicons-chart-area',
            20
        );
    }

    public function render_admin_page() {
        require_once BRIGHTSIDEAI_PATH . 'admin/templates/main.php';
    }

    public function render_app() {
        ob_start();
        require_once BRIGHTSIDEAI_PATH . 'templates/app.php';
        return ob_get_clean();
    }
}