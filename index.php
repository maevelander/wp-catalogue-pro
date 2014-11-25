<?php
/*
  Plugin Name: WP Catalogue Pro
  Plugin URI: http://www.enigmaplugins.com
  Description: Display your products in an attractive and professional catalogue. It's easy to use, easy to customise, and lets you show off your products in style.
  Author: Enigma Plugins
  Version: 1.2.6
  Author URI: http://www.enigmaplugins.com
 */
//testing repo braches Development
//creating db tables

error_reporting(0);

function customtaxorder_init() {
    global $wpdb;
    $init_query = $wpdb->query("SHOW COLUMNS FROM $wpdb->terms LIKE 'term_order'");
    if ($init_query == 0) {
        $wpdb->query("ALTER TABLE $wpdb->terms ADD `term_order` INT( 4 ) NULL DEFAULT '0'");
    }

    //	For Previous Users Product Images
    $prefix = $wpdb->prefix;
    $postSql = "SELECT DISTINCT post_id
                FROM " . $prefix . "postmeta As meta
                Inner Join " . $prefix . "posts As post
                On post.ID = meta.post_id
                Where post_type = 'wpcproduct' 
                And post_status = 'publish'
                And meta_key Like '%product_img%'";
    $postQry = mysql_query($postSql);
    while ($postRow = mysql_fetch_array($postQry)) {
        $post_id = $postRow['post_id'];
        $meta_key = "product_images";

        $sql = "Select post.*, meta.*
                From " . $prefix . "posts As post
                Inner Join " . $prefix . "postmeta As meta
                On post.ID = meta.post_id
                Where post_type = 'wpcproduct' 
                And post_status = 'publish'
                And meta_key Like '%product_img%'
                And post_id = " . $post_id;
        $qry = mysql_query($sql);
        $prod_key = array();
        $prod_value = array();
        $a = 0;
        while ($row = mysql_fetch_array($qry)) {
            $product_img = $row['meta_key'];

            $product_img = preg_replace("([0-9]+)", "", $product_img);

            $response[$a] = $row['meta_key'];
            $response[$a] = $row['meta_value'];

            $data[$a][$product_img] = $response[$a];

            $a = $a + 1;
        }
        //print_r($data);
        $data_serialize = serialize($data);

        $insert_images = "Insert Into " . $prefix . "postmeta(post_id,meta_key,meta_value) Value('$post_id','$meta_key','$data_serialize')";
        mysql_query($insert_images);
    }

    //	Delete All product_img1,product_img2,product_img3
    mysql_query("Delete From " . $prefix . "postmeta Where meta_key IN ('product_img1','product_img2','product_img3')");

    //	Update All product_price to Product Price
    $support_sql = "Select * From " . $prefix . "postmeta Where meta_key Like '%product_price%'";
    $support_qry = mysql_query($support_sql);
    while ($support_arr = mysql_fetch_array($support_qry)) {
        $supportMetaID = $support_arr['post_id'];
        $supportMetaPrice = $support_arr['meta_key'];

        $price_split = explode("_", $supportMetaPrice);
        $supportMetaPrice = $price_split[0] . " " . $price_split[1];
        $supportMetaPrice = ucwords($supportMetaPrice);

        $update_price = "Update " . $prefix . "postmeta
                         Set meta_key = '$supportMetaPrice'
                         Where post_id = $supportMetaID
                         And meta_key Like '%product_price%'";
        mysql_query($update_price);
    }

    //  Update all Previous "register_setting" Names
    $update_setting_1 = mysql_query("Update " . $prefix . "options Set option_name = 'wpc_grid_rows' Where option_name = 'grid_rows'");
    $update_setting_2 = mysql_query("Update " . $prefix . "options Set option_name = 'wpc_pagination' Where option_name = 'pagination'");
    $update_setting_3 = mysql_query("Update " . $prefix . "options Set option_name = 'wpc_show_bc' Where option_name = 'show_bc'");
    $update_setting_4 = mysql_query("Update " . $prefix . "options Set option_name = 'wpc_show_title' Where option_name = 'show_title'");
    $update_setting_5 = mysql_query("Update " . $prefix . "options Set option_name = 'wpc_sidebar' Where option_name = 'sidebar'");
    $update_setting_6 = mysql_query("Update " . $prefix . "options Set option_name = 'wpc_image_height' Where option_name = 'image_height'");
    $update_setting_7 = mysql_query("Update " . $prefix . "options Set option_name = 'wpc_image_width' Where option_name = 'image_width'");
    $update_setting_8 = mysql_query("Update " . $prefix . "options Set option_name = 'wpc_thumb_height' Where option_name = 'thumb_height'");
    $update_setting_9 = mysql_query("Update " . $prefix . "options Set option_name = 'wpc_thumb_width' Where option_name = 'thumb_width'");
    $update_setting_10 = mysql_query("Update " . $prefix . "options Set option_name = 'wpc_image_scale_crop' Where option_name = 'image_scale_crop'");
    $update_setting_11 = mysql_query("Update " . $prefix . "options Set option_name = 'wpc_thumb_scale_crop' Where option_name = 'thumb_scale_crop'");
    $update_setting_12 = mysql_query("Update " . $prefix . "options Set option_name = 'wpc_croping' Where option_name = 'croping'");
    $update_setting_13 = mysql_query("Update " . $prefix . "options Set option_name = 'wpc_tcroping' Where option_name = 'tcroping'");
    $update_setting_14 = mysql_query("Update " . $prefix . "options Set option_name = 'wpc_next_prev' Where option_name = 'next_prev'");
    $update_setting_15 = mysql_query("Update " . $prefix . "options Set option_name = 'wpc_inn_temp_head' Where option_name = 'inn_temp_head'");
    $update_setting_16 = mysql_query("Update " . $prefix . "options Set option_name = 'wpc_inn_temp_foot' Where option_name = 'inn_temp_foot'");
}

