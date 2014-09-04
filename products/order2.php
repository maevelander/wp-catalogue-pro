<?php

if(get_option('custom_tax')){
		$custom_tax		=	get_option('custom_tax');
	}else{
		$custom_tax		=	'wpccategories';
	}
	global $custom_tax;

$customtaxorder_defaults = array($custom_tax => 0);

$args = array( 'public' => true, '_builtin' => false ); 

$output = 'objects';



$customtaxorder_defaults = apply_filters('customtaxorder_defaults', $customtaxorder_defaults);

$customtaxorder_settings = get_option('customtaxorder_settings');

$customtaxorder_settings = wp_parse_args($customtaxorder_settings, $customtaxorder_defaults);

add_action('admin_init', 'customtaxorder_register_settings');

function customtaxorder_register_settings() {

	register_setting('customtaxorder_settings', 'customtaxorder_settings', 'customtaxorder_settings_validate');

}

function customtaxorder_update_settings() {

	global $customtaxorder_settings, $customtaxorder_defaults;

	if ( isset($customtaxorder_settings['update']) ) {

		echo '<div class="updated fade" id="message"><p>'. __('Custom Taxonomy Order settings ','wpc').$customtaxorder_settings['update'].'.</p></div>';

		unset($customtaxorder_settings['update']);

		update_option('customtaxorder_settings', $customtaxorder_settings);

	}

}

function customtaxorder_settings_validate($input) {
	global $custom_tax;
	$input[$custom_tax] = ($input[$custom_tax] == 1 ? 1 : 0);

	$args = array( 'public' => true, '_builtin' => false );

	$output = 'objects';

	return $input;

}







/**/







