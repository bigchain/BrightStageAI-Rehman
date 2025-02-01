<?php
/**
 * Plugin Name: BrightsideAI
 * Description: AI-powered webinar creation platform
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: brightsideai
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('BRIGHTSIDEAI_PATH', plugin_dir_path(__FILE__));
define('BRIGHTSIDEAI_URL', plugin_dir_url(__FILE__));
define('BRIGHTSIDEAI_VERSION', '1.0.0');

// Include necessary files
require_once BRIGHTSIDEAI_PATH . 'includes/class-brightsideai.php';

// Initialize the plugin
function brightsideai_init() {
    $plugin = new BrightsideAI();
}
add_action('plugins_loaded', 'brightsideai_init');