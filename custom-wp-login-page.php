<?php 
/**
 * Plugin Name: Custom Wp Login Page
 * https://wordpress.org/plugins/contact-wp-login-page/
 * Description: lorem ipsum test plugin
 * Version:1.0.0
 * Requires at least:5.6
 * Requires PHP:8.0
 * Author:Hasnat
 * Author URI: https://linuxbangla.com
 * License: GPL V2 or later
 * License URI: http://www.gnu.org/licenses/lgpl.html
 * Update URI: https://github.com/hasnattanvir/contact-form-ht-plugin
 * Text Domain:cwlpht
 */


/**
 * Plugin Option Page Function
 */

function cwlpht_add_theme_page(){
    add_menu_page(
        'Login Option for Admin',
        'Login Option',
        'manage_options',
        'cwlpht-plugin-option',
        'cwlpht_create_page',
        'dashicons-unlock',
        101
    );
}

/**
 * Plugin option page style
 */
function plg_theme_css(){
    wp_enqueue_style('cwlpht_login_enque',plugins_url('assets/css/plg_theme_css.css', __FILE__ ),false,"1.0.0");
 }
 add_action('admin_enqueue_scripts','plg_theme_css');

//hook admin_menu
add_action('admin_menu','cwlpht_add_theme_page');

/**
 * plugin call back
 */
function cwlpht_create_page(){ ?>
    <div class="main_box">
        <div class="leftBox">
            <h3 id="title"><?php print esc_attr("Login Page Customizer"); ?></h3>
            <?php settings_errors(); ?>
            <form action="options.php" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field("update-options"); ?>
                <!-- Primary Color -->
                <div class="input_item">
                    <label for="cwlpht_pri_color">
                        <?php print esc_attr("Primary Color"); ?>
                    </label>
                    <input type="color" name="cwlpht_primary_color" value="<?php print get_option('cwlpht_primary_color'); ?>">
                </div>

                <!-- Main Logo -->
                <div class="input_item">
                    <label for="cwlpht_main_logo">
                        <?php print esc_attr("Upload Your Logo"); ?>
                    </label>
                    <input type="file" name="cwlpht_main_logo">
                </div>

                <!-- Background Image -->
                <div class="input_item">
                    <label for="cwlpht_bg_img">
                        <?php print esc_attr("Upload Your BG Image"); ?>
                    </label>
                    <input type="file" name="cwlpht_bg_img">
                </div>

                <!-- Background Brightness -->
                <div class="input_item">
                    <label for="cwlpht_bg_brightness">
                        <?php print esc_attr("Background Brightness"); ?>
                    </label>
                    <input type="text" name="cwlpht_bg_brightness" value="<?php print get_option('cwlpht_bg_brightness'); ?>" placeholder="Opacity">
                </div>

                <br>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="page_options" value="cwlpht_primary_color,cwlpht_bg_brightness">
                <input class="button button-primary" type="submit" name="submit" value="<?php _e('Save Change'); ?>">
            </form>
        </div>
        <div class="rightBox">
            <!-- Preview or additional content here -->
        </div>
    </div>
 <?php
 }

 function login_enqueue_register(){
    wp_enqueue_style('cwlpht_login_enque',plugins_url('assets/css/cwlpht_style.css', __FILE__ ),false,"1.0.0");
 }
 add_action('login_enqueue_scripts','login_enqueue_register');

 function login_logo_change() {
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
            background-color: <?php echo $primary_color; ?>;
        }
    </style>
<?php
}
add_action('login_enqueue_scripts', 'login_logo_change');


/**
 * plugin redirect feature
 */

register_activation_hook(__FILE__, 'clpht_plugin_activation');
function clpht_plugin_activation(){
    add_option('clpht_plugin_do_activation_redirect',true);
}

// init admin
add_action('admin_init', 'cwlpht_handle_file_upload');
function cwlpht_handle_file_upload() {
    $allowed_mime_types = array('image/jpeg', 'image/png', 'image/jpg');

    // Handle main logo upload
    if (isset($_FILES['cwlpht_main_logo']) && !empty($_FILES['cwlpht_main_logo']['name'])) {
        $file = $_FILES['cwlpht_main_logo'];
        if (in_array($file['type'], $allowed_mime_types)) {
            $upload = wp_handle_upload($file, array('test_form' => false));
            if (!isset($upload['error']) && isset($upload['url'])) {
                update_option('cwlpht_main_logo', $upload['url']);
            }
        } else {
            add_settings_error('cwlpht_main_logo', 'invalid_file_type', 'Only JPG, JPEG, and PNG files are allowed for the logo.');
        }
    }

    // Handle background image upload
    if (isset($_FILES['cwlpht_bg_img']) && !empty($_FILES['cwlpht_bg_img']['name'])) {
        $file = $_FILES['cwlpht_bg_img'];
        if (in_array($file['type'], $allowed_mime_types)) {
            $upload = wp_handle_upload($file, array('test_form' => false));
            if (!isset($upload['error']) && isset($upload['url'])) {
                update_option('cwlpht_bg_img', $upload['url']);
            }
        } else {
            add_settings_error('cwlpht_bg_img', 'invalid_file_type', 'Only JPG, JPEG, and PNG files are allowed for the background image.');
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




?>