register_activation_hook(__FILE__, 'customtaxorder_init');

register_uninstall_hook('uninstall.php', $callback);

require 'wpc-catalogue.php';
require 'products/wpc-product.php';

define('WP_CATALOGUE', plugin_dir_url(__FILE__));
define('WP_CATALOGUE_PRODUCTS', WP_CATALOGUE . 'products');
define('WP_CATALOGUE_INCLUDES', WP_CATALOGUE . 'includes');
define('WP_CATALOGUE_CSS', WP_CATALOGUE_INCLUDES . '/css');
define('WP_CATALOGUE_JS', WP_CATALOGUE_INCLUDES . '/js');
//these were used but missing, please try to replace these with useful ones
define('WPC_SCRIPT', 'WPC_SCRIPT');
define('WPC_STYLE', 'WPC_STYLE');
define('WPCACHEHOME', WP_CATALOGUE);

// licensing
// adding scripts and styles to amdin
add_action('admin_enqueue_scripts', 'wp_catalogue_scripts_method');

function wp_catalogue_scripts_method() {
    global $current_screen;
    wp_deregister_script('wpc-js');
    wp_register_script('wpc-js', WP_CATALOGUE_JS . '/wpc.js');
    if ($current_screen->post_type == 'wpcproduct') {
        wp_enqueue_script('wpc-js');
    }
    wp_register_style('admin-css', WP_CATALOGUE_CSS . '/admin-styles.css');
    wp_enqueue_style('admin-css');
}

function wpc_admin_init() {
    $style_url = WP_CATALOGUE_CSS . '/sorting.css';
    wp_register_style(WPC_STYLE, $style_url);
    $script_url = WP_CATALOGUE_JS . '/sorting.js';
    wp_register_script(WPC_SCRIPT, $script_url, array('jquery', 'jquery-ui-sortable'));
}

function wpc_admin_images_cycle() {
    $script_url = plugin_dir_url(__FILE__) . 'includes/js/jquery.cycle2.js';
    wp_register_script(WPC_SCRIPT, $script_url, array('jquery', 'jquery-image'));
    wp_enqueue_script('jquery-image');
}

function wpc_admin_images() {
    $script_url = plugin_dir_url(__FILE__) . 'includes/js/jquery.cycle2.carousel.js';
    wp_register_script(WPC_SCRIPT, $script_url, array('jquery', 'jquery-slider'));
    wp_enqueue_script('jquery-slider');
}

