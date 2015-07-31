<?php get_header(); ?>

<!--Content-->

<?php echo get_option('wpc_inn_temp_head'); ?>



<?php
$catalogue_page_url = get_option('catalogue_page_url');
$terms = get_terms('wpccategories');

global $post;
$terms1 = get_the_terms($post->post_parent, 'wpccategories');

if ($terms1) {
    foreach ($terms1 as $term1) {
        $slug = $term1->slug;
        $tname = $term1->name;
        $pId = $term1->parent;
        $cat_url = get_site_url() . '/?wpccategories=/' . $slug;
    };
}

$pterm = get_term_by('id', $pId, 'wpccategories');

$prname = $pterm->name;
$prslug = get_site_url() . '/?wpccategories=/' . $pterm->slug;


//list terms in a given taxonomy using wp_list_categories  (also useful as a widget)
$orderby = 'term_order';
$show_count = 0; // 1 for yes, 0 for no
$pad_counts = 0; // 1 for yes, 0 for no
$hierarchical = 1; // 1 for yes, 0 for no

$taxonomy = 'wpccategories';
$title = '';

$val = array(
    'orderby' => $orderby,
    'show_count' => $show_count,
    'pad_counts' => $pad_counts,
    'hierarchical' => $hierarchical,
    'taxonomy' => $taxonomy,
    'title_li' => $title,
);

$all_product_label = get_option('wpc_all_product_label');

$all_product_label = ((!empty($all_product_label)) ? $all_product_label : "All Products");

if (is_single()) {
    $pname = '&gt;&gt;&nbsp;' . get_the_title();
    //$show_title = the_title();
}
/* ========================= on/off breadcrumbs ======================== */
if (get_option('wpc_show_bc') == yes) {
    if ($prslug == get_site_url() . '/?wpccategories=/') {
        $has_parent = "&gt;&gt;";
    } else {
        $has_parent = '&gt;&gt; <a href="' . $prslug . '">' . $prname . '</a> &gt;&gt;';
    }

    echo '<div class="wp-catalogue-breadcrumb"> <a href="' . $catalogue_page_url . '">' . $all_product_label . '</a> ' . $has_parent . ' <a href="' . $cat_url . '">' . $tname . '</a> ' . $pname . '</div>';
}
/* ========================= on/off breadcrumbs CLOSING ======================== */
?>

