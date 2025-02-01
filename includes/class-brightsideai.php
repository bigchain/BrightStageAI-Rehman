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

        // Add AJAX action for getting project
        add_action('wp_ajax_brightsideai_get_project', array($this, 'get_project'));
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
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Unauthorized');
            return;
        }

        $prompt = isset($_POST['prompt']) ? sanitize_text_field($_POST['prompt']) : '';
        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'custom';

        if (empty($prompt)) {
            wp_send_json_error('Prompt is required');
            return;
        }

        try {
            $api_key = get_option('brightsideai_openai_api_key');
            if (!$api_key) {
                wp_send_json_error('OpenAI API key not configured');
                return;
            }

            // Log the request
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('BrightsideAI: Generating text for type: ' . $type);
                error_log('BrightsideAI: Prompt: ' . $prompt);
            }

            $system_message = '';
            switch ($type) {
                case 'enhance':
                    $system_message = "You are an expert webinar content writer. Your task is to enhance the given webinar description to be more engaging and informative while maintaining its core message. Include:
                    1. A clear definition of the target audience
                    2. 3-5 specific learning objectives
                    3. Key takeaways or benefits
                    4. A structured outline of the content
                    
                    Keep the tone professional yet engaging, and ensure the description is between 500-1000 characters.";
                    break;
                case 'slides':
                    $system_message = "You are an expert presentation designer. Create a sequence of slide content based on the webinar description. Format as:

                    Slide 1: [Title]
                    - [Bullet point]
                    - [Bullet point]
                    
                    Slide 2: [Title]
                    - [Bullet point]
                    - [Bullet point]
                    
                    Create 8-12 slides with clear, concise bullet points. Focus on maintaining a logical flow and covering all key points from the description.";
                    break;
                case 'narration':
                    $system_message = "You are an expert presentation narrator. Create natural, conversational narration script for each slide. Format as:
                    
                    [Slide 1]
                    'Narration text that naturally introduces the topic...'
                    
                    [Slide 2]
                    'Narration text that flows from previous slide...'
                    
                    Make it engaging and conversational while maintaining professionalism. Ensure smooth transitions between slides.";
                    break;
                default:
                    wp_send_json_error('Invalid generation type');
                    return;
            }

            $messages = array(
                array(
                    'role' => 'system',
                    'content' => $system_message
                ),
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            );

            $body = array(
                'model' => 'gpt-3.5-turbo',
                'messages' => $messages,
                'max_tokens' => 2000,
                'temperature' => 0.7,
                'top_p' => 1,
                'frequency_penalty' => 0,
                'presence_penalty' => 0
            );

            $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type' => 'application/json'
                ),
                'body' => json_encode($body),
                'timeout' => 60,
                'data_format' => 'body'
            ));

            if (is_wp_error($response)) {
                $this->log_error('OpenAI API request failed', array(
                    'error' => $response->get_error_message(),
                    'type' => $type,
                    'prompt' => $prompt
                ));
                wp_send_json_error('Failed to connect to OpenAI API: ' . $response->get_error_message());
                return;
            }

            $response_code = wp_remote_retrieve_response_code($response);
            $response_body = json_decode(wp_remote_retrieve_body($response), true);

            if ($response_code !== 200) {
                $this->log_error('OpenAI API error response', array(
                    'code' => $response_code,
                    'body' => $response_body,
                    'type' => $type,
                    'prompt' => $prompt
                ));
                $error_message = isset($response_body['error']['message']) 
                    ? $response_body['error']['message'] 
                    : 'OpenAI API returned an error';
                wp_send_json_error($error_message);
                return;
            }

            if (!isset($response_body['choices'][0]['message']['content'])) {
                $this->log_error('Unexpected OpenAI API response format', array(
                    'body' => $response_body,
                    'type' => $type,
                    'prompt' => $prompt
                ));
                wp_send_json_error('Unexpected API response format');
                return;
            }

            $generated_text = $response_body['choices'][0]['message']['content'];

            // Log success
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('BrightsideAI: Successfully generated text for type: ' . $type);
            }

            wp_send_json_success($generated_text);

        } catch (Exception $e) {
            $this->log_error('Exception in generate_text', array(
                'error' => $e->getMessage(),
                'type' => $type,
                'prompt' => $prompt
            ));
            wp_send_json_error('Failed to generate text: ' . $e->getMessage());
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

        $project_data = isset($_POST['project']) ? json_decode(stripslashes($_POST['project']), true) : null;
        if (!$project_data || !isset($project_data['id'])) {
            wp_send_json_error('Invalid project data');
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'brightsideai_projects';
        
        // Log the incoming project data
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Saving project data: ' . print_r($project_data, true));
        }

        // Map camelCase to underscore fields
        $data = array(
            'title' => sanitize_text_field($project_data['title']),
            'description' => wp_kses_post($project_data['description'] ?? ''),
            'enhanced_description' => wp_kses_post($project_data['enhancedDescription'] ?? ''),
            'script' => wp_kses_post($project_data['script'] ?? ''),
            'narration' => wp_kses_post($project_data['narration'] ?? ''),
            'duration' => sanitize_text_field($project_data['duration'] ?? '15m'),
            'updated_at' => current_time('mysql')
        );

        $where = array(
            'id' => $project_data['id'],
            'user_id' => get_current_user_id()
        );

        $result = $wpdb->update($table_name, $data, $where);

        if ($result === false) {
            $this->log_error('Failed to save project', array(
                'error' => $wpdb->last_error,
                'project_data' => $project_data
            ));
            wp_send_json_error('Failed to save project: ' . $wpdb->last_error);
            return;
        }

        // Get the updated project data
        $updated_project = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d AND user_id = %d",
                $project_data['id'],
                get_current_user_id()
            ),
            ARRAY_A
        );

        // Convert DB field names to camelCase for JS
        if(isset($updated_project['enhanced_description'])) {
            $updated_project['enhancedDescription'] = $updated_project['enhanced_description'];
        }
        if(isset($updated_project['detailed_description'])) {
            $updated_project['description'] = $updated_project['detailed_description'];
        }

        wp_send_json_success($updated_project);
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
                "SELECT * FROM $table_name WHERE user_id = %d ORDER BY created_at DESC",
                get_current_user_id()
            ),
            ARRAY_A
        );

        // Convert field names for all projects
        foreach ($projects as &$project) {
            if(isset($project['enhanced_description'])) {
                $project['enhancedDescription'] = $project['enhanced_description'];
            }
            if(isset($project['detailed_description'])) {
                $project['description'] = $project['detailed_description'];
            }
        }

        wp_send_json_success($projects);
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

    public function get_project() {
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
        
        $project = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d AND user_id = %d",
                $project_id,
                get_current_user_id()
            ),
            ARRAY_A
        );

        if (!$project) {
            wp_send_json_error('Project not found');
            return;
        }

        // Convert DB field names to camelCase expected by JS
        $project = (array) $project;

        if(isset($project['enhanced_description'])) {
            $project['enhancedDescription'] = $project['enhanced_description'];
        } else {
            $project['enhancedDescription'] = '';
        }

        // If needed, map detailed_description to description; otherwise, keep description intact
        if(isset($project['detailed_description'])) {
            $project['description'] = $project['detailed_description'];
        } elseif(!isset($project['description'])) {
            $project['description'] = '';
        }

        // Log the project data being returned
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Project data retrieved: ' . print_r($project, true));
        }

        wp_send_json_success($project);
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