add_action('admin_init', 'wpc_admin_init');
add_action('wp_enqueue_scripts', 'front_scripts');
add_action('wp_enqueue_scripts', 'wpc_admin_images_cycle');
add_action('wp_enqueue_scripts', 'wpc_admin_images');

function front_scripts() {
    global $bg_color;
    $bg_color = get_option('templateColorforProducts');
    wp_enqueue_script('jquery');
    wp_register_script('wpc-accord-js', WP_CATALOGUE_JS . '/daccordian.js');
    wp_enqueue_script('wpc-accord-js');
    wp_register_script('wpc-accordian-js', WP_CATALOGUE_JS . '/jquery.cookie.js');
    wp_enqueue_script('wpc-accordian-js');
    wp_register_script('wpc-images-js', WP_CATALOGUE_JS . '/jquery.cycle2.js');
    wp_enqueue_script('wpc-images-js');
    wp_register_script('wpc-image-js', WP_CATALOGUE_JS . '/jquery.cycle2.scrollVert.js');
    wp_enqueue_script('wpc-image-js');
    wp_register_script('wpc-image-carousel-js', WP_CATALOGUE_JS . '/jquery.cycle2.carousel.js');
    wp_enqueue_script('wpc-image-carousel-js');
    wp_register_style('catalogue-css', WP_CATALOGUE_CSS . '/catalogue-styles.css');
    wp_enqueue_style('catalogue-css');

    // For IE 7, 8
    wp_enqueue_style('style-ie', WP_CATALOGUE_CSS . '/ie.css', array(), '');
    wp_style_add_data('style-ie', 'conditional', 'lt IE 9');
}

// creating wp catalogue menus

add_action('admin_print_styles', 'wpc_admin_styles');
add_action('admin_print_scripts', 'wpc_admin_scripts');

add_action('admin_menu', 'wp_catalogue_menu');

function wp_catalogue_menu() {
    remove_submenu_page('edit.php?post_type=wpcproduct', 'post-new.php?post_type=wpcproduct');
    add_submenu_page('edit.php?post_type=wpcproduct', 'Order', 'Order', 'manage_options', 'customtaxorder', 'customtaxorder', 2);
    add_submenu_page('edit.php?post_type=wpcproduct', 'Settings', 'Settings', 'manage_options', 'catalogue_settings', 'wp_catalogue_settings');
    add_submenu_page('edit.php?post_type=wpcproduct', 'Plugin License', 'Activate License', 'manage_options', 'wpc-license', 'wpc_pro_license_page');
}

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define('WPC_PRO_STORE_URL', 'http://enigmaplugins.com'); // you should use your own CONSTANT name, and be sure to replace it throughout this file
// the name of your product. This should match the download name in EDD exactly
define('WPC_PRO_ITEM_NAME', 'WP Catalogue PRO'); // you should use your own CONSTANT name, and be sure to replace it throughout this file

if (!class_exists('EDD_SL_Plugin_Updater')) {
    // load our custom updater
    include( dirname(__FILE__) . '/wpc_plugin_updater.php' );
}

// retrieve our license key from the DB
$license_key = trim(get_option('wpc_pro_license_key'));

// setup the updater
$edd_updater = new EDD_SL_Plugin_Updater(WPC_PRO_STORE_URL, __FILE__, array(
    'version' => '1.2.6', // current version number
    'license' => $license_key, // license key (used get_option above to retrieve from DB)
    'item_name' => WPC_PRO_ITEM_NAME, // name of this plugin
    'author' => 'Enigma Plugins'  // author of this plugin
        )
);

/* * **********************************
 * the code below is just a standard
 * options page. Substitute with 
 * your own.
 * *********************************** */

