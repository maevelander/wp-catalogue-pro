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

$all_product_label = ((!empty($all_product_label)) ? $all_product_label : __("All Products","wpc"));

if (is_single()) {
    $pname = '&gt;&gt;&nbsp;' . get_the_title();
    //$show_title = the_title();
}
?>

<div id="wpc-catalogue-wrapper">
    <?php
        if (get_option('wpc_show_bc') == yes) {
            if ($prslug == get_site_url() . '/?wpccategories=/') {
                $has_parent = "&gt;&gt;";
            } else {
                $has_parent = '&gt;&gt; <a href="' . $prslug . '">' . $prname . '</a> &gt;&gt;';
            }
    ?>
        <div class="wp-catalogue-breadcrumb">
            <a href="<?php echo $catalogue_page_url?>">
                <?php echo $all_product_label; ?>
            </a>
            <?php echo $has_parent; ?>
            <a href="<?php echo $cat_url; ?>">
                <?php echo $tname ?>
            </a>
            <?php echo $pname; ?>
        </div>
    <?php
        }
        
        if (get_option('wpc_sidebar') == yes) {
            global $wpdb;

            $args = array(
                'orderby' => 'term_order',
                'order' => 'ASC',
                'hide_empty' => true,
            );
            $terms = get_terms('wpccategories', $args);
            $count = count($terms);
    ?>
        <div id="wpc-col-1">
            <a class="wpc-visible-phone checking" href="#">Categories</a>
            
            <ul class="wpc-categories">
            <?php
                if ($count > 0) {
                    echo '<li class="wpc-category ' . $class . ' wpc_all_product_label"><a href="' . get_option('catalogue_page_url') . '">' . $all_product_label . '</a></li>';
                    echo '<ul class="wpc-categories">' . wp_list_categories($val) . '</ul>';
                } else {
                    echo '<li class="wpc-category"><a href="#">No category</a></li>';
                }
            ?>
            </ul>
        <?php
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
        ?>
        </div>
    <?php
        }
        
        if (get_option('wpc_sidebar') == yes) {
            $wpc_righ_content = 'wpc-col-2';
        } else {
            $wpc_righ_content = 'wpc-catalogue-wrapper';
        }
    ?>
    <div id="<?php echo $wpc_righ_content; ?>">
    <?php
        $wpc_path = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        if (strpos($wpc_path, '?s=')) {
            if (have_posts()) {
                while (have_posts()):
                    the_post();
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
    ?>
                    <!--wpc product-->
                    <div class="wpc-product">
                        <div class="wpc-img" style="width:<?php echo $new_size['width'].'px;'; ?> height:<?php echo $new_size['height'].'px;'; ?> overflow:hidden">
                            <a href="<?php echo $permalink; ?>" class="wpc-product-link">
                            <?php
                                foreach ($wpc_thumb_images as $field) {
                                    $wpc_thumb_img_path = $field['wpc_thumb_img'];
                            ?>
                                    <img src="<?php echo $wpc_thumb_img_path; ?>" alt="" />
                            <?php
                                }
                            ?>
                            </a>
                        </div>
                        <p class="wpc-title">
                            <a href="<?php echo $permalink; ?>"><?php echo $title; ?></a>
                        </p>
                    </div>
                    <!--/wpc-product-->
    <?php
                    if ($i == get_option('wpc_grid_rows')) {
                        echo '<br clear="all" />';
                        $i = 0; // reset counter
                    }
                    $i++;
                endwhile;
                wp_reset_postdata();
    ?>
    </div>
    <?php
            } else {
                echo __('No Products','wpc');
            }
        } else {
        if (have_posts()) :
            while (have_posts()) :
            the_post();
                $wpc_thumb_images = get_post_meta($post->ID, 'wpc_thumb_images', true);
                $wpc_big_images = get_post_meta($post->ID, 'wpc_big_images', true);

                $wpc_new_arr = array_values($wpc_big_images);
                $wpc_vert_horiz = get_option('wpc_vert_horiz');

                $wpc_vert_horiz_class = '';
                if ($wpc_vert_horiz == 'wpc_h') {
                    $wpc_vert_horiz_class = ' layout_hort';
                } elseif ($wpc_vert_horiz == 'wpc_v') {
                    $wpc_vert_horiz_class = ' layout_vert';
                }
    ?>
                <div id="wpc_my_carousel" class="wpc_my_carousel <?php echo $wpc_vert_horiz_class; ?>">
                    <div class="wpc_hero_img">
                        <img src="...">
                    </div>
                <?php
                    if ($wpc_vert_horiz == 'wpc_h') {
                        $wpc_vert_horiz_class = 'wpc_h_carousel_wrap';
                    } elseif($wpc_vert_horiz == 'wpc_v') {
                        $wpc_vert_horiz_class = 'wpc_v_carousel_wrap';
                    }
                ?>
                    <div class="<?php echo $wpc_vert_horiz_class; ?>">
                        <div class="wpc_carousel">
                            <ul>
                                <?php
                                $count = 0;
                                foreach ($wpc_thumb_images as $wpc_imgs) {
                                    ?>
                                    <li>
                                        <img src="<?php echo $wpc_imgs['wpc_thumb_img']; ?>" alt="" data-resize="<?php echo $wpc_new_arr[$count]['wpc_big_img']; ?>" />
                                    </li>
                                    <?php
                                    $count++;
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="wpc_product_content">
                    <article class="wpc_post">
                    <?php
                        $wpc_product_price = get_post_meta($post->ID, 'wpc_product_price', true);
                    ?>
                        <h4 class="wpc_catalogue_title">
                    <?php
                        if (get_option('wpc_show_title') == yes) {
                            the_title();
                        } else {
                            _e('Product Details', 'wpc');
                        }
                    ?>
                        </h4>
                    <?php
                        if ($wpc_product_price):
                    ?>
                        <h4 class="wpc_catalogue_price">
                            <span class="product-price">
                                <?php _e('Price:', 'wpc') ?>
                                <span>
                                    <?php echo $wpc_product_price; ?>
                                </span>
                            </span>
                        </h4>
                    <?php
                        endif;
                    ?>
                        <div class="entry-content"> 
                            <?php
                            the_content();
                            echo "<br />";
                            wpc_the_meta();

                            if (get_option('wpc_next_prev') == 1) {
                                echo '<p class="wpc-next-prev">';
                                    previous_post_link('%link', __('Previous',"wpc"), TRUE, ' ', 'wpccategories');
                                    next_post_link('%link', __('Next','wpc'), TRUE, ' ', 'wpccategories');
                                echo '</p>';
                            }
                            ?>
                        </div>
                    </article>
                </div>
    <?php
            endwhile;
        endif;
        }
    ?>
    </div>
</div>
<?php
    echo get_option('wpc_inn_temp_foot');
?>
<!--/Content-->
<?php
get_footer();
?>  