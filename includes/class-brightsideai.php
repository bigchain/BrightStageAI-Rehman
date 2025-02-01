<?php
class BrightsideAI {
    public function __construct() {
        // Initialize both admin and frontend functionality
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_assets'));
        add_action('wp_ajax_brightsideai_save_project', array($this, 'save_project'));
        add_action('wp_ajax_brightsideai_get_projects', array($this, 'get_projects'));
        add_action('wp_ajax_brightsideai_delete_project', array($this, 'delete_project'));

        // Add defer attribute to Alpine.js
        add_filter('script_loader_tag', array($this, 'add_defer_attribute'), 10, 2);

        // Register activation hook for database setup
        register_activation_hook(BRIGHTSIDEAI_FILE, array($this, 'activate'));

        // Add action for saving prompts
        add_action('admin_post_brightsideai_save_prompts', array($this, 'save_prompts'));

        // Add action for saving API key
        add_action('admin_post_brightsideai_save_api_key', array($this, 'save_api_key'));

        // Add AJAX action for generating text
        add_action('wp_ajax_brightsideai_generate_text', array($this, 'generate_text'));

        // New credits endpoints
        add_action('wp_ajax_brightsideai_update_credits', array($this, 'update_credits'));
        add_action('wp_ajax_brightsideai_get_credits', array($this, 'get_credits'));
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
                    'nonce' => wp_create_nonce('brightsideai_nonce')
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
    
            // Enqueue scripts in correct order
            wp_enqueue_script('alpinejs');
            wp_enqueue_script('brightsideai-app');

            // Enqueue frontend-specific Alpine.js initialization script
            wp_enqueue_script(
                'brightsideai-frontend-init',
                BRIGHTSIDEAI_URL . 'assets/js/alpine-init-frontend.js',
                array('alpinejs', 'brightsideai-app'),
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

    public function save_prompts() {
        if (!current_user_can('manage_options')) {
            wp_die("Unauthorized");
        }
        
        check_admin_referer('brightsideai_save_prompts');
        
        $prompts = array(
            'brightsideai_enhance_prompt' => isset($_POST['brightsideai_enhance_prompt']) ? wp_kses_post($_POST['brightsideai_enhance_prompt']) : '',
            'brightsideai_slides_prompt' => isset($_POST['brightsideai_slides_prompt']) ? wp_kses_post($_POST['brightsideai_slides_prompt']) : '',
            'brightsideai_narration_prompt' => isset($_POST['brightsideai_narration_prompt']) ? wp_kses_post($_POST['brightsideai_narration_prompt']) : ''
        );
        
        foreach ($prompts as $option_name => $value) {
            update_option($option_name, $value);
        }
        
        wp_redirect(add_query_arg('updated', 'true', wp_get_referer()));
        exit;
    }

    public function generate_text() {
        check_ajax_referer('brightsideai_nonce', 'nonce');
        
        $prompt = isset($_POST['prompt']) ? sanitize_text_field($_POST['prompt']) : '';
        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'enhance';
        
        if (empty($prompt)) {
            wp_send_json_error('Prompt is required');
            return;
        }

        try {
            $api_key = get_option('brightsideai_openai_key');
            if (!$api_key) {
                wp_send_json_error('OpenAI API key is not configured');
                return;
            }

            $system_prompt = '';
            switch ($type) {
                case 'enhance':
                    $system_prompt = "You are a professional content writer specializing in webinar descriptions. Your task is to enhance the given webinar description while maintaining its core message. Make it more engaging, professional, and compelling. Focus on clarity, value proposition, and audience benefits.";
                    break;
                    
                case 'slides':
                    $system_prompt = "You are a professional presentation designer. Create a well-structured slide outline based on the webinar description. Format the output in Markdown with clear sections and bullet points. Each section should have a title and 3-5 key points. Focus on creating a logical flow that will engage the audience.";
                    break;
                    
                case 'narration':
                    $system_prompt = "You are a professional speaker and presentation coach. Create a natural, engaging narration script based on the webinar description. The narration should flow naturally, be conversational yet professional, and effectively convey the key messages. Include clear transitions between topics.";
                    break;
                    
                default:
                    wp_send_json_error('Invalid generation type');
                    return;
            }

            $body = array(
                'model' => 'gpt-4',
                'messages' => array(
                    array(
                        'role' => 'system',
                        'content' => $system_prompt
                    ),
                    array(
                        'role' => 'user',
                        'content' => $prompt
                    )
                ),
                'temperature' => 0.7,
                'max_tokens' => 1000
            );

            $args = array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type' => 'application/json'
                ),
                'body' => json_encode($body),
                'method' => 'POST',
                'data_format' => 'body',
                'timeout' => 45 // Increased timeout for GPT-4
            );

            error_log('Making OpenAI request for type: ' . $type);
            $response = wp_remote_post('https://api.openai.com/v1/chat/completions', $args);

            if (is_wp_error($response)) {
                error_log('BrightsideAI OpenAI API Error: ' . $response->get_error_message());
                wp_send_json_error('Failed to connect to OpenAI API. Please try again.');
                return;
            }

            $response_code = wp_remote_retrieve_response_code($response);
            if ($response_code !== 200) {
                error_log('BrightsideAI OpenAI API Error: Non-200 response code: ' . $response_code);
                error_log('Response body: ' . wp_remote_retrieve_body($response));
                wp_send_json_error('OpenAI API returned an error. Please try again.');
                return;
            }

            $result = json_decode(wp_remote_retrieve_body($response), true);
            if (!isset($result['choices'][0]['message']['content'])) {
                error_log('BrightsideAI OpenAI API Error: Unexpected response format');
                error_log('Response: ' . print_r($result, true));
                wp_send_json_error('Invalid response from OpenAI API. Please try again.');
                return;
            }

            $generated_text = $result['choices'][0]['message']['content'];
            wp_send_json_success($generated_text);

        } catch (Exception $e) {
            error_log('BrightsideAI OpenAI API Error: ' . $e->getMessage());
            wp_send_json_error('Failed to generate text. Please try again.');
        }
    }