function wpc_pro_license_page() {
    $license = get_option('wpc_pro_license_key');
    $status = get_option('wpc_pro_license_status');
    ?>

    <div class="wrap">
        <div class="wpc-left-liquid">
            <h2>
                <?php _e('Plugin License Options'); ?>
            </h2>
            <p><strong>Please enter and activate your license key in order to receive automatic updates and support for this plugin</strong></p>
            <form method="post" action="options.php">
    <?php settings_fields('wpc_pro_license'); ?>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row" valign="top"> <?php _e('License Key', 'wpc'); ?>
                            </th>
                            <td><input id="wpc_pro_license_key" name="wpc_pro_license_key" type="text" class="regular-text" value="<?php esc_attr_e($license); ?>" />
                                <label class="description" for="wpc_pro_license_key">
    <?php _e('Enter your license key'); ?>
                                </label></td>
                        </tr>
    <?php if (false !== $license) { ?>
                            <tr valign="top">
                                <th scope="row" valign="top"> <?php _e('Activate License'); ?>
                                </th>
                                <td><?php if ($status !== false && $status == 'valid') { ?>
                                        <span style="color:green;">
                                        <?php _e('active'); ?>
                                        </span>
                                        <?php wp_nonce_field('wpc_pro_nonce', 'wpc_pro_nonce'); ?>
                                        <input type="submit" class="button-secondary" name="wpc_pro_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
                            <?php } else {
                                wp_nonce_field('wpc_pro_nonce', 'wpc_pro_nonce');
                                ?>
                                        <input type="submit" class="button-secondary" name="wpc_pro_license_activate" value="<?php _e('Activate License'); ?>"/>
                    <?php } ?></td>
                            </tr>
    <?php } ?>
                    </tbody>
                </table>
    <?php submit_button(); ?>
            </form>
        </div>
        <div class="wpc-right-liquid">
            <table cellpadding="0" class="widefat" style="margin-bottom:10px;" width="50%">
                <thead>
                <th scope="col"><strong style="color:#008001;">
    <?php _e('How to use this plugin', 'wpc') ?>
                    </strong></th>
                </thead>
                <tbody>
                    <tr>
                        <td style="border:0;"><?php _e('You can use 3 shortcodes', 'wpc') ?></td>
                    </tr>
                    <tr>
                        <td style="border:0;"><b>1. [wp-catalogue] </b>
    <?php _e('to display complete catalogue', 'wpc') ?></td>
                    </tr>
                    <tr>
                        <td style="border:0;"><b>2. [wp-catalogue featured="true"]</b>
    <?php _e('to display featured products anywhere on your blog.', 'wpc') ?></td>
                    </tr>
                    <tr>
                        <td style="border:0;"><b>3. [wp-catalogue wpcat="wpc-category-slug"] </b>
    <?php _e('to display products from specific category.', 'wpc') ?></td>
                    </tr>
                </tbody>
            </table>
            <table cellpadding="0" class="widefat donation" style="margin-bottom:10px; border:solid 2px #008001;" width="50%">
                <thead>
                <th scope="col"><strong style="color:#008001;">
    <?php _e('Help Improve This Plugin!', 'wpc') ?>
                    </strong></th>
                </thead>
                <tbody>
                    <tr>
                        <td style="border:0;"><?php _e('Enjoyed this plugin? All donations are used to improve and further develop this plugin. Thanks for your contribution.', 'wpc') ?></td>
                    </tr>
                    <tr>
                        <td style="border:0;"><form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                                <input type="hidden" name="cmd" value="_s-xclick">
                                <input type="hidden" name="hosted_button_id" value="A74K2K689DWTY">
                                <input type="image" src="https://www.paypalobjects.com/en_AU/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
                                <img alt="" border="0" src="https://www.paypalobjects.com/en_AU/i/scr/pixel.gif" width="1" height="1">
                            </form></td>
                    </tr>
                    <tr>
                        <td style="border:0;"><?php _e('you can also help by', 'wpc') ?>
                            <a href="http://wordpress.org/support/view/plugin-reviews/wp-catalogue" target="_blank">
    <?php _e('rating this plugin on wordpress.org', 'wpc') ?>
                            </a></td>
                    </tr>
                </tbody>
            </table>
            <table cellpadding="0" class="widefat" border="0">
                <thead>
                <th scope="col"><?php _e('Need Support?', 'wpc') ?></th>
                </thead>
                <tbody>
                    <tr>
                        <td style="border:0;"><?php _e('Check out the', 'wpc') ?>
                            <a href="http://enigmaplugins.com/documentation/" target="_blank">FAQs</a>
    <?php _e('and', 'wpc') ?>
                            <a href="http://wordpress.org/support/plugin/wp-catalogue" target="_blank">
    <?php _e('Support Forums', 'wpc') ?>
                            </a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

function wpc_pro_register_option() {
    // creates our settings in the options table
    register_setting('wpc_pro_license', 'wpc_pro_license_key', 'wpc_sanitize_license');
}

add_action('admin_init', 'wpc_pro_register_option');

function wpc_sanitize_license($new) {
    $old = get_option('wpc_pro_license_key');
    if ($old && $old != $new) {
        delete_option('wpc_pro_license_status'); // new license has been entered, so must reactivate
    }
    return $new;
}

/* * **********************************
 * this illustrates how to activate 
 * a license key
 * *********************************** */

function wpc_pro_activate_license() {

    // listen for our activate button to be clicked
    if (isset($_POST['wpc_pro_license_activate'])) {

        // run a quick security check 
        if (!check_admin_referer('wpc_pro_nonce', 'wpc_pro_nonce'))
            return; // get out if we didn't click the Activate button

            
// retrieve the license from the database
        $license = trim(get_option('wpc_pro_license_key'));

        // data to send in our API request
        $api_params = array(
            'edd_action' => 'activate_license',
            'license' => $license,
            'item_name' => urlencode(WPC_PRO_ITEM_NAME) // the name of our product in EDD
        );

        // Call the custom API.
        $response = wp_remote_get(add_query_arg($api_params, WPC_PRO_STORE_URL), array('timeout' => 15, 'sslverify' => false));

        // make sure the response came back okay
        if (is_wp_error($response))
            return false;

        // decode the license data
        $license_data = json_decode(wp_remote_retrieve_body($response));

        // $license_data->license will be either "active" or "inactive"

        update_option('wpc_pro_license_status', $license_data->license);
    }
}

add_action('admin_init', 'wpc_pro_activate_license');

/* * *********************************************
 * Illustrates how to deactivate a license key.
 * This will descrease the site count
 * ********************************************* */

function wpc_pro_deactivate_license() {

    // listen for our activate button to be clicked
    if (isset($_POST['wpc_pro_license_deactivate'])) {

        // run a quick security check 
        if (!check_admin_referer('wpc_pro_nonce', 'wpc_pro_nonce'))
            return; // get out if we didn't click the Activate button

            
// retrieve the license from the database
        $license = trim(get_option('wpc_pro_license_key'));

        // data to send in our API request
        $api_params = array(
            'edd_action' => 'deactivate_license',
            'license' => $license,
            'item_name' => urlencode(WPC_PRO_ITEM_NAME) // the name of our product in EDD
        );

        // Call the custom API.
        $response = wp_remote_get(add_query_arg($api_params, WPC_PRO_STORE_URL), array('timeout' => 15, 'sslverify' => false));

        // make sure the response came back okay
        if (is_wp_error($response))
            return false;

        // decode the license data
        $license_data = json_decode(wp_remote_retrieve_body($response));

        // $license_data->license will be either "deactivated" or "failed"
        if ($license_data->license == 'deactivated')
            delete_option('wpc_pro_license_status');
    }
}

add_action('admin_init', 'wpc_pro_deactivate_license');

/* * **********************************
 * this illustrates how to check if 
 * a license key is still valid
 * the updater does this for you,
 * so this is only needed if you
 * want to do something custom
 * *********************************** */

function wpc_pro_check_license() {

    global $wp_version;

    $license = trim(get_option('wpc_pro_license_key'));

    $api_params = array(
        'edd_action' => 'check_license',
        'license' => $license,
        'item_name' => urlencode(WPC_PRO_ITEM_NAME)
    );

    // Call the custom API.
    $response = wp_remote_get(add_query_arg($api_params, WPC_PRO_STORE_URL), array('timeout' => 15, 'sslverify' => false));

    if (is_wp_error($response))
        return false;

    $license_data = json_decode(wp_remote_retrieve_body($response));

    if ($license_data->license == 'valid') {
        echo 'valid';
        exit;
        // this license is still valid
    } else {
        echo 'invalid';
        exit;
        // this license is no longer valid
    }
}

//licensing and updates
// add required styles
function wpc_admin_styles() {
    wp_enqueue_style(WPC_STYLE);
}

// add required scripts
function wpc_admin_scripts() {
    wp_enqueue_script(WPC_SCRIPT);
}

add_action('admin_init', 'register_catalogue_settings');
$plugin_dir_path = dirname(__FILE__);

function register_catalogue_settings() {
    register_setting('baw-settings-group', 'wpc_grid_rows');
    register_setting('baw-settings-group', 'wpc_pagination');
    register_setting('baw-settings-group', 'templateColorforProducts');  // new added color picker
    register_setting('baw-settings-group', 'wpc_show_bc'); // show bread crumbs PRO-feature
    register_setting('baw-settings-group', 'wpc_show_title'); // show title PRO-feature
    register_setting('baw-settings-group', 'wpc_sidebar'); // on/off sidebar premium feature
    register_setting('baw-settings-group', 'wpc_image_height');
    register_setting('baw-settings-group', 'wpc_image_width');
    register_setting('baw-settings-group', 'wpc_thumb_height');
    register_setting('baw-settings-group', 'wpc_thumb_width');
    register_setting('baw-settings-group', 'wpc_image_scale_crop');
    register_setting('baw-settings-group', 'wpc_thumb_scale_crop');
    register_setting('baw-settings-group', 'wpc_croping');
    register_setting('baw-settings-group', 'wpc_tcroping');
    register_setting('baw-settings-group', 'wpc_next_prev');
    register_setting('baw-settings-group', 'wpc_inn_temp_head');
    register_setting('baw-settings-group', 'wpc_inn_temp_foot');
    register_setting('baw-settings-group', 'wpc_all_product_label');
    register_setting('baw-settings-group', 'wpc_accordion_setting');
    add_option('wpc_accordion_setting', 'yes', '', 'yes');
    register_setting('baw-settings-group', 'wpc_custom_fields');
}

function wp_catalogue_settings() {
    require 'settings.php';
}

require 'products/order.php';
add_action("template_redirect", 'my_theme_redirect');

function my_theme_redirect() {

    global $wp;
    $plugindir = dirname(__FILE__);
    //A Specific Custom Post Type
    if ($wp->query_vars["post_type"] == 'wpcproduct') {
        $templatefilename = 'single-wpcproduct.php';
        if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
            $return_template = TEMPLATEPATH . '/' . $templatefilename;
        } else {
            $return_template = $plugindir . '/themefiles/' . $templatefilename;
        }
        do_theme_redirect($return_template);
    }
    if (is_tax('wpccategories')) {
        $templatefilename = 'taxonomy-wpccategories.php';
        if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
            $return_template = TEMPLATEPATH . '/' . $templatefilename;
        } else {
            $return_template = $plugindir . '/themefiles/' . $templatefilename;
        }
        do_theme_redirect($return_template);
    }
}