function customtaxorder() {

	global $customtaxorder_settings;
	global $custom_tax;
	customtaxorder_update_settings();

	$options = $customtaxorder_settings;

	$settings = '';

	$parent_ID = 0;

	if ( $_GET['page'] == 'customtaxorder' ) { 

		$args = array( 'public' => true, '_builtin' => false ); 

		$output = 'objects';

		

		

	

		$tax_label = 'Catalogue Categories';

		$tax = $custom_tax;

	} 

	

	

	

	$message = "";

	if (isset($_POST['order-submit'])) { 

		customtaxorder_update_order();

	}

?>
<div class="wrap">
  <div id="icon-options-general" class="icon32"><br>
  </div>
  <h2>Order</h2>
  <div class="wpc-left-liquid">
    <div class="wpc-left">
      <div class="wpc-headings">
        <h3>Order Categories</h3>
      </div>
      <div class="wpc-inner">
        <p class="description"><?php _e('Drag and drop items to customise the order of categories in WP Catalogue','wpc') ?> </p>
        <form name="custom-order-form" method="post" action="">
          <?php  

		$args = array(

			'orderby' => 'term_order',

			'order' => 'ASC',

			'hide_empty' => false,

			'parent' => $parent_ID, 

		);
		
		$terms = get_terms( $tax, $args );

			if ( $terms ) {

		?>
          <ul id="orderly-sortable" class="orderly-items">
            <?php foreach ( $terms as $term ) : ?>
            <li id="id_<?php echo $term->term_id; ?>" class="lineitem <?php echo ($i % 2 == 0 ? 'alternate ' : ''); ?>ui-state-default"><?php echo $term->name." _ ".$term->term_id; ?>
            <?php
            	$term_id = $term->term_id;
				
				$child_terms = get_term_children( $term_id, $tax );
			?>
            	<ul id="orderly-sortable" class="child-orderly-items">
                
                <?php foreach ( $child_terms as $child_term ) : ?>
                	<?php $child = get_term_by( 'id', $child_term, $tax ); ?>
                	<li id="child_id_<?php echo $child->term_id; ?>" class="lineitem <?php echo ($i % 2 == 0 ? 'alternate ' : ''); ?>ui-state-default"><?php echo $child->name." _ ".$child->term_id; ?></li>
               	<?php endforeach; ?>
                
                </ul>
            </li>
            <?php endforeach; ?>
          </ul>
          <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" id="custom-loading" style="display:none" alt="" />
          <input type="submit" name="order-submit" id="order-submit" class="button-primary" value="<?php _e('Save Order', 'wpc') ?>" />
          <div class="clear"></div>
          <input type="hidden" id="hidden-custom-order" name="hidden-custom-order" />
          <input type="text" id="hidden-custom-child-order" name="hidden-custom-child-order" />
          <input type="hidden" id="hidden-parent-id" name="hidden-parent-id" value="<?php echo $parent_ID; ?>" />
          <?php } else { ?>
          <p>
            <?php _e('No terms found', 'wpc'); ?>
          </p>
          <?php } ?>
        </form>
      </div>
      <br class="clear">
      <?php if ( $terms ) { function image_sort(){  
wp_enqueue_script('jquery');
?>
<!--<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>-->
<?php 
wp_enqueue_script('jquery-ui-sortable');
?>
<script type="text/javascript">

jQuery(document).ready( function(e) {
   jQuery('#image_sort').sortable({
	   		items: '.list_item',
			opacity: 0.5,
			cursor: 'pointer',
			axis: 'y',
			update: function() {
				var ordr = jQuery(this).sortable('serialize') + '&action=list_update_order';
				jQuery.post(ajaxurl, ordr, function(response){
					//alert(response);
				});
			}
	  });
});

</script>
<?php
    }
	add_action('admin_head','image_sort');
	
	function order_list(){
		global $wp_logo_slider_images;
		
		$list = $wp_logo_slider_images;
		$new_order = $_POST['list_item'];
		$new_list = array();
		
		foreach($new_order as $v){
			if(isset($list[$v])){
				$new_list[$v] = $list[$v];
			}
		}
		update_option('wp_logo_slider_images',$new_list);
	}
	add_action('wp_ajax_list_update_order','order_list'); }
?>

<?php

$post_type = trim($_REQUEST['post_type']);

if (empty($post_type)) $post_type = 'post';

$post_type_object = get_post_type_object($post_type);


if ($_SERVER['REQUEST_METHOD'] == 'POST')

{

    $message = "Custom order saved for <em>{$post_type_object->labels->name}</em>";

    $values = (array)$_POST['orderly_values'];

    if (!empty($values))

    {

        global $wpdb;



        for ($i = 0; $i < count($values); $i++)

        {

            $post_id = (int)$values[$i];

            $sql = $wpdb->prepare(

                "UPDATE `{$wpdb->posts}` SET `menu_order` = %d WHERE ID = %d",

                $i,

                $post_id

            );

            $wpdb->query($sql);

        }

    }

}



$loop = new WP_Query(array(

    'post_type' => $post_type,

    'order'     => 'ASC',

    'orderby'   => 'menu_order',

    'nopaging'  => true,

));



?>
      <?php

    if (!empty($message)):

    ?>
      <div class="updated">
        <p> <strong>
          <?php _e($message, ORDERLY_DOMAIN); ?>
          </strong> </p>
      </div>
      <?php

    endif;

    ?>
    
    <!--Child Catagories-->
    <?php /*?><?php
    	if($terms){
    ?>
        <div class="wpc-headings">
            <h3>Order Sub-Categories</h3>
        </div>
        <div class="wpc-inner">
            <p class="description"><?php _e('Drag and drop items to customise the order of sub categories in WP Catalogue','wpc') ?> </p>
            <form name="custom-order-form" method="post" action="">
            <div style="wpc-child-div">
            <?php
                $args_parent = array(
                                'orderby' => 'term_order',
                                'order' => 'ASC',
                                'hide_empty' => false,
                                'parent' => $parent_ID, 
                            );
            
                $terms_parent = get_terms( $tax, $args_parent );
                
                foreach ( $terms_parent as $term_parent ) :
            ?>
                <div class="wpc-parent-name">
				<?php echo $term_parent->name; ?>
                <?php
                    $parent_id = $term_parent->term_id;
                    
                    $child_term = get_term_children( $parent_id, $tax );
                ?>
                    <ul id="child-orderly-sortable" class="child-orderly-items">
                    
                    <?php foreach ( $child_term as $child_cat ) : ?>
                        <?php $get_child = get_term_by( 'id', $child_cat, $tax ); ?>
                        <li id="child_id_<?php echo $get_child->term_id; ?>" class="lineitem <?php echo ($i % 2 == 0 ? 'alternate ' : ''); ?>ui-state-default"><?php echo $get_child->name." _ ".$get_child->term_id; ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            <?php
                endforeach;
            ?>
            <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" id="custom-loading" style="display:none" alt="" />
            <input type="submit" name="child-order-submit" id="child-order-submit" class="button-primary" value="<?php _e('Save Order', 'wpc') ?>" />
            <div class="clear"></div>
            <input type="hidden" id="hidden-custom-child-order" name="hidden-custom-child-order" />
            </div>
            </form>
        </div>
  	<?php
		}
	?>
    <br class="clear">
  	<?php if($terms){ ?>
		<script type="text/javascript">
    
			// <![CDATA[
			jQuery(document).ready(function($) {
				$("#custom-loading").hide();		
				$("#child-order-submit").click(function() {
					orderChildSubmit();
				});
			});
			
			function customChildTaxOrderAddLoadEvent(){
				jQuery("#child-orderly-sortable").sortable({		
					placeholder: "sortable-placeholder",		
					revert: false,		
					tolerance: "pointer"		
				});
			};
			addLoadEvent(customChildTaxOrderAddLoadEvent);
			
			function orderChildSubmit() {
				var newChildOrder = jQuery("#child-orderly-sortable").sortable("toArray");
				//alert(newChildOrder);
				jQuery("#custom-loading").show();
				jQuery("#hidden-custom-child-order").val(newChildOrder);
				return true;
			}
	
			// ]]>
		
		</script>
  	<?php } ?><?php */?>
    
      <?php if ($loop->have_posts()): ?>
      <div class="wpc-headings">
        <h3>Order Products</h3>
      </div>
      <div class="wpc-inner">
        <p class="description"><?php _e('Drag and drop items to customise the order of products in WP Catalogue','wpc') ?> </p>
        <form name="orderly-order-form" method="post" action="">
          <ul class="orderly-items orderly-sortable">
            <?php

        $i = 1;

        while ($loop->have_posts()) : $loop->the_post(); ?>
            <li id="orderly-item-<?php echo the_ID(); ?>" class="<?php echo ($i % 2 == 0 ? 'alternate ' : ''); ?>ui-state-default"> <span class="orderly-index"><?php echo $i; ?>.</span> <?php echo the_title(); ?>
              <input type="hidden" value="<?php echo the_ID(); ?>" name="orderly_values[]" id="orderly_values_<?php echo $i; ?>"/>
            </li>
            <?php

            $i++;

        endwhile;

        ?>
          </ul>
          <p>
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e("Save Order", ORDERLY_DOMAIN); ?>"/>
          </p>
        </form>
        <?php else: ?>
        <p>
          <?php $label = strtolower($post_type_object->labels->name); ?>
          <?php _e("There doesn't seem to be any {$label} yet. Click below to add one.", ORDERLY_DOMAIN); ?>
        </p>
        <p> <a href="<?php echo admin_url("post-new.php?post_type={$post_type}"); ?>" class="button-primary">
          <?php _e("Add {$post_type_object->labels->singular_name}", ORDERLY_DOMAIN); ?>
          </a> </p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="wpc-right-liquid">
      <table cellpadding="0" class="widefat" style="margin-bottom:10px;" width="50%">
          <thead>
          <th scope="col"><strong style="color:#008001;"><?php _e('How to use this plugin','wpc') ?></strong></th>
            </thead>
          <tbody>
            <tr>
              <td style="border:0;"><?php _e('You can use 3 shortcodes','wpc') ?></td>
            </tr>
            <tr>
              <td style="border:0;">
             <b>1. [wp-catalogue]  </b><?php _e('to display complete catalogue','wpc') ?>
              </td>
            </tr>
            <tr>
              <td style="border:0;"><b>2. [wp-catalogue featured="true"]</b>  <?php _e('to display featured products anywhere on your blog.','wpc') ?> </td>
            </tr>
            <tr>
              <td style="border:0;"><b>3. [wp-catalogue wpcat="wpc-category-slug"] </b> <?php _e('to display products from specific category.','wpc') ?> </td>
            </tr>
          </tbody>
    </table>
    <table cellpadding="0" class="widefat donation" style="margin-bottom:10px; border:solid 2px #008001;" width="50%">
      <thead>
      <th scope="col"><strong style="color:#008001;"><?php _e('Help Improve This Plugin!','wpc'); ?></strong></th>
          </thead>
      <tbody>
        <tr>
          <td style="border:0;"><?php _e('Enjoyed this plugin? All donations are used to improve and further develop this plugin. Thanks for your contributaion.','wpc') ?></td>
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
          <td style="border:0;"><?php _e('you can also help by','wpc') ?> <a href="http://wordpress.org/plugins/wp-catalogue/"><?php _e('rating this plugin on wordpress.org','wpc')?></a></td>
        </tr>
      </tbody>
    </table>
    <table cellpadding="0" class="widefat" border="0">
      <thead>
      <th scope="col">Need Support</th>
          </thead>
      <tbody>
        <tr>
          <td style="border:0;"><?php _e('If you are having problems with plugin please talk about them on ','wpc') ?><a href="http://wordpress.org/support/plugin/wp-catalogue">Support Forums</a></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<?php

}

