<?php
/*
  Plugin Name: WP Catalogue Pro
  Plugin URI: http://www.enigmaplugins.com
  Description: Display your products in an attractive and professional catalogue. It's easy to use, easy to customise, and lets you show off your products in style.
  Author: Enigma Plugins
  Version: 1.4.7
  Author URI: http://www.enigmaplugins.com
 */

error_reporting(0);

/* ========================  Plugin Text Domain =========================== */
add_action('init', 'wpc_plugin_load_textdomain');
function wpc_plugin_load_textdomain() {
    load_plugin_textdomain('wpc', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

/* ========================  Plugin Activation Hook =========================== */
function customtaxorder_init($wpc_networkwide) {
    global $wpdb;
    $init_query = $wpdb->query("SHOW COLUMNS FROM $wpdb->terms LIKE 'term_order'");
    if ($init_query == 0) {
        $wpdb->query("ALTER TABLE $wpdb->terms ADD `term_order` INT( 4 ) NULL DEFAULT '0'");
    }

    //	For Previous Users Product Images
    $wpc_prefix = $wpdb->prefix;
    
    $postSql = $wpdb->get_results("SELECT DISTINCT post_id
                                   FROM ".$wpc_prefix."postmeta As meta
                                   Inner Join ".$wpc_prefix."posts As post
                                   On post.ID = meta.post_id
                                   Where post_type = 'wpcproduct' 
                                   And post_status = 'publish'
                                   And meta_key Like '%product_img%'");
    foreach($postSql as $postRow) :
        $post_id = $postRow->post_id;
        $meta_key = "product_images";

        $sql = $wpdb->get_results("Select post.*, meta.*
                                   From ".$wpc_prefix."posts As post
                                   Inner Join ".$wpc_prefix."postmeta As meta
                                   On post.ID = meta.post_id
                                   Where post_type = 'wpcproduct' 
                                   And post_status = 'publish'
                                   And meta_key Like '%product_img%'
                                   And post_id = ".$post_id);
        $prod_key = array();
        $prod_value = array();
        $a = 0;
        foreach($sql as $row) :
            $product_img = $row->meta_key;

            $product_img = preg_replace("([0-9]+)", "", $product_img);

            $response[$a] = $row->meta_key;
            $response[$a] = $row->meta_value;

            $data[$a][$product_img] = $response[$a];

            $a = $a + 1;
        endforeach;
        $data_serialize = serialize($data);

        $wpc_images_data = array(
                                'post_id' => $post_id,
                                'meta_key' => $meta_key,
                                'meta_value' => $data_serialize
                            );
        $wpdb->insert($wpc_prefix."postmeta", $wpc_images_data);

        delete_post_meta($post_id, 'product_img1');
        delete_post_meta($post_id, 'product_img2');
        delete_post_meta($post_id, 'product_img3');
    endforeach;
    
    // Rename Resized big images When user upgrade plugin from free(1.7.5) version to pro(1.4.4) version 
    $postBigSql = $wpdb->get_results("SELECT DISTINCT post_id
                                      FROM ".$wpdb->postmeta." AS meta
                                      INNER JOIN ".$wpdb->posts." AS post
                                      ON post.ID = meta.post_id
                                      WHERE post_type = 'wpcproduct'
                                      AND post_status = 'publish'
                                      AND meta_key IN ('product_img1_big', 'product_img2_big', 'product_img3_big')");
    foreach($postBigSql as $postBigRow) :
        $post_big_id = $postBigRow->post_id;
        $big_meta_key = "wpc_big_images";

        $big_sql = $wpdb->get_results("Select post.*, meta.*
                                       From ".$wpdb->posts." As post
                                       Inner Join ".$wpdb->postmeta." As meta
                                       On post.ID = meta.post_id
                                       Where post_type = 'wpcproduct' 
                                       And post_status = 'publish'
                                       And meta_key IN ('product_img1_big', 'product_img2_big', 'product_img3_big')
                                       And post_id = ".$post_big_id);

        $big_a = 0;
        foreach($big_sql as $big_row) :                    
            $big_response[$big_a] = $big_row->meta_key;
            $big_response[$big_a] = $big_row->meta_value;

            $big_data[$big_a]["wpc_big_img"] = $big_response[$big_a];

            $big_data = array_filter(array_map('array_filter', $big_data));

            $big_a = $big_a + 1;
        endforeach;
        $big_data_serialize = serialize($big_data);
      
        $wpc_big_images_data = array(
                                'post_id' => $post_big_id,
                                'meta_key' => $big_meta_key,
                                'meta_value' => $big_data_serialize
                           );
        $wpdb->insert($wpc_prefix."postmeta", $wpc_big_images_data);

        delete_post_meta($post_big_id, 'product_img1_big');
        delete_post_meta($post_big_id, 'product_img2_big');
        delete_post_meta($post_big_id, 'product_img3_big');
    endforeach;
    
    // Rename Resized thumb images When user upgrade plugin from free(1.7.5) version to pro(1.4.4) version 
    $postThumbSql = $wpdb->get_results("SELECT DISTINCT post_id
                                        FROM ".$wpdb->postmeta." AS meta
                                        INNER JOIN ".$wpdb->posts." AS post
                                        ON post.ID = meta.post_id
                                        WHERE post_type = 'wpcproduct'
                                        AND post_status = 'publish'
                                        AND meta_key IN ('product_img1_thumb', 'product_img2_thumb', 'product_img3_thumb')");
    foreach($postThumbSql as $postThumbRow) :
        $post_thumb_id = $postThumbRow->post_id;
        $thumb_meta_key = "wpc_thumb_images";

        $thumb_sql = $wpdb->get_results("Select post.*, meta.*
                                         From ".$wpdb->posts." As post
                                         Inner Join ".$wpdb->postmeta." As meta
                                         On post.ID = meta.post_id
                                         Where post_type = 'wpcproduct' 
                                         And post_status = 'publish'
                                         And meta_key IN ('product_img1_thumb', 'product_img2_thumb', 'product_img3_thumb')
                                         And post_id = ".$post_thumb_id);
        $thumb_a = 0;
        foreach($thumb_sql as $thumb_row) :                    
            $thumb_response[$thumb_a] = $thumb_row->meta_key;
            $thumb_response[$thumb_a] = $thumb_row->meta_value;

            $thumb_data[$thumb_a]["wpc_thumb_img"] = $thumb_response[$thumb_a];

            $thumb_data = array_filter(array_map('array_filter', $thumb_data));

            $thumb_a = $thumb_a + 1;
        endforeach;
        $thumb_data_serialize = serialize($thumb_data);
      
        $wpc_thumb_images_data = array(
                                    'post_id' => $post_thumb_id,
                                    'meta_key' => $thumb_meta_key,
                                    'meta_value' => $thumb_data_serialize
                               );
        $wpdb->insert($wpc_prefix."postmeta", $wpc_thumb_images_data);

        delete_post_meta($post_thumb_id, 'product_img1_thumb');
        delete_post_meta($post_thumb_id, 'product_img2_thumb');
        delete_post_meta($post_thumb_id, 'product_img3_thumb');
    endforeach;

    $support_sql = $wpdb->get_results("Select * From ".$wpc_prefix."postmeta Where meta_key Like '%product_price%'");
    foreach($support_sql as $support_arr) :
        $supportMetaID = $support_arr->post_id;
        $supportMetaPrice = $support_arr->meta_key;

        $supportMeta_priceValue = array('meta_key' => 'wpc_product_price');
        $supportMeta_priceWhere = array('meta_key' => 'product_price', 'post_id' => $supportMetaID);
        
        $wpdb->update($wpc_prefix."postmeta", $supportMeta_priceValue, $supportMeta_priceWhere);
    endforeach;

    $wpc_grid_rows = array('option_name' => 'wpc_grid_rows');
    $grid_rows = array('option_name' => 'grid_rows');
    $wpdb->update($wpc_prefix."options", $wpc_grid_rows, $grid_rows);

    $wpc_pagination = array('option_name' => 'wpc_pagination');
    $pagination = array('option_name' => 'pagination');
    $wpdb->update($wpc_prefix."options", $wpc_pagination, $pagination);

    $wpc_show_bc = array('option_name' => 'wpc_show_bc');
    $show_bc = array('option_name' => 'show_bc');
    $wpdb->update($wpc_prefix."options", $wpc_show_bc, $show_bc);

    $wpc_show_title = array('option_name' => 'wpc_show_title');
    $show_title = array('option_name' => 'show_title');
    $wpdb->update($wpc_prefix."options", $wpc_show_title, $show_title);

    $wpc_sidebar = array('option_name' => 'wpc_sidebar');
    $sidebar = array('option_name' => 'sidebar');
    $wpdb->update($wpc_prefix."options", $wpc_sidebar, $sidebar);

    $wpc_image_width = array('option_name' => 'wpc_image_width');
    $image_width = array('option_name' => 'image_width');
    $wpdb->update($wpc_prefix."options", $wpc_image_width, $image_width);
	
    $wpc_image_height = array('option_name' => 'wpc_image_height');
    $image_height = array('option_name' => 'image_height');
    $wpdb->update($wpc_prefix."options", $wpc_image_height, $image_height);

    $wpc_thumb_width = array('option_name' => 'wpc_thumb_width');
    $thumb_width = array('option_name' => 'thumb_width');
    $wpdb->update($wpc_prefix."options", $wpc_thumb_width, $thumb_width);

    $wpc_thumb_height = array('option_name' => 'wpc_thumb_height');
    $thumb_height = array('option_name' => 'thumb_height');
    $wpdb->update($wpc_prefix."options", $wpc_thumb_height, $thumb_height);

    $wpc_next_prev = array('option_name' => 'wpc_next_prev');
    $next_prev = array('option_name' => 'next_prev');
    $wpdb->update($wpc_prefix."options", $wpc_next_prev, $next_prev);

    $wpc_inn_temp_head = array('option_name' => 'wpc_inn_temp_head');
    $inn_temp_head = array('option_name' => 'inn_temp_head');
    $wpdb->update($wpc_prefix."options", $wpc_inn_temp_head, $inn_temp_head);

    $wpc_inn_temp_foot = array('option_name' => 'wpc_inn_temp_foot');
    $inn_temp_foot = array('option_name' => 'inn_temp_foot');
    $wpdb->update($wpc_prefix."options", $wpc_inn_temp_foot, $inn_temp_foot);
    
    if (function_exists('is_multisite') && is_multisite()) {
        // check if it is a network activation - if so, run the activation function for each blog id
        if ($wpc_networkwide) {
            $wpc_old_blog = $wpdb->blogid;
            // Get all blog ids
            $wpc_blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($wpc_blog_ids as $wpc_blog_id) {
                switch_to_blog($wpc_blog_id);
            }
            switch_to_blog($wpc_old_blog);
            return;
        }   
    }
}
register_activation_hook(__FILE__, 'customtaxorder_init');

/* ========================  Plugin Uninstall Hook =========================== */
register_uninstall_hook('uninstall.php', $callback);

require 'wpc-catalogue.php';
require 'products/wpc-product.php';

/* ========================  Define Values =========================== */
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
// adding scripts and styles to admin
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

add_action('admin_init', 'wpc_admin_init');
add_action('wp_enqueue_scripts', 'front_scripts');
function front_scripts() {
    global $bg_color;
    $bg_color = get_option('templateColorforProducts');
    wp_enqueue_script('jquery');
    
    wp_register_style('catalogue-css', WP_CATALOGUE_CSS . '/catalogue-styles.css');
    wp_enqueue_style('catalogue-css');
	
	wp_register_script('wpc-carousel-js', WP_CATALOGUE_JS . '/wpc-carousel.js');
    wp_enqueue_script('wpc-carousel-js');

    // For IE 7, 8
    wp_enqueue_style('style-ie', WP_CATALOGUE_CSS . '/ie.css', array(), '');
    wp_style_add_data('style-ie', 'conditional', 'lt IE 9');
}
add_action('admin_print_styles', 'wpc_admin_styles');
add_action('admin_print_scripts', 'wpc_admin_scripts');

// creating wp catalogue menus
add_action('admin_menu', 'wp_catalogue_menu');
function wp_catalogue_menu() {
    remove_submenu_page('edit.php?post_type=wpcproduct', 'post-new.php?post_type=wpcproduct');
    add_submenu_page('edit.php?post_type=wpcproduct', 'Order', __('Order', 'wpc'), 'manage_options', 'customtaxorder', 'customtaxorder', 2);
    add_submenu_page('edit.php?post_type=wpcproduct', 'Settings', __('Settings', 'wpc'), 'manage_options', 'catalogue_settings', 'wp_catalogue_settings');
    add_submenu_page('edit.php?post_type=wpcproduct', 'Plugin License', __('Activate License', 'wpc'), 'manage_options', 'wpc-license', 'wpc_pro_license_page');
	add_submenu_page('edit.php?post_type=wpcproduct', '', '', 'manage_options', 'image_resize', 'wpc_image_resize');
}

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'WPC_PRO_STORE_URL', 'http://enigmaplugins.com' );
// you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of your product. This should match the download name in EDD exactly
define( 'WPC_PRO_ITEM_NAME', 'WP Catalogue PRO' );
// you should use your own CONSTANT name, and be sure to replace it throughout this file

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
    // load our custom updater
    include( dirname( __FILE__ ) . '/wpc_plugin_updater.php' );
}

function wpc_plugin_updater() {
    // retrieve our license key from the DB
    $license_key = trim( get_option( 'wpc_pro_license_key' ) );

    // setup the updater
    $edd_updater = new EDD_SL_Plugin_Updater( WPC_PRO_STORE_URL, __FILE__, array(
                    'version' 	=> '1.4.7', 				// current version number
                    'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
                    'item_name' => WPC_PRO_ITEM_NAME, 	// name of this plugin
                    'author' 	=> 'Enigma Plugins'  // author of this plugin
            )
    );
}
add_action( 'admin_init', 'wpc_plugin_updater', 0 );

/************************************
* the code below is just a standard
* options page. Substitute with
* your own.
*************************************/

function wpc_pro_license_page() {
    $license 	= get_option( 'wpc_pro_license_key' );
    $status 	= get_option( 'wpc_pro_license_status' );
?>
    <div class="wrap">
        <div class="wpc-left-liquid">
        <h2>
            <?php _e('Plugin License Options', 'wpc'); ?>
        </h2>
        <p>
            <strong>
                <?php 
                _e('Please enter and activate your license key in order to receive automatic updates and support for this plugin', 'wpc');
                ?>
            </strong>
        </p>
        <form method="post" action="options.php">
        <?php
            settings_fields('wpc_pro_license');
        ?>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row" valign="top">
                            <?php _e('License Key', 'wpc'); ?>
                        </th>
                        <td>
                            <input id="wpc_pro_license_key" name="wpc_pro_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
                            <label class="description" for="wpc_pro_license_key"><?php _e('Enter your license key', 'wpc'); ?></label>
                        </td>
                    </tr>
                <?php
                    if( false !== $license ) {
                ?>
                    <tr valign="top">
                        <th scope="row" valign="top">
                            <?php _e('Activate License', 'wpc'); ?>
                        </th>
                        <td>
                    <?php
                        if( $status !== false && $status == 'valid' ) {
                    ?>
                        <span style="color:green;"><?php _e('active','wpc'); ?></span>
                        <?php wp_nonce_field('wpc_pro_nonce', 'wpc_pro_nonce'); ?>
                        <input type="submit" class="button-secondary" name="wpc_pro_license_deactivate" value="<?php _e('Deactivate License','wpc'); ?>"/>
                    <?php
                        } else {
                    ?>
                        <span style="color:red;"><?php _e('deactive','hmp'); ?></span>
                    <?php
                        wp_nonce_field('wpc_pro_nonce', 'wpc_pro_nonce');
                    ?>
                        <input type="submit" class="button-secondary" name="wpc_pro_license_activate" value="<?php _e('Activate License','wpc'); ?>"/>
                    <?php
                        }
                    ?>
                        </td>
                    </tr>
                <?php
                    }
                ?>
                </tbody>
            </table>
            <?php submit_button(); ?>
        </form>
        </div>
        
        <div class="wpc-right-liquid">
            <table cellpadding="0" class="widefat" style="margin-bottom:10px;" width="50%">
                <thead>
                <th scope="col">
                    <strong style="color:#008001;">
                        <?php _e('How to use this plugin', 'wpc') ?>
                    </strong>
                </th>
                </thead>
                <tbody>
                    <tr>
                        <td style="border:0;"><?php _e('You can use 3 shortcodes', 'wpc') ?></td>
                    </tr>
                    <tr>
                        <td style="border:0;"><b>1. [wp-catalogue] </b>
                            <?php _e('to display complete catalogue', 'wpc') ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:0;"><b>2. [wp-catalogue featured="true"]</b>
                            <?php _e('to display featured products anywhere on your blog.', 'wpc') ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:0;"><b>3. [wp-catalogue wpcat="wpc-category-slug"] </b>
                            <?php _e('to display products from specific category.', 'wpc') ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table cellpadding="0" class="widefat donation" style="margin-bottom:10px; border:solid 2px #008001;" width="50%">
                <thead>
                <th scope="col">
                    <strong style="color:#008001;">
                        <?php _e('Help Improve This Plugin!', 'wpc') ?>
                    </strong>
                </th>
                </thead>
                <tbody>
                    <tr>
                        <td style="border:0;">
                            <?php _e('Enjoyed this plugin? All donations are used to improve and further develop this plugin. Thanks for your contribution.', 'wpc') ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:0;">
                            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                                <input type="hidden" name="cmd" value="_s-xclick">
                                <input type="hidden" name="hosted_button_id" value="A74K2K689DWTY">
                                <input type="image" src="https://www.paypalobjects.com/en_AU/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
                                <img alt="" border="0" src="https://www.paypalobjects.com/en_AU/i/scr/pixel.gif" width="1" height="1">
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:0;"><?php _e('you can also help by', 'wpc') ?>
                            <a href="http://wordpress.org/support/view/plugin-reviews/wp-catalogue" target="_blank">
                                <?php _e('rating this plugin on wordpress.org', 'wpc') ?>
                            </a>
                        </td>
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
                            <a href="http://enigmaplugins.com/contact-support" target="_blank">
                                <?php _e('Support', 'wpc') ?>
                            </a>
                        </td>
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

function wpc_sanitize_license( $new ) {
    $old = get_option( 'wpc_pro_license_key' );
    if( $old && $old != $new ) {
        delete_option( 'wpc_pro_license_status' ); // new license has been entered, so must reactivate
    }
    return $new;
}

/************************************
* this illustrates how to activate
* a license key
*************************************/

function wpc_pro_activate_license() {

    // listen for our activate button to be clicked
    if( isset( $_POST['wpc_pro_license_activate'] ) ) {

        // run a quick security check
        if( ! check_admin_referer( 'wpc_pro_nonce', 'wpc_pro_nonce' ) )
            return; // get out if we didn't click the Activate button

        // retrieve the license from the database
        $license = trim( get_option( 'wpc_pro_license_key' ) );

        // data to send in our API request
        $api_params = array(
                'edd_action'=> 'activate_license',
                'license' 	=> $license,
                'item_name' => urlencode( WPC_PRO_ITEM_NAME ), // the name of our product in EDD
                'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post( WPC_PRO_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

        // make sure the response came back okay
        if ( is_wp_error( $response ) )
            return false;

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        // $license_data->license will be either "valid" or "invalid"
        update_option( 'wpc_pro_license_status', $license_data->license );
    }
}
add_action('admin_init', 'wpc_pro_activate_license');

/***********************************************
* Illustrates how to deactivate a license key.
* This will descrease the site count
***********************************************/

function wpc_pro_deactivate_license() {

    // listen for our activate button to be clicked
    if( isset( $_POST['wpc_pro_license_deactivate'] ) ) {

        // run a quick security check
        if( ! check_admin_referer( 'wpc_pro_nonce', 'wpc_pro_nonce' ) )
            return; // get out if we didn't click the Activate button

        // retrieve the license from the database
        $license = trim( get_option( 'wpc_pro_license_key' ) );

        // data to send in our API request
        $api_params = array(
                'edd_action'=> 'deactivate_license',
                'license' 	=> $license,
                'item_name' => urlencode( WPC_PRO_ITEM_NAME ), // the name of our product in EDD
                'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post( WPC_PRO_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

        // make sure the response came back okay
        if ( is_wp_error( $response ) )
                return false;

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        // $license_data->license will be either "deactivated" or "failed"
        if( $license_data->license == 'deactivated' )
            delete_option( 'wpc_pro_license_status' );
    }
}
add_action('admin_init', 'wpc_pro_deactivate_license');


/************************************
* this illustrates how to check if
* a license key is still valid
* the updater does this for you,
* so this is only needed if you
* want to do something custom
*************************************/

function wpc_pro_check_license() {

    global $wp_version;

    $license = trim( get_option( 'wpc_pro_license_key' ) );

    $api_params = array(
            'edd_action' => 'check_license',
            'license' => $license,
            'item_name' => urlencode( WPC_PRO_ITEM_NAME ),
            'url'       => home_url()
    );

    // Call the custom API.
    $response = wp_remote_post( WPC_PRO_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

    if ( is_wp_error( $response ) )
        return false;

    $license_data = json_decode( wp_remote_retrieve_body( $response ) );

    if( $license_data->license == 'valid' ) {
        echo __('valid','wpc'); exit;
        // this license is still valid
    } else {
        echo __('invalid','wpc'); exit;
        // this license is no longer valid
    }
}
?>
<?php

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
    register_setting('baw-settings-group', 'wpc_show_tags');
    register_setting('baw-settings-group', 'wpc_image_width');
    register_setting('baw-settings-group', 'wpc_image_height');
    register_setting('baw-settings-group', 'wpc_thumb_width');
    register_setting('baw-settings-group', 'wpc_thumb_height');
    register_setting('baw-settings-group', 'wpc_next_prev');
    register_setting('baw-settings-group', 'wpc_vert_horiz');
    register_setting('baw-settings-group', 'wpc_inn_temp_head');
    register_setting('baw-settings-group', 'wpc_inn_temp_foot');
    register_setting('baw-settings-group', 'wpc_all_product_label');
    register_setting('baw-settings-group', 'wpc_accordion_setting');
    
    add_option('wpc_accordion_setting', 'yes', '', 'yes');
    add_option('wpc_vert_horiz', 'wpc_v', '', 'yes');
    add_option('wpc_show_tags', 'off', '', 'yes');
    
    global $wpdb;
    
    $sql = $wpdb->get_results("Select * From $wpdb->options Where option_name = 'wpc_pagination'");
    
    $wpc_option_value = '';
    foreach($sql as $row) {
        $wpc_option_value = $row->option_value;
    }
    if(($wpc_option_value == 0) || (empty($wpc_option_value))) {
        update_option('wpc_pagination', '8', '', 'yes');
    }
    
    register_setting('baw-settings-group', 'wpc_custom_fields');
}

function wp_catalogue_settings() {
    require 'settings.php';
}

function wpc_image_resize() {
    require 'image_resize.php';
}

require 'products/order.php';

// Redirect file templates
function wpc_template_chooser($wpc_template){
    global $wp_query;
    $wpc_plugindir = dirname(__FILE__);
	
    $wpc_post_type = get_query_var('post_type');
    
    if( $wpc_post_type == 'wpcproduct' ){
        return $wpc_plugindir . '/themefiles/single-wpcproduct.php';
    }
	
    if (is_tax('wpccategories')) {
        return $wpc_plugindir . '/themefiles/taxonomy-wpccategories.php';
    }
    
    if (is_tax('wpctags')) {
        return $wpc_plugindir . '/themefiles/taxonomy-wpctags.php';
    }
	
    return $wpc_template;   
}
add_filter('template_include', 'wpc_template_chooser');

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

/* ========================  Take User Defined color =========================== */
add_action('wp_head', 'wpc_head_css');
function wpc_head_css() {

    $wpc_image_width = get_option('wpc_image_width');
    $wpc_image_height = get_option('wpc_image_height');
    
    $wpc_thumb_width = get_option('wpc_thumb_width');
    $wpc_thumb_height = get_option('wpc_thumb_height');
?>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
<meta name="HandheldFriendly" content="true">
<style type="text/css">
    .wpc-img:hover {
        border: 5px solid <?php echo get_option('templateColorforProducts'); ?> !important;
    }
    .wpc-title {
        color: <?php echo get_option('templateColorforProducts'); ?> !important;
    }
    .wpc-title a:hover {
        color: <?php echo get_option('templateColorforProducts'); ?> !important;
    }
    #wpc-col-1 ul li a:hover, #wpc-col-1 ul li.active-wpc-cat a {
        border-right: none;
        background:<?php echo get_option('templateColorforProducts'); ?> no-repeat left top !important;
    }
    .wpc-paginations a:hover, .wpc-paginations .active-wpc-page {
        background: <?php echo get_option('templateColorforProducts'); ?> !important;
    }
    .checking{
        background-color:<?php echo get_option('templateColorforProducts'); ?> !important;
    }
    .wpc-post-meta-key {
        color: <?php echo get_option('templateColorforProducts'); ?> !important;
    }
    .navigation {
        list-style:none;
        font-size:12px;
    }
    .navigation li{
        display:inline;
    }
    .navigation li a{
        display:block;
        float:left;
        padding:4px 9px;
        margin-right:7px;
        border:1px solid #efefef;
    }
    .navigation li span.current {
        display:block;
        float:left;
        padding:4px 9px;
        margin-right:7px;
        border:1px solid #efefef;
        background-color:#f5f5f5;
    }   
    .navigation li span.dots {
        display:block;
        float:left;
        padding:4px 9px;
        margin-right:7px;
    }
    .current-cat > a{
        background-color: <?php echo get_option('templateColorforProducts'); ?> !important;
    }
    .wpc_page_link_disabled {
        background-color: <?php echo get_option('templateColorforProducts'); ?>;
    }
    #wpc-col-1 .wpc_sidebar_tags a {
        color: <?php echo get_option('templateColorforProducts'); ?>;
        text-decoration: none !important;
    }


    /*.wpc_product_content article.wpc_post {
        padding-top: 30px;
    }*/
    /*Carousel CSS*/
    .wpc_my_carousel {
        padding: 0 15px 0 0;
        display: inline-block;
        margin-bottom: 18px;
        width  : 100%;
    }
    .wpc_carousel ul {
        list-style: none;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        text-align: center;
        margin: 0 auto;
    }
    <?php
        $wpc_vert_horiz = get_option('wpc_vert_horiz');

            // For Horizental
        if($wpc_vert_horiz == 'wpc_h') {
            $wpc_thumb_height = get_option('wpc_thumb_height');
        if ($wpc_thumb_width >= 200) {
            ?>

            .wpc_h_carousel_wrap {
                position: relative;
                width: <?php echo ($wpc_thumb_width * 3) - 100 ; ?>px;
                /*text-align: center;
                margin: 0 auto;*/
            }

            <?php 
        }else {

    ?>


            .wpc_h_carousel_wrap {
                position: relative;
                width: <?php echo ($wpc_thumb_width * 3) + 15; ?>px;
               /* text-align: center;
                margin: 0 auto;*/
            }

            <?php 
        }
        ?>
            .layout_hort .wpc_hero_img img {
                width: <?php echo $wpc_image_width; ?>px;
                border: 4px solid #DCDBDB;
            }
            .layout_hort .wpc_hero_img {
                margin-bottom: 12px;
            }
            .layout_hort .wpc_carousel {
                overflow:hidden;
                position: relative;
                height: <?php echo $wpc_thumb_height + 14; ?>px;
                margin: 0 auto;
                z-index: 2;
            }
            .layout_hort .wpc_carousel ul {
                    margin: 0;
                }
            .layout_hort .wpc_carousel ul li {
                width: <?php echo $wpc_thumb_width + 10; ?>px;
                text-align: center;
                float: left;
            }
            .layout_hort ul li img {
                /*width: <?php echo $wpc_thumb_width; ?>px;*/
                border: 3px solid #DCDBDB;
                box-sizing: border-box;
            }
            .layout_hort .wpc_controls {
                position: absolute;
                left: 0;
                width: 100%;
                top: 50%;
                margin-top: -19px;
                bottom: 66px;
            }
            .layout_hort .prev-up {
                float: left;
                cursor: pointer;
                background: rgba(0, 0, 0, 0) url("<?php echo plugin_dir_url(__FILE__) ?>/includes/css/images/prev-arrow.png") no-repeat scroll center top;
                display: block;
                height: 30px;
                text-indent: -9000px;
                width: 30px;
            }
            .layout_hort .next-down {
                float: right;
                cursor: pointer;
                background: rgba(0, 0, 0, 0) url("<?php echo plugin_dir_url(__FILE__) ?>/includes/css/images/next-arrow.png") no-repeat scroll center top;
                display: block;
                height: 30px;
                text-indent: -9000px;
                width: 30px;
            }
            #wpc-catalogue-wrapper h4.wpc_catalogue_title {
                display:inline-block;
                vertical-align:top;
                text-align:left;
                
            }
            #wpc-catalogue-wrapper h4.wpc_catalogue_price {
                display:inline-block;
                vertical-align:top;
                float:none;
            }
            /* For Horizental Responsive */
            @media screen and (min-width: 768px) and (max-width: 1024px) {
                .layout_hort .wpc_hero_img {
                    height: auto !important;
                    margin-bottom: 12px;
                    width: 100% !important;
                }
                .layout_hort .wpc_hero_img img {
                    width: 466px;
                }

                <?php 

                    if($wpc_thumb_height >= 200) {
                        ?>
                    .layout_hort .wpc_carousel {
                    height: <?php echo ($wpc_thumb_height) - 50 ?>px;
                    margin-left: 24px;
                    overflow: hidden;
                    position: relative;
                    z-index: 2;
                    text-align: center;
                    margin: 0 auto;
                }
                <?php
                        }else {

                ?>


                .layout_hort .wpc_carousel {
                    height: <?php echo ($wpc_thumb_height); ?>px;
                    margin-left: 24px;
                    overflow: hidden;
                    position: relative;
                    z-index: 2;
                    text-align: center;
                    margin: 0 auto;
                }

                <?php 
                    }
                 ?>
                .layout_hort .wpc_controls {
                    bottom: 50px;
                    left: 0;
                    position: absolute;
                    width: 100%;
                    margin-top:-23px;
                }
                .layout_hort .wpc_carousel ul {
                    margin: 0;
                }
                .layout_hort .wpc_carousel ul li {
                    float: left;
                    height: 129px;
                    margin-right: 0;
                    text-align: center;
                    width: 176px;
                }
                .layout_hort ul li img {
                    height: auto !important;
                    width: 172px !important;
                }
                .wpc_my_carousel {
                    display: inline-block;
                    margin-bottom: 18px;
                    padding: 0;
                    position: relative;
                    width: 100%;
                }
                .wpc_h_carousel_wrap {
                    position: relative;
                    width: 455px;
                    text-align: center;
                }
                .layout_hort .prev-up {
                    margin-left: 0;
                }
                .layout_hort .next-down {
                    margin-right: 0;
                }
                .wpc_h_carousel_wrap {
                    margin:0 auto;
                }
            }
            @media screen and (min-width: 668px) and (max-width: 767px) {
                

                <?php

                if($wpc_thumb_height >= 200) {
                    ?>
                    .layout_hort .wpc_carousel {
                                        height: <?php echo $wpc_thumb_height - 30 ?>px;
                                        /*margin-left: 92px;*/
                                        overflow: hidden;
                                        position: relative;
                                        z-index: 2;
                                    }
                    <?php
                                    }

                                    else {
                    ?>

                    .layout_hort .wpc_carousel {
                                        height: <?php echo $wpc_thumb_height + 14; ?>px;
                                        /*margin-left: 92px;*/
                                        overflow: hidden;
                                        position: relative;
                                        z-index: 2;
                                    }
                    <?php

                }

                 ?>

                .wpc_carousel {
                    width: 410px !important;
                }
                .layout_hort .wpc_carousel ul li {
                    float: left;
                    height: 129px;
                    text-align: center;
                    width: 204px;
                }
                .layout_hort ul li img {
                    height: auto !important;
                    width: 200px !important;
                }
                .layout_hort .wpc_controls {
                    bottom: 50px;
                    /*left: 52px;*/
                    position: absolute;
                    width: 100%;
                }
                .wpc_h_carousel_wrap {
                    width: 505px;
                    text-align: center;
                    margin: 0 auto;
                }
            }
            @media screen and (min-width: 481px) and (max-width: 667px) {
                .wpc_h_carousel_wrap {
                    width: 540px;
                }
                .layout_hort .wpc_hero_img {
                    height: auto !important;
                    margin-bottom: 12px;
                    width: 100% !important;
                }
                .layout_hort .wpc_hero_img img {
                    height: auto;
                    width: 100%;
                }

                <?php 

                    if($wpc_thumb_height >= 200 ) {
                        ?>

                    .layout_hort .wpc_carousel {
                    height: <?php echo $wpc_thumb_height - 40; ?>px;
                    margin-left: 33px;
                    overflow: hidden;
                    position: relative;
                    z-index: 2;
                    width: 355px !important;
                }
                    <?php
            
            }
                else {

                ?>
                    .layout_hort .wpc_carousel {
                    height: <?php echo $wpc_thumb_height; ?>px;
                    margin-left: 33px;
                    overflow: hidden;
                    position: relative;
                    z-index: 2;
                    width: 355px !important;
                }

                <?php 
               
                    }
                ?>
                .layout_hort .wpc_controls {
                    bottom: 42px;
                    left: 0;
                    position: absolute;
                    width: 78%;
                }
                .layout_hort .wpc_carousel ul li {
                    float: left;
                    height: <?php echo $wpc_thumb_height + 14; ?>px;
                    text-align: center;
                    width: 177px;
                }
                .layout_hort ul li img {
                    height: auto !important;
                    width: 172px !important;
                }
                .wpc_my_carousel {
                    padding: 0;
                    width: 419px;
                }
                .layout_hort .prev-up {
                    margin-left: 0;
                }
                .layout_hort .next-down {
                    margin-right: 0;
                }
                .wpc_my_carousel {
                    height:490px;
                }
            }
            @media screen and (min-width: 320px) and (max-width: 480px) {

                .wpc_h_carousel_wrap {
                    width: 540px;
                }
                .layout_hort .wpc_hero_img {
                    height: auto !important;
                    margin-bottom: 12px;
                    width: 100% !important;
                }
                
                .layout_hort .wpc_hero_img img {
                    height: auto;
                    width: 100%;
                }
                .layout_hort .wpc_carousel {
                    height: 78px;
                    margin-left: 35px;
                    overflow: hidden;
                    position: relative;
                    z-index: 2;
                }
                .layout_hort .wpc_controls {
                    bottom: 16px;
                    left: 0;
                    position: absolute;
                    width: 50%;
                    margin-top: -16px;
                }
                .layout_hort .wpc_carousel ul li {
                    float: left;
                    height: 63px;
                    text-align: center;
                    width: 99px;
                }
                .layout_hort ul li img {
                    height: auto !important;
                    width: 96px !important;
                }
                .wpc_my_carousel {
                    padding: 0;
                    width: 270px;
                }
                .layout_hort .prev-up {
                    margin-left: 0;
                }
                .layout_hort .next-down {
                    margin-right: 0;
                }
                .product-price {
                    float:none;
                }
            }
    <?php
        // For Vertical
        } elseif($wpc_vert_horiz == 'wpc_v') {
    ?>
            .wpc_my_carousel {
                padding: 0 15px 0 0;
                position: relative;
                display: block;
               /* height: <?php echo $wpc_image_height + 8; ?>px;*/
               height: 408px;
            }
            .wpc_carousel ul li img {
                height: 92px;
                border: 2px solid #DCDBDB;
            }
            .layout_vert .wpc_hero_img {
                float: left;
                margin-right: 20px;
                width: <?php echo $wpc_image_width + 8; ?>px;
            }
            .layout_vert .wpc_hero_img img {
                border: 4px solid #DCDBDB;
            }
            .wpc_v_carousel_wrap {
                float: right;
                padding:0 0 0 10px;
                position: relative;
            }
            .layout_vert .wpc_carousel {
                overflow:hidden;
                position: relative;
                width: 158px;
                margin-top: 35px;
                float: left;
                z-index: 2;
            }
            .layout_vert .wpc_carousel ul li {
                text-align: center;
                float: none;
                padding-bottom: 1px;
            }
            .layout_vert .wpc_controls {
                position: absolute;
                right: 38%;
                height: 350px;
                width: 30px;
            }
            .layout_vert .prev-up {
                cursor: pointer;
                background: rgba(0, 0, 0, 0) url("<?php echo plugin_dir_url(__FILE__) ?>/includes/css/images/up-arrow.png") no-repeat scroll center top;
                display: block;
                height: 30px;
                text-indent: -9000px;
                width: 30px;
                position: absolute;
                right:0;
                top: 0;
            }
            .layout_vert .next-down {
                float: none;
                cursor: pointer;
                background: rgba(0, 0, 0, 0) url("<?php echo plugin_dir_url(__FILE__) ?>/includes/css/images/down-arrow.png") no-repeat scroll center top;
                display: block;
                height: 30px;
                text-indent: -9000px;
                width: 30px;
                right:0;
                position: absolute;
                bottom: 0;
            }
            /* For Vertical Responsive */
            @media screen and (min-width: 769px) and (max-width: 1024px) {
                #wpc-col-2 {
                    margin: 0 auto;
                    overflow: hidden;
                    width: 722px;
                }
                .layout_vert .wpc_carousel {
                    float: none;
                    margin: 38px auto 0;
                    overflow: hidden;
                    position: relative;
                    width: 152px;
                }
                .layout_vert .wpc_controls {
                    bottom: -35px;
                    height: 348px;
                    left: 50%;
                    margin-left: -12px;
                    position: absolute;
                    right: inherit;
                    width: 30px;
                }
                .layout_vert .wpc_hero_img {
                    float: left;
                    width: 490px;
                }
                .wpc_v_carousel_wrap {
                    float: right;
                    height: auto;
                    position: relative;
                    top: 0;
                    width: 25%;
                    padding:0 0 0 10px;

                }
                .product-price {
                    margin-right: 25px;
                }
                #wpc-catalogue-wrapper #wpc-col-2 #wpc_my_carousel article.wpc_post {
                    width: 98%;
                }
            }
            @media screen and (min-width: 668px) and (max-width: 768px) {
                #wpc_my_carousel {
                    height: 350px;
                }
                .layout_vert .wpc_hero_img {
                    float: none;
                }
                .layout_vert .wpc_carousel {
                    float: none;
                    margin: 38px auto 0;
                    overflow: hidden;
                    position: relative;
                    width: 152px;
                }
                .layout_vert .wpc_controls {
                    bottom: -54px;
                    height: 350px;
                    left: 55%;
                    margin-left: -18px;
                    position: absolute;
                    right: inherit;
                    width: 30px;
                }
                .layout_vert .wpc_hero_img {
                    float: left;
                    width:70%;
                }
                .wpc_v_carousel_wrap {
                    float: right;
                    height: 85%;
                    position: relative;
                    top: 0;
                    width: auto;
                }
                .layout_vert .wpc_hero_img img {
                    width:100%;
                    display:block;
                }
                #wpc-col-2 {
                    text-align: left;
                }
                .product-price {
                    margin-right: 25px;
                }
                #wpc-catalogue-wrapper #wpc-col-2 #wpc_my_carousel article.wpc_post {
                    width: 98%;
                }
                .wpc_product_content {

                    padding-left: 20px;
                }
            }
            @media screen and (min-width: 481px) and (max-width: 667px) {
                .layout_vert .wpc_hero_img {
                    float: none;
                    width: 100% !important;
                    height: auto !important;
                }
                .layout_vert .wpc_hero_img img {
                    width: 100%;
                    height: auto;
                }
                .wpc_product_content {
                    width:65%;
                    padding:20px 0;
                    font-size:13px;
                    padding-left: 20px;

                }
                #wpc-catalogue-wrapper h4.wpc_catalogue_price {
                    float:none;
                    width:auto;
                    display:block;
                }
                .layout_vert .wpc_carousel {
                    float: none;
                    margin: 38px auto 0;
                    overflow: hidden;
                    position: relative;
                    width: 152px;
                }
                .layout_vert .wpc_controls {
                    bottom: -21px;
                    height: 350px;
                    left: 60%;
                    margin-left:-56px;
                    position: absolute;
                    right: inherit;
                    width: 30px;
                }
                .wpc_v_carousel_wrap {
                    position: relative;
                    top: 12px;
                    width: auto;
                    margin: 0 auto;
                    padding:0 0 0 0;
                }
                #wpc-col-2 {
                    text-align: left;
                }
                .product-price {
                    margin-right: 25px;
                    float:none;
                }
                #wpc-catalogue-wrapper #wpc-col-2 #wpc_my_carousel article.wpc_post {
                    width: 95%;
                }
                .layout_vert .prev-up {
                    right:-27px;
                    top:10px;
                }
                .layout_vert .next-down {
                    right:-27px;
                    bottom:-18px;
                }
                #wpc-catalogue-wrapper h4.wpc_catalogue_title {
                    margin:0 0 10px;
                    
                }
                .layout_vert .wpc_carousel {
                float:right;
                }
            }
            @media screen and (min-width: 320px) and (max-width: 480px) {

                .wpc_product_content {
                   margin-top: 20px;
                    
                }

                .layout_vert .wpc_hero_img {
                    float: none;
                    width: 100% !important;
                    height: auto !important;
                    margin:0 0 43px;
                }
                .layout_vert .wpc_hero_img img {
                    width: 100%;
                    height: auto;
                }
                .wpc_my_carousel {
                    margin-bottom: 110px;
                }
                .layout_vert .wpc_carousel {
                    float: none;
                    margin:0 auto;
                    overflow: hidden;
                    position: relative;
                    width:200px;
                }
                .layout_vert .wpc_controls {
                    bottom: -35px;
                    height: 350px;
                    left: 60%;
                    margin-left: -38px;
                    position: absolute;
                    right: inherit;
                    width: 30px;
                }
                .wpc_v_carousel_wrap {
                    height: auto;
                    position: relative;
                    top: 12px;
                    margin: 0 auto 50px;
                    float: none;
                    padding: 0 0 0 0;
                }
                #wpc-col-2 {
                    text-align: left;
                }
                #wpc-catalogue-wrapper h4.wpc_catalogue_price {
                    float:none;
                }
                .product-price {
                    margin-bottom: 8px;
                    margin-left: 25px;
                    margin-top: 12px;
                    width: 100%;
                }
                #wpc-catalogue-wrapper #wpc-col-2 #wpc_my_carousel article.wpc_post {
                    width: 95%;
                }
                #wpc-catalogue-wrapper h4.wpc_catalogue_title {
                    margin:0 0 10px;
                    
                }
            }
                .product-price {
                    float:none;
                    display:inline-block;
                    vertical-align:top;
                }
                .wpc_catalogue_title {
                    display:inline-block;
                    vertical-align:top;
                }
    <?php
        }
    ?>
    </style>