function do_theme_redirect($url) {
    global $post, $wp_query;
    if (have_posts()) {
        include($url);
        die();
    } else {
        $wp_query->is_404 = true;
    }
}

add_action('admin_notices', 'dev_check_current_screen');

add_filter('wp_list_categories', 'style_current_cat_single_post');

// filter to add the .current-cat class to categories list in single post
function style_current_cat_single_post($val) {
    if (is_single()) :
        global $post;
        foreach (get_the_category($post->ID) as $cat) {
            $cats[] = $cat->term_id;
        }
        foreach ($cats as $value) {
            if (preg_match('#item-' . $value . '">#', $output)) {
                $output = str_replace('item-' . $value . '">', 'item-' . $value . 'active-wpc-cat">', $output);
            }
        }
    endif;
    return $val;
}

/* ========================  pick color through Iris =========================== */

add_action('admin_enqueue_scripts', 'mw_enqueue_color_picker');

function mw_enqueue_color_picker($hook_suffix) {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('my-script-handle', plugins_url('my-script.js', __FILE__), array('wp-color-picker'), false, true);
}

/* ========================  Multicolor =========================== */

load_plugin_textdomain('wpc', WPCACHEHOME . 'languages', basename(dirname(__FILE__)) . '/languages');

/* ========================  Take User Defined color =========================== */

