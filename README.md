Explanation
Class Definition: The class CustomWPLoginPage encapsulates all the plugin functionality.
Constructor: The constructor method __construct hooks into the necessary WordPress actions.

Methods:

* add_theme_page: Adds a menu page in the WordPress admin.
* enqueue_admin_styles: Enqueues the admin styles.
* enqueue_login_styles: Enqueues the login page styles.
* custom_login_logo: Outputs custom CSS for the login logo and background.
* create_page: Outputs the HTML for the plugin's settings page.
* handle_file_upload: Handles file uploads and updates the options.
* plugin_activation: Handles plugin activation tasks.
* plugin_redirect: Redirects to the plugin settings page after activation.

2. Plugin Activation Hook
Ensure the plugin activation hook is set up to use the class method.
php
register_activation_hook(__FILE__, array('CustomWPLoginPage', 'plugin_activation'));

3. Initialize the Plugin
Instantiate the class to ensure the plugin functionality is initialized.

php
new CustomWPLoginPage();
This refactoring converts the procedural code into a class-based structure, providing better organization and encapsulation of the plugin's functionality.
