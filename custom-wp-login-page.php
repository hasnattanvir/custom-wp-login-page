<?php 
/**
 * Plugin Name: Custom WP Login Page
 * Description: A custom wp-admin dashboard capabilities.
 * Version: 1.0
 * Author: A H Tanvir
 * Requires at least: 5.6
 * Requires PHP: 8.0
 * Author URI: https://github.com/hasnattanvir
 * License: GPL V2 or later
 * License URI: http://www.gnu.org/licenses/lgpl.html
 * Update URI: https://github.com/hasnattanvir/contact-form-ht-plugin
 * Company Name: linuxbangla
 * Text Domain: cwlpht
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('CustomWPLoginPage')) {
    class CustomWPLoginPage {
        public function __construct() {
            add_action('admin_menu', array($this, 'add_theme_page'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
            add_action('login_enqueue_scripts', array($this, 'enqueue_login_styles'));
            add_action('login_enqueue_scripts', array($this, 'custom_login_logo'));
            add_action('admin_init', array($this, 'handle_file_upload'));
            register_activation_hook(__FILE__, array($this, 'plugin_activation'));
            add_action('admin_init', array($this, 'plugin_redirect'));
        }

        public function add_theme_page() {
            add_menu_page(
                'Login Option for Admin',
                'Login Option',
                'manage_options',
                'cwlpht-plugin-option',
                array($this, 'create_page'),
                'dashicons-unlock',
                101
            );
        }

        public function enqueue_admin_styles() {
            wp_enqueue_style('cwlpht_admin_styles', plugins_url('assets/css/plg_theme.css', __FILE__), false, "1.0.0");
        }

        public function enqueue_login_styles() {
            wp_enqueue_style('cwlpht_login_styles', plugins_url('assets/css/cwlpht_style.css', __FILE__), false, "1.0.0");
        }

        public function custom_login_logo() {
            $primary_color = get_option('cwlpht_primary_color');
            $logo_url = get_option('cwlpht_main_logo');
            $bg_img_url = get_option('cwlpht_bg_img');
            $bg_brightness = get_option('cwlpht_bg_brightness');
            ?>
            <style>
                #login h1 a, .login h1 a {
                    background-image: url(<?php echo esc_url($logo_url); ?>);
                }
                .login {
                    background-image: url(<?php echo esc_url($bg_img_url); ?>);
                }
                .login::after {
                    opacity: <?php echo esc_attr($bg_brightness); ?> !important;
                }
                #loginform{
                    background-color: <?php echo esc_attr($primary_color); ?>;
                }
            </style>
            <?php
        }

        public function create_page() { ?>
            <div class="main_box">
                <div class="leftBox">
                    <h3 id="title"><?php esc_attr_e("Login Page Customizer", 'cwlpht'); ?></h3>
                    <?php settings_errors(); ?>
                    <form action="options.php" method="post" enctype="multipart/form-data">
                        <?php wp_nonce_field("update-options"); ?>
                        <!-- Primary Color -->
                        <div class="input_item color_item">
                            <label for="cwlpht_pri_color">
                                <?php esc_attr_e("Primary Color", 'cwlpht'); ?>
                            </label>
                            <input type="color" name="cwlpht_primary_color" value="<?php echo esc_attr(get_option('cwlpht_primary_color')); ?>">
                        </div>

                        <!-- Main Logo -->
                        <div class="input_item logo_item">
                            <label for="cwlpht_main_logo">
                                <?php esc_attr_e("Upload Your Logo", 'cwlpht'); ?>
                            </label>
                            <input type="file" name="cwlpht_main_logo">
                        </div>

                        <!-- Background Image -->
                        <div class="input_item bgimg_item">
                            <label for="cwlpht_bg_img">
                                <?php esc_attr_e("Upload Your BG Image", 'cwlpht'); ?>
                            </label>
                            <input type="file" name="cwlpht_bg_img">
                        </div>

                        <!-- Background Brightness -->
                        <div class="input_item opacity_item">
                            <label for="cwlpht_bg_brightness">
                                <?php esc_attr_e("Background Brightness", 'cwlpht'); ?>
                            </label>
                            <input type="text" name="cwlpht_bg_brightness" value="<?php echo esc_attr(get_option('cwlpht_bg_brightness')); ?>" placeholder="Opacity">
                        </div>

                        <br>
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="page_options" value="cwlpht_primary_color,cwlpht_bg_brightness">
                        <input class="button button-primary" type="submit" name="submit" value="<?php esc_attr_e('Save Change', 'cwlpht'); ?>">
                    </form>
                </div>
                <div class="rightBox">
                    <!-- Preview or additional content here -->
                    <h3 class="righttitle">Custom Admin Login</h3>
                    <p><b>info:</b> Lorem ipsum dolor sit amet consectetur adipisicing elit. Non, dolore aut nisi eius impedit eligendi est voluptatum corporis quam laudantium vero repudiandae, cupiditate iusto dolorem iure possimus quaerat consequuntur veniam?</p>
       
                </div>
            </div>
        <?php
        }

        public function handle_file_upload() {
            $allowed_mime_types = array('image/jpeg', 'image/png', 'image/jpg');

            // Handle main logo upload
            if (isset($_FILES['cwlpht_main_logo']) && !empty($_FILES['cwlpht_main_logo']['name'])) {
                $file = $_FILES['cwlpht_main_logo'];
                if (in_array($file['type'], $allowed_mime_types)) {
                    $upload = wp_handle_upload($file, array('test_form' => false));
                    if (!isset($upload['error']) && isset($upload['file'])) {
                        $wp_filetype = wp_check_filetype($upload['file'], null);
                        $attachment = array(
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title' => sanitize_file_name($file['name']),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );
                        $attachment_id = wp_insert_attachment($attachment, $upload['file']);
                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        $attach_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                        wp_update_attachment_metadata($attachment_id, $attach_data);
                        update_option('cwlpht_main_logo', wp_get_attachment_url($attachment_id));
                    }
                } else {
                    add_settings_error('cwlpht_main_logo', 'invalid_file_type', __('Only JPG, JPEG, and PNG files are allowed for the logo.', 'cwlpht'));
                }
            }

            // Handle background image upload
            if (isset($_FILES['cwlpht_bg_img']) && !empty($_FILES['cwlpht_bg_img']['name'])) {
                $file = $_FILES['cwlpht_bg_img'];
                if (in_array($file['type'], $allowed_mime_types)) {
                    $upload = wp_handle_upload($file, array('test_form' => false));
                    if (!isset($upload['error']) && isset($upload['file'])) {
                        $wp_filetype = wp_check_filetype($upload['file'], null);
                        $attachment = array(
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title' => sanitize_file_name($file['name']),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );
                        $attachment_id = wp_insert_attachment($attachment, $upload['file']);
                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        $attach_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                        wp_update_attachment_metadata($attachment_id, $attach_data);
                        update_option('cwlpht_bg_img', wp_get_attachment_url($attachment_id));
                    }
                } else {
                    add_settings_error('cwlpht_bg_img', 'invalid_file_type', __('Only JPG, JPEG, and PNG files are allowed for the background image.', 'cwlpht'));
                }
            }

            // Handle other options
            if (isset($_POST['cwlpht_primary_color'])) {
                update_option('cwlpht_primary_color', sanitize_text_field($_POST['cwlpht_primary_color']));
            }

            if (isset($_POST['cwlpht_bg_brightness'])) {
                update_option('cwlpht_bg_brightness', sanitize_text_field($_POST['cwlpht_bg_brightness']));
            }
        }

        public function plugin_activation() {
            add_option('clpht_plugin_do_activation_redirect', true);
        }

        public function plugin_redirect() {
            if (get_option('clpht_plugin_do_activation_redirect', false)) {
                delete_option('clpht_plugin_do_activation_redirect');
                if (!isset($_GET['active-multi'])) {
                    wp_safe_redirect(admin_url('admin.php?page=cwlpht-plugin-option'));
                    exit;
                }
            }
        }
    }

    new CustomWPLoginPage();
}
?>