add_action('wp_head', 'colorPalette');

function colorPalette() {
    ?>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <meta name="HandheldFriendly" content="true">
    <style type="text/css">
        .wpc-img:hover {
            border: 5px solid <?php echo get_option('templateColorforProducts');
            ?> !important;
        }
        .wpc-title {
            color: <?php echo get_option('templateColorforProducts');
            ?> !important;
        }
        .wpc-title a:hover {
            color: <?php echo get_option('templateColorforProducts');
            ?> !important;
        }
        #wpc-col-1 ul li a:hover, #wpc-col-1 ul li.active-wpc-cat a {
            border-right: none;
            background:<?php echo get_option('templateColorforProducts');
            ?> no-repeat left top !important;
        }
        .wpc-paginations a:hover, .wpc-paginations .active-wpc-page {
            background: <?php echo get_option('templateColorforProducts');
            ?> !important;
        }
        .checking{
            background-color:<?php echo get_option('templateColorforProducts');
            ?>  !important;
        }
        .wpc-post-meta-key {
            color: <?php echo get_option('templateColorforProducts');
            ?> !important;
        }	

        .navigation { list-style:none; font-size:12px; }
        .navigation li{ display:inline; }
        .navigation li a{ display:block; float:left; padding:4px 9px; margin-right:7px; border:1px solid #efefef; }
        .navigation li span.current { display:block; float:left; padding:4px 9px; margin-right:7px; border:1px solid #efefef; background-color:#f5f5f5;  }	
        .navigation li span.dots { display:block; float:left; padding:4px 9px; margin-right:7px;  }	

        .current-cat{
            background-color: <?php echo get_option('templateColorforProducts'); ?> !important;
        }

    </style>
    <script type="text/javascript">

    <?php
    if (get_option('wpc_accordion_setting') == yes) {
        ?>
            jQuery(document).ready(function() {
                jQuery('#accordion').dcAccordion({
                    eventType: 'click',
                    disableLink: true,
                    saveState: true,
                    autoClose: false,
                    speed: 'fast'
                });
            });
        <?php
    }
    ?>
        jQuery(document).ready(function($) {
            $(".checking").click(function() {
                $(".wpc-categories").toggle();
                return (false);
            });

            var slideshows = $('.cycle-slideshow').on('cycle-next cycle-prev', function(e, opts) {
                // advance the other slideshow
                slideshows.not(this).cycle('goto', opts.currSlide);
            });

            $('#cycle-2 .cycle-slide').click(function() {
                var index = $('#cycle-2').data('cycle.API').getSlideIndex(this);
                slideshows.cycle('goto', index);
            });
        });

    </script>
<?php
}

/* ========================  Support Specific Custom Field =========================== */

function wpc_the_meta() {
    $custom_field_keys = get_post_custom_keys();

    $wpc_custom_fields = get_option("wpc_custom_fields");

    if ($wpc_custom_fields == "yes" && $wpc_custom_fields != "") {
        echo '<ul class="wpc-the-meta">';
        foreach ($custom_field_keys as $key => $value) {
            $valuet = trim($value);
            if ('_' == $valuet{0} || 'product_images' == $valuet || 'is_featured' == $valuet || 'Product Price' == $valuet) {
                continue;
            }
            $values = array_map('trim', get_post_custom_values($valuet));
            $key_value = implode($values, ', ');
            echo '<li><span class="wpc-post-meta-key">' . $value . " </span> " . $key_value . "</li>";
        }
        echo "</ul>\n";
    }
}

function wpc_accordiaon() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery('.wpc-categories li a.active').find('.wpc-expand').toggleClass('wpc-collapse');
        })
    </script>
<?php
}

add_action('wp_head', 'wpc_accordiaon');
