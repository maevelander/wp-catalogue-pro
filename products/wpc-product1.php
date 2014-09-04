<?php

//////// Advance custom post type

function wpt_wpcproduct_posttype() {
	
    register_post_type( 'wpcproduct',
		 array(
			'labels' => array(
                'name' => __( 'WP Catalogue +' ),
                'singular_name' => __( 'WP Catalogue' ),
                'add_new' => __( 'Add New Product' ),
                'add_new_item' => __( 'Add New Product' ),
                'edit_item' => __( 'Edit Product' ),
                'new_item' => __( 'Add New Product' ),
                'view_item' => __( 'View Product' ),
                'search_items' => __( 'Search WPC Product' ),
                'not_found' => __( 'No Product found' ),
                'not_found_in_trash' => __( 'No Product found in trash' )
            ),
            'public' => true,
			'menu_icon' => WP_CATALOGUE.'/images/shopping-basket.png',  // Icon Path
            'supports' => array( 'title','editor',"custom-fields"),
            'capability_type' => 'post',
            'rewrite' => array("slug" => "wpcproduct"), // Permalinks format
            'menu_position' => 121,
            'register_meta_box_cb' => 'add_wpcproduct_metaboxes',
        )
    );
}

add_action( 'init', 'wpt_wpcproduct_posttype' );
add_action( 'add_meta_boxes', 'add_wpcproduct_metaboxes' );

function add_wpcproduct_metaboxes() {
	add_meta_box('wpt_product_featured', 'Featured Product', 'wpt_product_featured', 'wpcproduct', 'side');
	
}

function wpt_product_featured() {
    global $post;
	
	$featured = get_post_meta($post->ID, 'is_featured', true);
	
echo '<p>Mark this product as Featured? &nbsp;&nbsp;&nbsp;&nbsp;<label>
    <input name="is_featured" type="checkbox" value="is_featured"';
	if($featured == "is_featured"){
		echo 'checked="checked"';
	}
	 echo '/>
    </label> &nbsp;&nbsp;&nbsp;
	 </p>';
	
}

function wpt_product_imgs() {
    global $post;
 // Noncename needed to verify where the data originated
    echo '<input type="hidden" name="itemmeta_noncename" id="itemmeta_noncename" value="' .
  wp_create_nonce( plugin_basename(__FILE__) ) . '" />';


	
}


// Save the Metabox Data
function wpt_save_wpcproduct_meta($post_id, $post) {

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !wp_verify_nonce( $_POST['itemmeta_noncename'], plugin_basename(__FILE__) )) {
    return $post->ID;
    }
    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;
    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
	$item_meta['is_featured'] = $_POST['is_featured'];
    // Add values of $events_meta as custom fields
    foreach ($item_meta as $key => $value) { // Cycle through the $events_meta array!
        if( $post->post_type == 'revision' ) return; // Don't store custom data twice
        $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, $key, $value);
        }
        if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }
 }

add_action('save_post', 'wpt_save_wpcproduct_meta', 1, 2); // save the custom fields
add_action('init','create_wpcproduct_taxonomies',0);
function create_wpcproduct_taxonomies(){


$labels = array( 
    'name' => _x( 'Categories', 'taxonomy general name' ),
    'singular_name' => _x( 'Categories', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Categories' ),
    'all_items' => __( 'All Categories' ),
    'parent_item' => __( 'Parent Categories' ),
    'parent_item_colon' => __( 'Parent Categories:' ),
    'edit_item' => __( 'Edit Categories' ), 
    'update_item' => __( 'Update Categories' ),
    'add_new_item' => __( 'Add New Categories' ),
    'new_item_name' => __( 'New Categories Name' ),
    'menu_name' => __( 'Categories' ),
 ); 	
  register_taxonomy('wpccategories',array('wpcproduct'), array(
	'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'wpccategories', 'with_front' => false ),
  ));
} 

add_filter( 'manage_edit-wpcproduct_columns', 'my_edit_wpcproduct_columns' ) ;
function my_edit_wpcproduct_columns( $columns ) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Title' ),
		'wpccategories' => __( '<a href="javascript:;">Category</a>' ),
		'date' => __( 'Date' )
	);
	return $columns;
}



