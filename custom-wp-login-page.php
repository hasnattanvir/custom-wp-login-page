<?php 
/**
 * Plugin Name: Custom Wp Login Page
 * https://wordpress.org/plugins/contact-wp-login-page/
 * Description: lorem ipsum test plugin
 * Version:1.0.0
 * Requires at least:5.6
 * Requires PHP:8.0
 * Author:Hasnat
 * Author URI: https://ahtanvir.com
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

 function cwlpht_create_page(){?>

    <div class="main_box">
        <div class="leftBox">
            <h3 id="title"><?php print esc_attr("Login Page Customizer"); ?></h3>
            <form action="options.php" method="post">
                <?php wp_nonce_field("update-options"); ?>
                <!-- Primary Color -->
                <div class="input_item">
                    <label for="cwlpht_pri_color" name="cwlpht_pri_color">
                        <?php print esc_attr("Parimery Color"); ?>
                    </label>
                    <input type="color" name="cwlpht_primary_color" value="<?php print get_option('cwlpht_primary_color'); ?>">
                </div>

                <!-- Main Logo -->
                <div class="input_item">
                    <label for="cwlpht_main_logo" name="cwlpht_main_logo">
                        <?php print esc_attr("Upload Your Logo Link"); ?>
                    </label>
                    <input type="text" name="cwlpht_main_logo" value="<?php print get_option('cwlpht_main_logo'); ?>" placeholder="Past You Logo url here">
                </div>

                <!-- Background Image -->
                <div class="input_item">
                    <label for="cwlpht_bg_img" name="cwlpht_bg_img">
                        <?php print esc_attr("Upload Your BG image link"); ?>
                    </label>
                    <input type="text" name="cwlpht_bg_img" value="<?php print get_option('cwlpht_bg_img'); ?>" placeholder="Past Your image link">
                </div>

                <!-- Background Brightness -->
                <div class="input_item">
                    <label for="cwlpht_bg_brightness" name="cwlpht_bg_brightness">
                        <?php print esc_attr("Background Brightness"); ?>
                    </label>
                    <input type="text" name="cwlpht_bg_brightness" value="<?php print get_option('cwlpht_bg_brightness'); ?>" placeholder="Opacity">
                </div>

                <br>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="page_options" value="cwlpht_primary_color,cwlpht_main_logo,cwlpht_bg_img,cwlpht_bg_brightness">
                <input class="button button-primary" type="submit" name="submit" value="<?php _e('Save Change'); ?>">
            </form>
        </div>
        <div class="rightBox">

        </div>
    </div>

 <?php
 }

 function login_enqueue_register(){
    wp_enqueue_style('cwlpht_login_enque',plugins_url('assets/css/cwlpht_style.css', __FILE__ ),false,"1.0.0");
 }
 add_action('login_enqueue_scripts','login_enqueue_register');

 function login_logo_change(){
?>
    <style>
        #login h1 a, .login h1 a{
            background-image: url(<?php print get_option('cwlpht_main_logo'); ?>);
        }
        .login{
            background-image: url(<?php print get_option('cwlpht_bg_img'); ?>);
        }
        .login::after{
            opacity:<?php print get_option('cwlpht_bg_brightness'); ?> !important;
        }
    </style>
<?php
 }
add_action('login_enqueue_scripts','login_logo_change');

/**
 * plugin redirect feature
 */

register_activation_hook(__FILE__, 'clpht_plugin_activation');
function clpht_plugin_activation(){
    add_option('clpht_plugin_do_activation_redirect',true);
}
add_action('admin_init','clpht_plugin_redirect');
function clpht_plugin_redirect(){
    if(get_option('clpht_plugin_do_activation_redirect',false)){
        delete_option('clpht_plugin_do_activation_redirect');
        if(!isset($_GET['active-multi'])){
            wp_safe_redirect(admin_url('admin.php?page=cwlpht-plugin-option'));
            exit;
        }
    }
}


?>