function customtaxorder_update_order() {

	if (isset($_POST['hidden-custom-order']) && $_POST['hidden-custom-order'] != "") { 

		global $wpdb;

		$new_order = $_POST['hidden-custom-order'];

		$IDs = explode(",", $new_order);

		$result = count($IDs);

		for($i = 0; $i < $result; $i++) {

			$str = str_replace("id_", "", $IDs[$i]);
			
			$wpdb->query("UPDATE $wpdb->terms SET term_order = '$i' WHERE term_id ='$str'");

		}

		echo '<div id="message" class="updated fade"><p>'. __('Order updated successfully.', 'wpc').'</p></div>';

	} else {

		echo '<div id="message" class="error fade"><p>'. __('An error occured, order has not been saved.', 'wpc').'</p></div>';

	}
	
	
	if (isset($_POST['hidden-custom-child-order']) && $_POST['hidden-custom-child-order'] != "") { 

		global $wpdb;

		$child_order = $_POST['hidden-custom-child-order'];

		$childIDs = explode(",", $child_order);

		$child_result = count($childIDs);

		for($c = 0; $c < $child_result; $c++) {

			$child_str = str_replace("child_id_", "", $childIDs[$c]);
			
			$wpdb->query("UPDATE $wpdb->terms SET term_order = '$c' WHERE term_id ='$child_str'");

		}

		echo '<div id="message" class="updated fade"><p>'. __('Order updated successfully.', 'wpc').'</p></div>';

	} else {

		echo '<div id="message" class="error fade"><p>'. __('An error occured, order has not been saved.', 'wpc').'</p></div>';

	}

}

function customtaxorder_sub_query( $terms, $tax ) {

	$options = '';

	foreach ( $terms as $term ) :

		$subterms = get_term_children( $term->term_id, $tax );

		if ( $subterms ) { 

			$options .= '<option value="' . $term->term_id . '">' . $term->name . '</option>'; 

		}

	endforeach;

	return $options;

}

function customtaxorder_apply_order_filter($orderby, $args) {

	global $custom_tax;
	global $customtaxorder_settings;

	$options = $customtaxorder_settings;

	$taxonomy = $custom_tax;



	if ( $args['orderby'] == 'term_order' ) {

		return 't.term_order';

	} elseif ( $options[$taxonomy] == 1 && !isset($_GET['orderby']) ) {

		return 't.term_order';

	} else {

		return $orderby;

	}

}

add_filter('get_terms_orderby', 'customtaxorder_apply_order_filter', 10, 2);