add_action( 'manage_wpcproduct_posts_custom_column', 'my_manage_wpcproduct_columns', 10, 2 );
function my_manage_wpcproduct_columns( $column, $post_id ) {
	global $post;

	switch( $column ) {
			/* If displaying the 'genre' column. */
		case  'wpccategories':
			/* Get the genres for the post. */
			$terms = get_the_terms( $post_id, 'wpccategories');
			/* If terms were found. */
			if ( !empty( $terms ) ) {
				$out = array();
				/* Loop through each term, linking to the 'edit posts' page for the specific term. */
				foreach ( $terms as $term ) {
					$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'wpccategories' => $term->slug ), 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'wpccategories', 'display' ) )
					);
				}
				/* Join the terms, separating them with a comma. */
				echo join( ', ', $out );
			}
			/* If no terms were found, output a default message. */
			else {
				_e( 'No Category' );
			}
			break;
		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}

function dev_check_current_screen() {
   global $current_screen;
     if($current_screen->post_type=='wpcproduct'){
		echo '<style type="text/css">
			#wp-content-media-buttons{
				display:none;	
			}
		</style>';	
	}
}

// Multiple Images For Products
	
function product_images($post){
	add_meta_box('download_link_id','Images','multiple_product_images','wpcproduct','normal','high');
}
add_action('add_meta_boxes','product_images');

function multiple_product_images($post){
	$product_images = get_post_meta($post->ID, 'product_images', true);
?>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			
			$('#add-row').on('click', function() {
				var row = $('.empty-row.screen-reader-text').clone(true);
				row.removeClass('empty-row screen-reader-text');
				row.insertAfter('#repeat_div table:last');
				return false;
			});
			
			$('.remove-row').on('click', function() {
				$(this).parents('table').remove();
				return false;
			});
		});
	</script>
    
    <div id="repeat_div">
	<?php
        if(!empty($product_images )) :
            foreach($product_images as $field ) {
    ?>
        <table width="100%" border="0" style="border-bottom:1px solid #CCC; padding-bottom:4px; margin-bottom:8px;">
            <tr>
                <td><strong>Image:</strong></td>
                <td>
				<p><input id="Image1" class="upload-url" type="text" name="product_img[]" value="<?php if ($field['product_img'] != '') echo esc_attr( $field['product_img'] ); else echo ''; ?>"><input id="st_upload_button1" class="st_upload_button" type="button" name="upload_button" value="Upload"></p>
				
                </td>
            </tr>
            
            <tr>
                <td>&nbsp;</td>
                <td align="right">
                    <a class="button remove-row" href="#">Remove</a>
                </td>
            </tr>
        </table>
    <?php
            }
        else :
        // show a blank one
    ?>
        <table id="repeatable-fieldset-one" width="100%" border="0" style="border-bottom:1px solid #CCC; padding-bottom:4px; margin-bottom:8px;">
			<tr>
                <td><strong>Image:</strong></td>
                <td>
                   <p><input id="Image1" class="upload-url" type="text" name="product_img[]" value=""><input id="st_upload_button1" class="st_upload_button" type="button" name="upload_button" value="Upload"></p>
				
                </td>
            </tr>
        </table>
    <?php endif; ?>
        <table width="100%" border="0" style="border-bottom:1px solid #CCC; padding-bottom:4px; margin-bottom:8px;" class="empty-row screen-reader-text">
            <tr>
                <td><strong>Image:</strong></td>
                <td>
                    <p><input id="Image1" class="upload-url" type="text" name="product_img[]" value=""><input id="st_upload_button1" class="st_upload_button" type="button" name="upload_button" value="Upload"></p>
				
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td align="right">
                    <a class="button remove-row" href="#">Remove</a>
                </td>
            </tr>
        </table>
    </div>
	<p><a id="add-row" class="button" href="#">Add Another Image</a></p>
<?php
}

add_action('save_post', 'save_multiple_images');
function save_multiple_images() {
	global $post;

	$new = array();
	$product_img = $_POST['product_img'];
	
	$count = count( $product_img );
	
	for($i = 0; $i < $count; $i++ ) {
		if(($product_img[$i] != '')){
			
			$new[$i]['product_img'] = stripslashes( $product_img[$i] ); 
			// and however you want to sanitize
		}
	}
 
	if(!empty($new))
		update_post_meta($post->ID, 'product_images', $new);
}

