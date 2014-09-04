<?php
//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

delete_option('custom_tax');
delete_option('dismiss-notice');
delete_option('wpc_pagination');
delete_option('wpc_image_height');
delete_option('wpc_image_width');
delete_option('wpc_thumb_height');
delete_option('wpc_thumb_width');
delete_option('wpc_image_scale_crop');
delete_option('wpc_thumb_scale_crop');
delete_option('wpc_croping');
delete_option('wpc_tcroping');
delete_option('wpc_next_prev');
delete_option('wpc_grid_rows');
delete_option('catalogue_page_url');

	global $wpdb;
$find_tax	=	$wpdb->get_results("SELECT term_taxonomy_id FROM wp_term_taxonomy WHERE taxonomy='wpccategories'",ARRAY_A );
$tax_id		=	$find_tax[0]['term_taxonomy_id'];
foreach($find_tax as $find_tax1){
	$tax_id	=	$find_tax1['term_taxonomy_id'];
	$delete		=	$wpdb->query("DELETE FROM wp_term_relationships WHERE term_taxonomy_id='$tax_id'");	
	$delete1	=	$wpdb->query("DELETE FROM wp_terms WHERE term_id='$tax_id'");
	$delete1	=	$wpdb->query("DELETE FROM wp_term_taxonomy WHERE term_taxonomy_id='$tax_id'");	
}
	
$allprods	=	new WP_Query('post_type=wpcproduct&posts_per_page=-1');
if($allprods){
while($allprods->have_posts()): $allprods->the_post();
$posid	=	get_the_id();
$args	=	array('post_parent' => $posid);
$post_attachments = get_children($args);
if($post_attachments) {
	foreach ($post_attachments as $attachment) {
        wp_delete_attachment($attachment->ID, true);
	}
 }
       wp_delete_post($posid, true);

endwhile;
}