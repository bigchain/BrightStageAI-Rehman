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

        // Add AJAX action for generating text
        add_action('wp_ajax_brightsideai_generate_text', array($this, 'generate_text'));

        // Add admin_post action for saving API key
        add_action('admin_post_brightsideai_save_api_key', array($this, 'save_api_key'));
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
        wp_enqueue_script(
            'brightsideai-admin-openai',
            BRIGHTSIDEAI_URL . 'assets/js/admin-openai.js',
            array('jquery'),
            BRIGHTSIDEAI_VERSION,
            true
        );
        
        wp_localize_script(
            'brightsideai-admin-openai',
            'brightsideaiAdmin',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('brightsideai_nonce')
            )
        );
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

    public function save_api_key() {
        if (!current_user_can('manage_options')) {
            wp_die("Unauthorized");
        }
        check_admin_referer('brightsideai_save_api_key');
        if (isset($_POST['brightsideai_api_key'])) {
            update_option('brightsideai_openai_key', sanitize_text_field($_POST['brightsideai_api_key']));
            wp_redirect(add_query_arg('updated', 'true', wp_get_referer()));
            exit;
        }
    }

    public function generate_text() {
        check_ajax_referer('brightsideai_nonce', 'nonce');
        $prompt = isset($_POST['prompt']) ? sanitize_text_field($_POST['prompt']) : "";
        $api_key = get_option('brightsideai_openai_key', '');
        if (empty($api_key)) {
            wp_send_json_error("OpenAI API key not set.");
        }
        if (empty($prompt)) {
            wp_send_json_error("Prompt is empty.");
        }
        
        $body = array(
            "model" => "gpt-3.5-turbo",
            "messages" => array(
                array(
                    "role" => "user",
                    "content" => $prompt
                )
            ),
            "max_tokens" => 150,
            "temperature" => 0.7
        );
        
        $args = array(
            "headers" => array(
                "Content-Type" => "application/json",
                "Authorization" => "Bearer $api_key"
            ),
            "body" => json_encode($body),
            "timeout" => 15,
        );
        
        $response = wp_remote_post("https://api.openai.com/v1/chat/completions", $args);
        
        if (is_wp_error($response)) {
            wp_send_json_error($response->get_error_message());
        }
        
        $data = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($data['choices'][0]['message']['content'])) {
            wp_send_json_success(trim($data['choices'][0]['message']['content']));
        } else {
            $error_message = isset($data['error']['message']) ? $data['error']['message'] : "No text generated.";
            wp_send_json_error($error_message);
        }
    }
}