    public function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'brightsideai_projects';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            name varchar(255) NOT NULL,
            short_description text,
            detailed_description longtext,
            enhanced_description longtext,
            script longtext,
            narration longtext,
            duration int(11) DEFAULT 30,
            credits_used int(11) DEFAULT 0,
            progress int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            last_modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            archived tinyint(1) DEFAULT 0,
            promo_pack longtext,
            assets longtext,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function save_project() {
        check_ajax_referer('brightsideai_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Unauthorized');
            return;
        }

        $project_data = json_decode(stripslashes($_POST['project']), true);
        if (!$project_data) {
            wp_send_json_error('Invalid project data');
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'brightsideai_projects';
        
        $data = array(
            'user_id' => get_current_user_id(),
            'name' => sanitize_text_field($project_data['name']),
            'short_description' => sanitize_textarea_field($project_data['shortDescription']),
            'detailed_description' => sanitize_textarea_field($project_data['detailedDescription']),
            'enhanced_description' => sanitize_textarea_field($project_data['enhancedDescription']),
            'script' => sanitize_textarea_field($project_data['script']),
            'narration' => sanitize_textarea_field($project_data['narration']),
            'duration' => intval($project_data['duration']),
            'credits_used' => intval($project_data['creditsUsed']),
            'progress' => intval($project_data['progress']),
            'archived' => intval($project_data['archived']),
            'promo_pack' => json_encode($project_data['promoPack']),
            'assets' => json_encode($project_data['assets'])
        );

        $format = array(
            '%d', // user_id
            '%s', // name
            '%s', // short_description
            '%s', // detailed_description
            '%s', // enhanced_description
            '%s', // script
            '%s', // narration
            '%d', // duration
            '%d', // credits_used
            '%d', // progress
            '%d', // archived
            '%s', // promo_pack
            '%s'  // assets
        );

        if (isset($project_data['id']) && $project_data['id']) {
            // Update existing project
            $wpdb->update(
                $table_name,
                $data,
                array('id' => intval($project_data['id']), 'user_id' => get_current_user_id()),
                $format,
                array('%d', '%d')
            );
            $project_id = intval($project_data['id']);
        } else {
            // Insert new project
            $wpdb->insert($table_name, $data, $format);
            $project_id = $wpdb->insert_id;
        }

        if ($project_id) {
            wp_send_json_success(array(
                'message' => 'Project saved successfully',
                'project_id' => $project_id
            ));
        } else {
            wp_send_json_error('Failed to save project');
        }
    }

    public function get_projects() {
        check_ajax_referer('brightsideai_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Unauthorized');
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'brightsideai_projects';
        
        $projects = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE user_id = %d ORDER BY last_modified DESC",
                get_current_user_id()
            ),
            ARRAY_A
        );

        if ($projects !== null) {
            // Format the projects for frontend
            $formatted_projects = array_map(function($project) {
                return array(
                    'id' => $project['id'],
                    'name' => $project['name'],
                    'shortDescription' => $project['short_description'],
                    'detailedDescription' => $project['detailed_description'],
                    'enhancedDescription' => $project['enhanced_description'],
                    'script' => $project['script'],
                    'narration' => $project['narration'],
                    'duration' => intval($project['duration']),
                    'creditsUsed' => intval($project['credits_used']),
                    'progress' => intval($project['progress']),
                    'createdAt' => $project['created_at'],
                    'lastModified' => $project['last_modified'],
                    'archived' => (bool)$project['archived'],
                    'promoPack' => json_decode($project['promo_pack'], true),
                    'assets' => json_decode($project['assets'], true)
                );
            }, $projects);

            wp_send_json_success($formatted_projects);
        } else {
            wp_send_json_error('Failed to retrieve projects');
        }
    }

    public function delete_project() {
        check_ajax_referer('brightsideai_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Unauthorized');
            return;
        }

        $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
        if (!$project_id) {
            wp_send_json_error('Invalid project ID');
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'brightsideai_projects';
        
        $result = $wpdb->delete(
            $table_name,
            array(
                'id' => $project_id,
                'user_id' => get_current_user_id()
            ),
            array('%d', '%d')
        );

        if ($result !== false) {
            wp_send_json_success('Project deleted successfully');
        } else {
            wp_send_json_error('Failed to delete project');
        }
    }

    public function update_credits() {
        check_ajax_referer('brightsideai_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Unauthorized');
            return;
        }

        $credits = isset($_POST['credits']) ? intval($_POST['credits']) : 0;
        update_user_meta(get_current_user_id(), 'brightsideai_credits', $credits);
        wp_send_json_success(array('credits' => $credits));
    }

    public function get_credits() {
        check_ajax_referer('brightsideai_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Unauthorized');
            return;
        }

        $credits = get_user_meta(get_current_user_id(), 'brightsideai_credits', true);
        if ($credits === '') {
            $credits = 100; // default credits
        } else {
            $credits = intval($credits);
        }
        wp_send_json_success(array('credits' => $credits));
    }

    private function log_error($message, $data = array()) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('BrightsideAI Error: ' . $message);
            if (!empty($data)) {
                error_log('Data: ' . print_r($data, true));
            }
        }
    }
}