<div id="wpc-catalogue-wrapper">
    <?php
    global $post;
    $terms1 = get_the_terms($post->id, 'wpccategories');

    if ($terms1 != null || $term1 != null) {
        foreach ($terms1 as $term1) {
            $slug = $term1->slug;
            $term_id = $term1->term_id;
        };
    }
    global $wpdb;

    $args = array(
        'orderby' => 'term_order',
        'order' => 'ASC',
        'hide_empty' => true,
    );
    $terms = get_terms('wpccategories', $args);
    $count = count($terms);

    /* ======================= sidebar on/off ====================== */
    if (get_option('wpc_sidebar') == yes) {
        echo '<div id="wpc-col-1">';
        echo '<a class="wpc-visible-phone checking" href="#">Categories</a>';
        echo '<ul class="wpc-categories">';

        // generating sidebar
        if ($count > 0) {
            echo '<li class="wpc-category ' . $class . ' wpc_all_product_label"><a href="' . get_option('catalogue_page_url') . '">' . $all_product_label . '</a></li>';
            echo '<ul class="wpc-categories">' . wp_list_categories($val) . '</ul>';
        } else {
            echo '<li class="wpc-category"><a href="#">No category</a></li>';
        }

        echo '</ul>';
        if (get_option('wpc_show_tags') == 'on') {
            ?>
            <div class="wpc_sidebar_tags">
                <h2>Catalogue Tags</h2>
                <?php
                $wpc_tags_args = array(
                    'smallest' => 12,
                    'largest' => 30,
                    'unit' => 'px',
                    'number' => 18,
                    'format' => 'flat',
                    'separator' => "\n",
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'exclude' => null,
                    'include' => null,
                    'topic_count_text_callback' => default_topic_count_text,
                    'link' => 'view',
                    'taxonomy' => 'wpctags',
                    'echo' => true
                );

                wp_tag_cloud($wpc_tags_args);

                wp_reset_query();
                ?>
            </div>
            <?php
        }
        echo ' </div>';
    }
    /* ======================= sidebar on/off CLOSING ====================== */

    $wpc_path = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    if (strpos($wpc_path, '?s=')) {

        if (have_posts()) {

            if (get_option('wpc_sidebar') == yes) {
                echo '  <!--col-2-->

					<div id="wpc-col-2">
					<div id="wpc-products">';
            } else {
                echo '  <!--col-2-->

					<div id="wpc-catalogue-wrapper">
					<div id="wpc-products">';
            }
            while (have_posts()): the_post();
                $wpc_thumb_images = get_post_meta($post->ID, 'wpc_thumb_images', true);
                $wpc_thumb_width = get_option('wpc_thumb_width');

                foreach ($wpc_thumb_images as $field_resize) {
                    $resize_img = wp_get_image_editor($field_resize['wpc_thumb_img']);

                    if (!is_wp_error($resize_img)) {
                        $wpc_resize = $resize_img->resize($wpc_thumb_width, NULL, false);
                        if ($wpc_resize !== FALSE) {
                            $new_size = $resize_img->get_size();
                        }
                    }
                }

                $title = get_the_title();
                $permalink = get_permalink();
                $price = get_post_meta(get_the_id(), 'product_price', true);

                echo '<!--wpc product-->';
                echo '<div class="wpc-product">';
                echo '<div class="wpc-img" style="width:' . $new_size['width'] . 'px; height:' . $new_size['height'] . 'px; overflow:hidden"><a href="' . $permalink . '" class="wpc-product-link">';
                foreach ($wpc_thumb_images as $field) {
                    $wpc_thumb_img_path = $field['wpc_thumb_img'];
                    echo '<img src="' . $wpc_thumb_img_path . '" alt="" />';
                }
                echo '</a></div>';
                echo '<p class="wpc-title"><a href="' . $permalink . '">' . $title . '</a></p>';
                echo '</div>';
                echo '<!--/wpc-product-->';

                if ($i == get_option('wpc_grid_rows')) {
                    echo '<br clear="all" />';
                    $i = 0; // reset counter
                }
                $i++;
            endwhile;
            wp_reset_postdata();
            echo '</div>';
        } else {
            echo 'No Products';
        }
    } else {
        ?>
        <!--/Left-menu-->
        <!--col-2-->
        <?php
        $i = 1;
        if (get_option('wpc_sidebar') == yes) {
            ?>
            <div id="wpc-col-2">
                <?php
            } else {
                ?>
                <div id="wpc-catalogue-wrapper">
                    <?php
                }

                if (have_posts()) :
                    while (have_posts()) : the_post();
                        $wpc_thumb_images = get_post_meta($post->ID, 'wpc_thumb_images', true);
                        $wpc_big_images = get_post_meta($post->ID, 'wpc_big_images', true);

                        $wpc_vert_horiz = get_option('wpc_vert_horiz');

                        $wpc_vert_horiz_class = '';
                        if ($wpc_vert_horiz == 'wpc_h') {
                            $wpc_vert_horiz_class = ' layout_hort';
                        } elseif ($wpc_vert_horiz == 'wpc_v') {
                            $wpc_vert_horiz_class = ' layout_vert';
                        }
                        ?>
                        <div id="wpc_my_carousel" class="wpc_my_carousel<?php echo $wpc_vert_horiz_class; ?>">
                            <div class="wpc_hero_img">
                                <img src="...">
                            </div>

                            <div class="wpc_carousel">
                                <ul>
                                    <?php
                                    $count = 0;
                                    foreach ($wpc_thumb_images as $wpc_imgs) {
                                        ?>
                                        <li>
                                            <img src="<?php echo $wpc_imgs['wpc_thumb_img']; ?>" alt="" data-resize="<?php echo $wpc_big_images[$count]['wpc_big_img']; ?>" />
                                        </li>
                                        <?php
                                        $count++;
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <?php
                        $wpc_product_price = get_post_meta($post->ID, 'wpc_product_price', true);
                        if (get_option('wpc_show_title') == yes) {
                            ?>
                            <h4>
                                <?php
                                the_title();
                            } else {
                                ?>
                                <h4>
                                    <?php _e('Product Details', 'wpc'); ?>
                                    <?php
                                }
                                if ($wpc_product_price):
                                    ?>
                                    <span class="product-price"><?php _e('Price:', 'wpc') ?> <span><?php echo $wpc_product_price; ?></span></span>
                                    <?php
                                endif;
                                ?>
                            </h4>
                            <article class="post">
                                <div class="entry-content"> 
                                    <?php
                                    the_content();
                                    echo "<br />";
                                    wpc_the_meta();

                                    if (get_option('wpc_next_prev') == 1) {
                                        echo '<p class="wpc-next-prev">';
                                        previous_post_link('%link', 'Previous', TRUE, ' ', 'wpccategories');
                                        next_post_link('%link', 'Next', TRUE, ' ', 'wpccategories');
                                        echo '</p>';
                                    }
                                    ?>
                                </div>
                            </article>
                            <?php
                        endwhile;
//                        global $wpdb;
//                        $wpc_product_images_sql = "Select wpc_posts.ID, wpc_meta.*
//                                    From " . $wpdb->posts . " As wpc_posts
//                                Inner Join " . $wpdb->postmeta . " As wpc_meta
//                                On wpc_posts.ID = wpc_meta.post_id
//                                Where wpc_meta.meta_key = 'product_images'";
//                        $wpc_images_qry = $wpdb->get_results($wpc_product_images_sql);
//
//                        $upload_dir = wp_upload_dir();
//                        $wpc_image_width = get_option('wpc_image_width');
//                        $wpc_image_height = get_option('wpc_image_height');
//                        $wpc_thumb_width = get_option('wpc_thumb_width');
//                        $wpc_thumb_height = get_option('wpc_thumb_height');
//                        
//                        
//                        foreach ($wpc_images_qry as $wpc_prod_images) {
//                            $wpc_post_id = $wpc_prod_images->ID;
//                            $wpc_product_images = get_post_meta($wpc_post_id, 'product_images', true);
//                            
//                            echo $wpc_post_id;
//                            
//                            $big_img_name = array();
//                            $thumb_img_name = array();
//                            foreach($wpc_product_images as $img) {
//                                $product_big_img = $img['product_img'];
//
//                                $product_img_explode = explode('/', $product_big_img);
//                                $product_img_name = end($product_img_explode);
//                                $product_img_name_explode = explode('.', $product_img_name);
//
//                                $product_img_name = $product_img_name_explode[0];
//                                $product_img_ext = $product_img_name_explode[1];
//                                
//                                $big_img_name[]['product_big_img'] = $upload_dir['url'].'/'.$product_img_name . '-big-' . $wpc_image_width . 'x' . $wpc_image_height . '-NEW.' . $product_img_ext;
//                                $thumb_img_name[]['product_thumb_img'] = $upload_dir['url'].'/'.$product_img_name . '-big-' . $wpc_thumb_width . 'x' . $wpc_thumb_height . '-NEW.' . $product_img_ext;
//                               
//                            }
//                            echo "<div style='border:red 5px solid; margin: 0 0 30px;'><pre>";
//                            print_r($wpc_product_images);
//                            echo '</pre><hr>';
//                            echo '<pre>';
//                            print_r($big_img_name);
//                            echo '</pre><hr>';
//                            echo "<pre>";
//                                print_r($thumb_img_name);
//                            echo "</pre>";
//                            echo '</div>';
//                        }
                    endif;
                    ?>
            </div>
            <!--/col-2-->
            <div class="clear"></div>    
        </div>
    <?php
}
echo get_option('wpc_inn_temp_foot');
?>
    <!--/Content-->
    <?php
    get_footer();
    