<?php
}

/* ========================  Enqueue jQuery in head =========================== */

function wpc_jQuery_head() {
?>
    <script type="text/javascript">
    <?php
    if (get_option('wpc_accordion_setting') == yes) {
    ?>
        jQuery(document).ready(function () {
            // baking cookie ;p
            function set_cookie(ID) {
                document.cookie = ID + "=opened; path=<?php echo COOKIEPATH ?>";
            }

            // getting it out from the oven... 
            function get_cookies_array() {
                var cookies = {};

                if (document.cookie && document.cookie != '') {
                    var split = document.cookie.split(';');
                    for (var i = 0; i < split.length; i++) {
                        var name_value = split[i].split("=");
                        name_value[0] = name_value[0].replace(/^ /, '');
                        cookies[decodeURIComponent(name_value[0])] = decodeURIComponent(name_value[1]);
                    }
                }
                return cookies;
            }

            // yuck... sorry i don't know how to cook :S
            function unset_cookie(cookie_name) {
                var cookie_date = new Date();
                cookie_date.setTime(cookie_date.getTime() - 1);
                document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString() + "; path=<?php echo COOKIEPATH ?>";
            }

            var tree_id = 0;
            jQuery('ul.wpc-categories li:has(ul)').addClass('has-child').prepend('<span class="switch"><img src="<?php echo plugin_dir_url(__FILE__); ?>/includes/css/images/icon-plus.png" /></span>').each(function () {
                tree_id++;
                jQuery(this).attr('id', 'tree' + tree_id);
            });

            jQuery('ul.wpc-categories li > span.switch').click(function () {
                var tree_id = jQuery(this).parent().attr('id');
                if (jQuery(this).hasClass('open')) {
                    jQuery(this).parent().find('ul:first').slideUp('fast');
                    jQuery(this).removeClass('open');
                    jQuery(this).html('<img src="<?php echo plugin_dir_url(__FILE__); ?>/includes/css/images/icon-plus.png" />');
                    unset_cookie(tree_id)
                } else {
                    jQuery(this).parent().find('ul:first').slideDown('fast');
                    jQuery(this).html('<img src="<?php echo plugin_dir_url(__FILE__); ?>/includes/css/images/icon-minus.png" />');
                    jQuery(this).addClass('open');
                    set_cookie(tree_id)
                }
            });
			
            var cookies = get_cookies_array();
            for (var name in cookies) {
                jQuery('#' + name).find('> ul').css({'display' : 'block'});
                jQuery('#' + name).find('> span').addClass('open').html('<img src="<?php echo plugin_dir_url(__FILE__); ?>/includes/css/images/icon-minus.png" />');
            }
        });
    <?php
    }
    ?>
		
        // wpc Carousel
        jQuery(function () {
            jQuery('#wpc_my_carousel').b29_carousel({
            <?php
                $wpc_vert_horiz = get_option('wpc_vert_horiz');
                
                if($wpc_vert_horiz == 'wpc_h') {
            ?>
                    layout: 'hort',
                    visible_items: 2,
            <?php
                } elseif($wpc_vert_horiz == 'wpc_v') {
            ?>
                    layout: 'vert',
                    visible_items: 3,
            <?php
                }
            ?>
            });
        });
    </script>
<?php
}
add_action('wp_head', 'wpc_jQuery_head');

/* ========================  Support Specific Custom Field =========================== */

function wpc_the_meta() {
    $custom_field_keys = get_post_custom_keys();

    $wpc_custom_fields = get_option("wpc_custom_fields");

    if ($wpc_custom_fields == "yes" && $wpc_custom_fields != "") {
        echo '<ul class="wpc-the-meta">';
        foreach ($custom_field_keys as $key => $value) {
            $valuet = trim($value);
            if ('_' == $valuet{0} || 'product_images' == $valuet || 'is_featured' == $valuet || 'wpc_product_price' == $valuet || 'jfs_subtitle' == $valuet || 'wpc_big_images' == $valuet || 'wpc_thumb_images' == $valuet) {
                continue;
            }
            $values = array_map('trim', get_post_custom_values($valuet));
            $key_value = implode($values, ', ');
            echo '<li><span class="wpc-post-meta-key">' . $value . " </span> " . $key_value . "</li>";
        }
        echo "</ul>\n";
    }
}

function wpc_responsive_menu() {
?>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery('.wpc-visible-phone').click(function(e) {
                jQuery('.wpc-categories').slideToggle('fast');
		return (false);
            });
        })
    </script>
<?php
}
add_action('wp_head', 'wpc_responsive_menu');
?>